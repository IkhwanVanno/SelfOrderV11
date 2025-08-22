<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AsidebarController extends Controller
{
    // Order Management
    public function order()
    {
        $orders = Order::with(['user', 'orderItems.product', 'payment'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('admin.order', compact('orders'));
    }

    // Update Order Status
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:queue,process,ready,delivered'
            ]);

            $order = Order::findOrFail($id);
            $oldStatus = $order->status;
            
            $order->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Order status berhasil diubah dari ' . ucfirst($oldStatus) . ' ke ' . ucfirst($request->status),
                'new_status' => $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Payment Management
    public function payment()
    {
        $payments = Payment::with(['order.user', 'order.orderItems.product'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('admin.payment', compact('payments'));
    }

    // Payment Details (for modal)
    public function paymentDetails($id)
    {
        try {
            $payment = Payment::with(['order.user', 'order.orderItems.product'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'order_id' => $payment->order_id,
                    'gross_amount' => $payment->gross_amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'payment_type' => $payment->payment_type,
                    'payment_status' => $payment->payment_status,
                    'transaction_status' => $payment->transaction_status,
                    'fraud_status' => $payment->fraud_status,
                    'status_code' => $payment->status_code,
                    'status_message' => $payment->status_message,
                    'status_color' => $payment->status_color,
                    'created_at' => $payment->created_at->format('d/m/Y H:i:s'),
                    'transaction_time' => $payment->transaction_time ? $payment->transaction_time->format('d/m/Y H:i:s') : null,
                    'customer_name' => $payment->order->user->username ?? 'N/A',
                    'table_number' => $payment->order->table_number,
                    'order_items_count' => $payment->order->orderItems->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
    }

    // Product Management
    public function product()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.product', compact('products'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan']);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json(['success' => true, 'message' => 'Produk berhasil diupdate']);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus']);
    }

    // User Management
    public function user()
    {
        $users = User::orderBy('username')->get();
        return view('admin.user', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        User::create([ 
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan']);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        $data = [
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'User berhasil diupdate']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User berhasil dihapus']);
    }
}