@extends('layouts.app')

@section('content')
    <section class="px-4 py-6">
        <h1 class="text-xl font-bold mb-4">Shopping Cart</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($activeOrder && $activeOrder->orderItems->count() > 0)
            <!-- Cart Header -->
            <div class="grid grid-cols-3 gap-4 border-b pb-2 font-semibold">
                <div>Order</div>
                <div>Price</div>
                <div class="text-center">Quantity</div>
            </div>

            <!-- Cart Items -->
            <div class="space-y-4 mt-4">
                @foreach($activeOrder->orderItems as $item)
                    <div class="grid grid-cols-3 items-center gap-4" id="item-{{ $item->id }}">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gray-300">
                                <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('images/ProductSample.jpg') }}" 
                                     alt="{{ $item->product->name }}" class="w-full h-full object-cover" />
                            </div>
                            <div>{{ $item->product->name }}</div>
                        </div>
                        <div>RP : {{ number_format($item->product->price, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-center space-x-4">
                            <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                    class="text-xl font-bold hover:bg-gray-200 w-8 h-8 rounded">−</button>
                            <span id="quantity-{{ $item->id }}">{{ $item->quantity }}</span>
                            <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                    class="text-xl font-bold hover:bg-gray-200 w-8 h-8 rounded">+</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total dan Button -->
            <div class="flex justify-between items-center border-t mt-6 pt-4">
                <div class="text-lg font-medium">Total :</div>
                <div class="flex items-center space-x-4">
                    <div class="text-lg font-bold" id="total-price">RP : {{ number_format($activeOrder->total_price, 0, ',', '.') }}</div>
                    <form method="" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">Checkout</button>
                    </form>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">Keranjang Anda kosong</p>
                <a href="{{ route('food') }}" class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </section>

    <script>
        function updateQuantity(itemId, newQuantity) {
            if (newQuantity <= 0) {
                removeItem(itemId);
                return;
            }

            fetch(`/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`quantity-${itemId}`).textContent = newQuantity;
                    // Refresh halaman untuk update total
                    location.reload();
                } else {
                    console.error('Gagal mengupdate quantity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function removeItem(itemId) {
            fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh halaman untuk update tampilan
                    location.reload();
                } else {
                    console.error('Gagal menghapus item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
@endsection