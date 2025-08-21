<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Midtrans transaction
     */
    public function createTransaction(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'table_number' => 'required|integer|min:1'
            ]);

            $order = Order::with('orderItems.product', 'user')->findOrFail($request->order_id);

            // Check if user owns this order
            if ($order->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Check if order is in correct status
            if ($order->status !== 'process') {
                return response()->json(['error' => 'Order tidak dapat diproses untuk pembayaran'], 400);
            }

            // Check if order has items
            if ($order->orderItems->isEmpty()) {
                return response()->json(['error' => 'Order tidak memiliki item'], 400);
            }

            // Update table number
            $order->update(['table_number' => $request->table_number]);

            // Generate unique transaction ID
            $transactionId = 'ORDER-' . $order->id . '-' . time() . '-' . rand(1000, 9999);

            DB::beginTransaction();
            try {
                // Create payment record
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => $transactionId,
                    'gross_amount' => $order->total_price,
                    'payment_status' => 'pending',
                    'transaction_status' => 'pending',
                ]);

                // Prepare items for Midtrans
                $items = [];
                foreach ($order->orderItems as $item) {
                    $items[] = [
                        'id' => (string)$item->product->id,
                        'price' => (int)$item->product->price,
                        'quantity' => (int)$item->quantity,
                        'name' => substr($item->product->name, 0, 50), // Midtrans has character limit
                    ];
                }

                // Prepare transaction details for Midtrans
                $params = [
                    'transaction_details' => [
                        'order_id' => $transactionId,
                        'gross_amount' => (int)$order->total_price,
                    ],
                    'item_details' => $items,
                    'customer_details' => [
                        'first_name' => $order->user->username,
                        'last_name' => '',
                        'email' => $order->user->username . '@selforder.com',
                        'phone' => '08123456789',
                    ],
                    'callbacks' => [
                        'finish' => url('/cart'),
                    ],
                    'expiry' => [
                        'start_time' => date('Y-m-d H:i:s O'),
                        'unit' => 'minutes',
                        'duration' => 30
                    ]
                ];

                // Get Snap Token from Midtrans
                $snapToken = Snap::getSnapToken($params);

                DB::commit();

                Log::info('Payment transaction created', [
                    'transaction_id' => $transactionId,
                    'order_id' => $order->id,
                    'amount' => $order->total_price,
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'snap_token' => $snapToken,
                    'transaction_id' => $transactionId,
                    'redirect_url' => url('/cart')
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Payment creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat membuat transaksi pembayaran'
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification callback
     */
    public function callback(Request $request)
    {
        try {
            $notification = new Notification();

            // Extract notification data
            $transactionStatus = $notification->transaction_status;
            $transactionId = $notification->order_id;
            $fraudStatus = $notification->fraud_status ?? null;
            $statusCode = $notification->status_code ?? null;
            $statusMessage = $notification->status_message ?? null;
            $signatureKey = $notification->signature_key ?? null;
            $paymentType = $notification->payment_type ?? null;
            $grossAmount = $notification->gross_amount ?? null;
            $transactionTime = $notification->transaction_time ?? null;

            Log::info('Midtrans notification received', [
                'transaction_id' => $transactionId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'status_code' => $statusCode,
                'payment_type' => $paymentType,
                'gross_amount' => $grossAmount
            ]);

            // Find payment by transaction_id
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                Log::error('Payment not found', ['transaction_id' => $transactionId]);
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Payment record not found'
                ], 404);
            }

            $order = $payment->order;

            DB::beginTransaction();
            try {
                // Update payment record dengan data dari Midtrans
                $updateData = [
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus,
                    'status_code' => $statusCode,
                    'status_message' => $statusMessage,
                    'signature_key' => $signatureKey,
                    'payment_type' => $paymentType,
                    'midtrans_response' => $notification->getResponse(),
                ];

                if ($transactionTime) {
                    $updateData['transaction_time'] = date('Y-m-d H:i:s', strtotime($transactionTime));
                }

                // Handle different transaction statuses sesuai dokumentasi Midtrans
                switch ($transactionStatus) {
                    case 'capture':
                        if ($fraudStatus == 'challenge') {
                            // Credit card transaction is challenged by FDS
                            $updateData['payment_status'] = 'challenge';
                            $order->update(['status' => 'pending']);
                            Log::info('Payment challenged by fraud detection', ['transaction_id' => $transactionId]);
                        } else if ($fraudStatus == 'accept') {
                            // Credit card transaction is captured and not challenged by FDS
                            $updateData['payment_status'] = 'paid';
                            $order->update(['status' => 'paid']);
                            Log::info('Payment captured successfully', ['transaction_id' => $transactionId]);
                        }
                        break;

                    case 'settlement':
                        // Transaction is successfully settled
                        $updateData['payment_status'] = 'paid';
                        $order->update(['status' => 'paid']);
                        Log::info('Payment settled successfully', ['transaction_id' => $transactionId]);
                        break;

                    case 'pending':
                        // Transaction is created and waiting to be paid
                        $updateData['payment_status'] = 'pending';
                        $order->update(['status' => 'pending']);
                        Log::info('Payment is pending', ['transaction_id' => $transactionId]);
                        break;

                    case 'deny':
                        // Payment was denied by bank or FDS
                        $updateData['payment_status'] = 'failed';
                        $order->update(['status' => 'cancelled']);
                        Log::info('Payment denied', ['transaction_id' => $transactionId]);
                        break;

                    case 'expire':
                        // Payment was not completed within allowed time
                        $updateData['payment_status'] = 'failed';
                        $order->update(['status' => 'cancelled']);
                        Log::info('Payment expired', ['transaction_id' => $transactionId]);
                        break;

                    case 'cancel':
                        // Payment was cancelled by customer
                        $updateData['payment_status'] = 'failed';
                        $order->update(['status' => 'cancelled']);
                        Log::info('Payment cancelled', ['transaction_id' => $transactionId]);
                        break;

                    case 'failure':
                        // Payment failed to process
                        $updateData['payment_status'] = 'failed';
                        $order->update(['status' => 'cancelled']);
                        Log::error('Payment failed', ['transaction_id' => $transactionId]);
                        break;

                    default:
                        Log::warning('Unknown transaction status', [
                            'transaction_id' => $transactionId,
                            'status' => $transactionStatus
                        ]);
                        break;
                }

                $payment->update($updateData);
                DB::commit();

                Log::info('Payment notification processed successfully', [
                    'transaction_id' => $transactionId,
                    'final_status' => $updateData['payment_status']
                ]);

                return response()->json(['status' => 'success']);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_body' => $request->getContent()
            ]);
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Failed to process payment notification'
            ], 500);
        }
    }

    /**
     * Check payment status (optional, untuk frontend polling)
     */
    public function checkStatus($transactionId)
    {
        try {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            
            if (!$payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            return response()->json([
                'transaction_id' => $payment->transaction_id,
                'payment_status' => $payment->payment_status,
                'transaction_status' => $payment->transaction_status,
                'order_status' => $payment->order->status,
                'is_paid' => $payment->isPaid(),
                'is_pending' => $payment->isPending(),
                'is_failed' => $payment->isFailed(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment status check error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);
            
            return response()->json(['error' => 'Failed to check payment status'], 500);
        }
    }
}