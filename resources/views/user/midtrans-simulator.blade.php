@extends('layouts.app')

@section('content')
    <section class="px-4 py-6">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <img src="https://midtrans.com/assets/images/main/midtrans-logo.svg" alt="Midtrans" class="h-8 mx-auto mb-4">
                <h1 class="text-xl font-bold">Payment Gateway</h1>
                <p class="text-gray-600">Secure Payment Processing</p>
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold mb-2">Payment Details</h3>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Order ID:</span>
                        <span>#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Amount:</span>
                        <span>RP {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Transaction ID:</span>
                        <span>{{ $payment->transaction_id }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="space-y-3 mb-6">
                <button onclick="selectPaymentMethod('credit_card')" 
                        class="w-full p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition duration-200 payment-method" 
                        data-method="credit_card">
                    <div class="flex items-center">
                        <div class="w-6 h-6 mr-3">💳</div>
                        <span>Credit/Debit Card</span>
                    </div>
                </button>
                
                <button onclick="selectPaymentMethod('gopay')" 
                        class="w-full p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition duration-200 payment-method" 
                        data-method="gopay">
                    <div class="flex items-center">
                        <div class="w-6 h-6 mr-3">🏍️</div>
                        <span>GoPay</span>
                    </div>
                </button>
                
                <button onclick="selectPaymentMethod('dana')" 
                        class="w-full p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition duration-200 payment-method" 
                        data-method="dana">
                    <div class="flex items-center">
                        <div class="w-6 h-6 mr-3">💙</div>
                        <span>DANA</span>
                    </div>
                </button>
                
                <button onclick="selectPaymentMethod('bank_transfer')" 
                        class="w-full p-3 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition duration-200 payment-method" 
                        data-method="bank_transfer">
                    <div class="flex items-center">
                        <div class="w-6 h-6 mr-3">🏦</div>
                        <span>Bank Transfer</span>
                    </div>
                </button>
            </div>

            <!-- Selected Payment Method Details -->
            <div id="payment-details" class="hidden mb-6">
                <div id="credit-card-form" class="payment-form hidden">
                    <h4 class="font-semibold mb-3">Credit Card Details</h4>
                    <div class="space-y-3">
                        <input type="text" placeholder="Card Number" class="w-full p-2 border rounded">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" placeholder="MM/YY" class="w-full p-2 border rounded">
                            <input type="text" placeholder="CVV" class="w-full p-2 border rounded">
                        </div>
                        <input type="text" placeholder="Cardholder Name" class="w-full p-2 border rounded">
                    </div>
                </div>
                
                <div id="ewallet-form" class="payment-form hidden">
                    <h4 class="font-semibold mb-3">E-Wallet Payment</h4>
                    <p class="text-sm text-gray-600 mb-3">You will be redirected to complete the payment</p>
                </div>
                
                <div id="bank-transfer-form" class="payment-form hidden">
                    <h4 class="font-semibold mb-3">Bank Transfer</h4>
                    <p class="text-sm text-gray-600 mb-3">Transfer to the virtual account number that will be provided</p>
                </div>
            </div>

            <!-- Payment Buttons -->
            <div class="space-y-3">
                <button id="pay-button" 
                        onclick="processPayment()" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 disabled:opacity-50" 
                        disabled>
                    Pay Now
                </button>
                
                <button onclick="cancelPayment()" 
                        class="w-full bg-gray-200 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-300 transition duration-200">
                    Cancel
                </button>
            </div>
        </div>
    </section>

    <!-- Loading Modal -->
    <div id="loading-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p>Processing payment...</p>
        </div>
    </div>

    <script>
        let selectedMethod = null;

        function selectPaymentMethod(method) {
            selectedMethod = method;
            
            // Remove active state from all buttons
            document.querySelectorAll('.payment-method').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50');
            });
            
            // Add active state to selected button
            document.querySelector(`[data-method="${method}"]`).classList.add('border-blue-500', 'bg-blue-50');
            
            // Show payment details
            document.getElementById('payment-details').classList.remove('hidden');
            
            // Hide all forms
            document.querySelectorAll('.payment-form').forEach(form => {
                form.classList.add('hidden');
            });
            
            // Show relevant form
            if (method === 'credit_card') {
                document.getElementById('credit-card-form').classList.remove('hidden');
            } else if (method === 'gopay' || method === 'dana') {
                document.getElementById('ewallet-form').classList.remove('hidden');
            } else if (method === 'bank_transfer') {
                document.getElementById('bank-transfer-form').classList.remove('hidden');
            }
            
            // Enable pay button
            document.getElementById('pay-button').disabled = false;
        }

        function processPayment() {
            if (!selectedMethod) {
                alert('Please select a payment method');
                return;
            }

            // Show loading
            document.getElementById('loading-modal').classList.remove('hidden');
            
            // Simulate payment processing
            setTimeout(() => {
                // Simulate random success/failure (80% success rate)
                const isSuccess = Math.random() > 0.2;
                
                // Hide loading
                document.getElementById('loading-modal').classList.add('hidden');
                
                // Process result
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('payment.cashless.complete', $payment->id) }}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'payment_status';
                statusInput.value = isSuccess ? 'success' : 'failed';
                form.appendChild(statusInput);
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = 'payment_method';
                methodInput.value = selectedMethod;
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }, 2000);
        }

        function cancelPayment() {
            if (confirm('Are you sure you want to cancel this payment?')) {
                window.location.href = '{{ route('cart') }}';
            }
        }
    </script>
@endsection