@extends('layouts.app')

@section('content')
    <section class="px-4 py-6">
        <h1 class="text-xl font-bold mb-4">Payment Options</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Order Summary -->
        <div class="bg-white border rounded-lg p-4 mb-6">
            <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
            <div class="space-y-2">
                @foreach($order->orderItems as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                        <span>RP {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between font-bold text-lg">
                    <span>Total:</span>
                    <span>RP {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Table Number Form -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m5-13v13m7-13v13M4 13h16"></path>
                </svg>
                Table Information
            </h3>
            <p class="text-sm text-gray-600 mb-4">Please enter your table number so we can deliver your order to the correct location.</p>
            <div class="flex items-center space-x-2">
                <label for="table_number" class="text-sm font-medium">Table Number:</label>
                <input type="text" 
                       id="table_number" 
                       name="table_number" 
                       value="{{ $order->table_number }}"
                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-20"
                       placeholder="1">
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Cash Payment -->
            <div class="bg-white border rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Cash Payment
                </h3>
                <p class="text-gray-600 mb-4">Pay with cash at the counter</p>
                
                <form action="{{ route('payment.cash') }}" method="POST" id="cash-form">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="table_number" id="cash-table-number">
                    
                    <div class="mb-4">
                        <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-2">
                            Amount Paid
                        </label>
                        <input type="number" 
                               id="amount_paid" 
                               name="amount_paid" 
                               min="{{ $order->total_price }}" 
                               step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter amount paid">
                    </div>
                    
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200">
                        Process Cash Payment
                    </button>
                </form>
            </div>

            <!-- Cashless Payment -->
            <div class="bg-white border rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Cashless Payment
                </h3>
                <p class="text-gray-600 mb-4">Pay using credit card, debit card, or e-wallet</p>
                
                <form action="{{ route('payment.cashless') }}" method="POST" id="cashless-form">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="table_number" id="cashless-table-number">
                    
                    <div class="mb-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <span>💳 Credit/Debit Card</span>
                            <span>•</span>
                            <span>📱 E-Wallet</span>
                            <span>•</span>
                            <span>🏦 Bank Transfer</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Pay with Midtrans
                    </button>
                </form>
            </div>
        </div>

        <!-- Back to Cart -->
        <div class="mt-6 text-center">
            <a href="{{ route('cart') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Cart
            </a>
        </div>
    </section>

    <script>
        // Update hidden table number fields when main table number changes
        document.getElementById('table_number').addEventListener('input', function() {
            const tableNumber = this.value;
            document.getElementById('cash-table-number').value = tableNumber;
            document.getElementById('cashless-table-number').value = tableNumber;
        });

        // Initialize hidden fields with current table number
        document.addEventListener('DOMContentLoaded', function() {
            const tableNumber = document.getElementById('table_number').value;
            document.getElementById('cash-table-number').value = tableNumber;
            document.getElementById('cashless-table-number').value = tableNumber;
        });

        // Validate table number before form submission
        document.getElementById('cash-form').addEventListener('submit', function(e) {
            const tableNumber = document.getElementById('table_number').value;
            if (!tableNumber.trim()) {
                e.preventDefault();
                alert('Please enter your table number');
                document.getElementById('table_number').focus();
                return false;
            }
        });

        document.getElementById('cashless-form').addEventListener('submit', function(e) {
            const tableNumber = document.getElementById('table_number').value;
            if (!tableNumber.trim()) {
                e.preventDefault();
                alert('Please enter your table number');
                document.getElementById('table_number').focus();
                return false;
            }
        });
    </script>
@endsection