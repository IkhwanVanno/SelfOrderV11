@extends('layouts.app')

@section('content')
    <section class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($products as $product)
            @include('components.card', ['product' => $product])
        @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">Tidak ada produk makanan yang tersedia.</p>
            </div>
        @endforelse
    </section>

    <script>
        // Initialize quantities dengan data dari database
        let quantities = {!! json_encode($cartItems) !!};

        // Update tampilan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            Object.keys(quantities).forEach(productId => {
                updateQuantityDisplay(productId);
            });
        });

        function increaseQuantity(productId) {
            if (!quantities[productId]) {
                quantities[productId] = 0;
            }
            quantities[productId]++;
            updateQuantityDisplay(productId);
            addToCart(productId, 1);
        }

        function decreaseQuantity(productId) {
            if (quantities[productId] && quantities[productId] > 0) {
                quantities[productId]--;
                updateQuantityDisplay(productId);
                
                if (quantities[productId] === 0) {
                    removeFromCart(productId);
                } else {
                    updateCartItem(productId, quantities[productId]);
                }
            }
        }

        function updateQuantityDisplay(productId) {
            const quantityElement = document.getElementById('quantity-' + productId);
            if (quantityElement) {
                quantityElement.textContent = quantities[productId] || 0;
            }
        }

        function addToCart(productId, quantity) {
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    table_number: '1'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Item berhasil ditambahkan ke keranjang');
                } else {
                    console.error('Gagal menambahkan item ke keranjang');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function updateCartItem(productId, quantity) {
            fetch(`/cart/update-product/${productId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Cart berhasil diupdate');
                } else {
                    console.error('Gagal mengupdate cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function removeFromCart(productId) {
            fetch(`/cart/remove-product/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Item berhasil dihapus dari keranjang');
                } else {
                    console.error('Gagal menghapus item dari keranjang');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
@endsection