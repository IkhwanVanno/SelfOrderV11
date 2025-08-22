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
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        
        if ($hashed == $request->signature_key) {
            $payment = Payment::where('transaction_id', $request->order_id)->first();
            
            if ($payment) {
                $updateData = [
                    'payment_type' => $request->payment_type,
                    'transaction_status' => $request->transaction_status,
                    'fraud_status' => $request->fraud_status,
                    'status_code' => $request->status_code,
                    'status_message' => $request->status_message,
                    'signature_key' => $request->signature_key,
                    'midtrans_response' => $request->all(),
                ];
                
                if ($request->transaction_time) {
                    $updateData['transaction_time'] = $request->transaction_time;
                }
                
                // Handle different transaction statuses
                switch ($request->transaction_status) {
                    case 'capture':
                        if ($request->fraud_status == 'accept') {
                            $updateData['payment_status'] = 'paid';
                            $payment->order->update(['status' => 'queue']);
                        } else if ($request->fraud_status == 'challenge') {
                            $updateData['payment_status'] = 'challenge';
                        }
                        break;
                        
                    case 'settlement':
                        $updateData['payment_status'] = 'paid';
                        $payment->order->update(['status' => 'queue']);
                        break;
                        
                    case 'pending':
                        $updateData['payment_status'] = 'pending';
                        break;
                        
                    case 'deny':
                    case 'cancel':
                    case 'expire':
                    case 'failure':
                        $updateData['payment_status'] = 'failed';
                        break;
                }
                
                $payment->update($updateData);
            }
        }
        
        return response('OK', 200);
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