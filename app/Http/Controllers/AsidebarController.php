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

    // Payment Management
    public function payment()
    {
        $payments = Payment::with(['order.user', 'order.orderItems.product'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('admin.payment', compact('payments'));
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