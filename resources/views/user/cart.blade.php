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

            <!-- Table Number Input -->
            <div class="mt-6 mb-4">
                <label for="table_number" class="block text-sm font-medium mb-2">Nomor Meja:</label>
                <input type="number" id="table_number" min="1" value="{{ $activeOrder->table_number }}" 
                       class="w-full max-w-xs px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       placeholder="Masukkan nomor meja" required>
            </div>

            <!-- Payment Status (if exists) -->
            @if($activeOrder->payment)
                <div class="mt-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold mb-2">Status Pembayaran:</h3>
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if($activeOrder->payment->payment_status === 'paid') 
                                bg-green-100 text-green-800
                            @elseif($activeOrder->payment->payment_status === 'pending') 
                                bg-yellow-100 text-yellow-800
                            @elseif($activeOrder->payment->payment_status === 'challenge') 
                                bg-orange-100 text-orange-800
                            @else 
                                bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($activeOrder->payment->payment_status) }}
                        </span>
                        <span class="text-sm text-gray-600">
                            Transaction ID: {{ $activeOrder->payment->transaction_id }}
                        </span>
                    </div>
                    @if($activeOrder->payment->isPaid())
                        <div class="mt-2 text-sm text-green-600">
                            ✓ Pembayaran berhasil! Pesanan sedang diproses.
                        </div>
                    @elseif($activeOrder->payment->isPending())
                        <div class="mt-2 text-sm text-yellow-600">
                            ⏳ Menunggu pembayaran. Silakan selesaikan pembayaran Anda.
                        </div>
                    @elseif($activeOrder->payment->isFailed())
                        <div class="mt-2 text-sm text-red-600">
                            ❌ Pembayaran gagal. Silakan coba lagi.
                        </div>
                    @endif
                </div>
            @endif

            <!-- Total dan Button -->
            <div class="flex justify-between items-center border-t mt-6 pt-4">
                <div class="text-lg font-medium">Total :</div>
                <div class="flex items-center space-x-4">
                    <div class="text-lg font-bold" id="total-price">RP : {{ number_format($activeOrder->total_price, 0, ',', '.') }}</div>
                    
                    @if(!$activeOrder->payment || $activeOrder->payment->isFailed())
                        <button type="button" id="pay-button" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
                            Bayar Sekarang
                        </button>
                    @elseif($activeOrder->payment->isPending())
                        <button type="button" id="pay-button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Lanjutkan Pembayaran
                        </button>
                    @elseif($activeOrder->payment->isPaid())
                        <button type="button" class="px-4 py-2 bg-green-600 text-white rounded cursor-not-allowed" disabled>
                            Sudah Dibayar
                        </button>
                    @endif
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

    <!-- Midtrans Snap JS -->
    @if(config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // Function to show loading state
        function showLoading(button, text = 'Processing...') {
            button.disabled = true;
            button.textContent = text;
            button.classList.add('opacity-75', 'cursor-not-allowed');
        }

        // Function to hide loading state
        function hideLoading(button, text = 'Bayar Sekarang') {
            button.disabled = false;
            button.textContent = text;
            button.classList.remove('opacity-75', 'cursor-not-allowed');
        }

        // Function to show alert
        function showAlert(message, type = 'info') {
            const alertClass = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 
                             type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                             'bg-blue-100 border-blue-400 text-blue-700';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `${alertClass} px-4 py-3 rounded mb-4 fixed top-4 right-4 z-50 min-w-80`;
            alertDiv.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="float-right ml-4 text-lg">&times;</button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }

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
                    location.reload(); // Reload to update total
                } else {
                    showAlert('Gagal mengupdate quantity', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', 'error');
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
                    location.reload();
                } else {
                    showAlert('Gagal menghapus item', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan sistem', 'error');
            });
        }

        // Payment function
        @if($activeOrder && $activeOrder->orderItems->count() > 0)
        const payButton = document.getElementById('pay-button');
        if (payButton && !payButton.disabled) {
            payButton.onclick = function () {
                const tableNumber = document.getElementById('table_number').value;
                
                if (!tableNumber || tableNumber < 1) {
                    showAlert('Mohon masukkan nomor meja yang valid', 'error');
                    document.getElementById('table_number').focus();
                    return;
                }

                showLoading(payButton, 'Membuat transaksi...');

                fetch('/payment/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        order_id: {{ $activeOrder->id }},
                        table_number: parseInt(tableNumber)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showAlert(data.error, 'error');
                        hideLoading(payButton);
                        return;
                    }

                    // Open Midtrans Snap popup
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            showAlert('Pembayaran berhasil! Pesanan sedang diproses.', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            showAlert('Pembayaran sedang diproses. Mohon tunggu konfirmasi.', 'info');
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        },
                        onError: function(result) {
                            console.error('Payment error:', result);
                            showAlert('Pembayaran gagal! Silakan coba lagi.', 'error');
                            hideLoading(payButton);
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                            hideLoading(payButton);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Terjadi kesalahan saat memproses pembayaran', 'error');
                    hideLoading(payButton);
                });
            };
        }
        @endif

        // Auto refresh payment status setiap 30 detik untuk pending payments
        @if($activeOrder && $activeOrder->payment && $activeOrder->payment->isPending())
        setInterval(function() {
            fetch(`/payment/status/{{ $activeOrder->payment->transaction_id }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.is_paid) {
                        showAlert('Pembayaran berhasil dikonfirmasi!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else if (data.is_failed) {
                        showAlert('Pembayaran gagal atau expired', 'error');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.log('Status check error:', error);
                });
        }, 30000); // Check every 30 seconds
        @endif
    </script>
@endsection