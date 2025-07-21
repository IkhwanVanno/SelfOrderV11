<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsidebarController extends Controller
{
    public function food()
    {
        $products = Product::active()->byCategory('food')->get();
        $cartItems = $this->getCartItems();
        return view('user.food', compact('products', 'cartItems'));
    }

    public function drink()
    {
        $products = Product::active()->byCategory('drink')->get();
        $cartItems = $this->getCartItems();
        return view('user.drink', compact('products', 'cartItems'));
    }

    public function snack()
    {
        $products = Product::active()->byCategory('snack')->get();
        $cartItems = $this->getCartItems();
        return view('user.snack', compact('products', 'cartItems'));
    }

        private function getCartItems()
    {
        $user = Auth::user();
        $activeOrder = Order::where('user_id', $user->id)
            ->where('status', 'process')
            ->with('orderItems.product')
            ->first();

        $cartItems = [];
        if ($activeOrder) {
            foreach ($activeOrder->orderItems as $item) {
                $cartItems[$item->product_id] = $item->quantity;
            }
        }

        return $cartItems;
    }

    public function cart()
    {
        $user = Auth::user();
        $activeOrder = Order::where('user_id', $user->id)
            ->where('status', 'process')
            ->with('orderItems.product')
            ->first();

        return view('user.cart', compact('activeOrder'));
    }

    public function addToCart(Request $request)
    {
        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Find or create active order
        $order = Order::firstOrCreate([
            'user_id' => $user->id,
            'status' => 'process',
        ], [
            'table_number' => $request->table_number ?? null, // Biarkan null untuk diisi saat checkout
            'total_price' => 0,
        ]);

        // Check if item already exists in cart
        $orderItem = OrderItem::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        if ($orderItem) {
            $orderItem->quantity += $request->quantity;
            $orderItem->subtotal = $orderItem->quantity * $product->price;
            $orderItem->save();
        } else {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'subtotal' => $request->quantity * $product->price,
            ]);
        }

        // Update order total
        $order->total_price = $order->calculateTotal();
        $order->save();

        return response()->json(['success' => true, 'message' => 'Item berhasil ditambahkan ke keranjang']);
    }

    public function updateCart(Request $request, $itemId)
    {
        $orderItem = OrderItem::findOrFail($itemId);
        $orderItem->quantity = $request->quantity;
        $orderItem->subtotal = $orderItem->quantity * $orderItem->product->price;
        $orderItem->save();

        // Update order total
        $order = $orderItem->order;
        $order->total_price = $order->calculateTotal();
        $order->save();

        return response()->json(['success' => true, 'message' => 'Keranjang berhasil diupdate']);
    }

    public function removeFromCart($itemId)
    {
        $orderItem = OrderItem::findOrFail($itemId);
        $order = $orderItem->order;
        $orderItem->delete();

        // Update order total
        $order->total_price = $order->calculateTotal();
        $order->save();

        return response()->json(['success' => true, 'message' => 'Item berhasil dihapus dari keranjang']);
    }

    public function updateCartByProduct(Request $request, $productId)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('status', 'process')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan']);
        }

        $orderItem = OrderItem::where('order_id', $order->id)
            ->where('product_id', $productId)
            ->first();

        if (!$orderItem) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan']);
        }

        $newQuantity = $request->quantity;
        
        if ($newQuantity <= 0) {
            $orderItem->delete();
        } else {
            $orderItem->quantity = $newQuantity;
            $orderItem->subtotal = $orderItem->quantity * $orderItem->product->price;
            $orderItem->save();
        }

        // Update order total
        $order->total_price = $order->calculateTotal();
        $order->save();

        return response()->json(['success' => true, 'message' => 'Cart berhasil diupdate']);
    }

    public function removeFromCartByProduct($productId)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->where('status', 'process')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan']);
        }

        $orderItem = OrderItem::where('order_id', $order->id)
            ->where('product_id', $productId)
            ->first();

        if ($orderItem) {
            $orderItem->delete();
            
            // Update order total
            $order->total_price = $order->calculateTotal();
            $order->save();
        }

        return response()->json(['success' => true, 'message' => 'Item berhasil dihapus']);
    }
}