@extends('layouts.app')

@section('content') 
    <section class="w-full py-4">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Payment Management</h2>
                        <div class="text-sm text-gray-600">
                            Total Records: {{ $payments->count() }}
                        </div>
                    </div>
                </div>

                <!-- Filter Tab -->
                <div class="bg-white border-b border-gray-200">
                    <nav class="flex space-x-8 px-4">
                        <button onclick="filterPayments('all')" class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap filter-btn active">
                            All Payments
                        </button>
                        <button onclick="filterPayments('paid')" class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap filter-btn">
                            Paid
                        </button>
                        <button onclick="filterPayments('pending')" class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap filter-btn">
                            Pending
                        </button>
                        <button onclick="filterPayments('failed')" class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap filter-btn">
                            Failed
                        </button>
                    </nav>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Payment ID</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Transaction ID</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Order Details</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Customer</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Payment Info</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Date & Time</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 payment-row" data-status="{{ $payment->payment_status }}">
                                    <!-- Payment ID -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-base sm:text-lg font-bold text-blue-600">{{ $payment->id }}</span>
                                        </div>
                                    </td>

                                    <!-- Transaction ID -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                            {{ $payment->transaction_id }}
                                        </div>
                                        @if($payment->status_code)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Code: {{ $payment->status_code }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Order Details -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="text-sm font-medium text-gray-900">Order #{{ $payment->order_id }}</div>
                                        @if($payment->order->table_number)
                                            <div class="text-xs text-gray-500">Table: {{ $payment->order->table_number }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $payment->order->orderItems->count() }} item(s)
                                        </div>
                                    </td>

                                    <!-- Customer -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $payment->order->user->username ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $payment->order->user->role ?? '' }}
                                        </div>
                                    </td>

                                    <!-- Payment Info -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        @if($payment->payment_type)
                                            <div class="inline-flex px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                                            </div>
                                        @else
                                            <div class="inline-flex px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                                Midtrans
                                            </div>
                                        @endif
                                        
                                        @if($payment->fraud_status)
                                            <div class="text-xs mt-1 
                                                @if($payment->fraud_status === 'accept') text-green-600
                                                @elseif($payment->fraud_status === 'challenge') text-orange-600
                                                @else text-red-600 @endif">
                                                Fraud: {{ $payment->fraud_status }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <span class="text-base sm:text-lg font-bold text-gray-900">
                                            {{ $payment->formatted_amount }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="space-y-2">
                                            <!-- Payment Status -->
                                            @php
                                                $paymentStatusColor = match($payment->payment_status) {
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'challenge' => 'bg-orange-100 text-orange-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $paymentStatusColor }}">
                                                {{ ucfirst($payment->payment_status) }}
                                            </span>

                                            <!-- Transaction Status -->
                                            @php
                                                $transactionStatusColor = match($payment->transaction_status) {
                                                    'settlement' => 'bg-green-100 text-green-800',
                                                    'capture' => 'bg-blue-100 text-blue-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'deny', 'cancel', 'expire', 'failure' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <div class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $transactionStatusColor }}">
                                                {{ ucfirst($payment->transaction_status) }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Date & Time -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="text-sm text-gray-900">
                                            {{ $payment->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $payment->created_at->format('H:i') }}
                                        </div>
                                        @if($payment->transaction_time)
                                            <div class="text-xs text-gray-400 mt-1">
                                                Paid: {{ $payment->transaction_time->format('H:i') }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="flex flex-col space-y-1">
                                            <button onclick="viewPaymentDetails({{ $payment->id }})" 
                                                    class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                                View Details
                                            </button>
                                            
                                            @if($payment->isPending())
                                                <button onclick="refreshPaymentStatus('{{ $payment->transaction_id }}')" 
                                                        class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200 transition-colors">
                                                    Check Status
                                                </button>
                                            @endif
                                            
                                            <button onclick="viewOrderDetails({{ $payment->order_id }})" 
                                                    class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                                                    View Order
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 sm:px-6 py-8 text-center text-gray-500">
                                        <div class="text-lg mb-2">No payments found</div>
                                        <div class="text-sm">There are no payment records to display.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Details Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Payment Details</h3>
                    <button onclick="closeModal('paymentModal')" class="text-gray-400 hover:text-gray-600">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="paymentDetails" class="space-y-4">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // Filter functionality
        function filterPayments(status) {
            // Update active tab
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'active');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-blue-500', 'text-blue-600', 'active');
            
            // Filter rows
            document.querySelectorAll('.payment-row').forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // View payment details
        function viewPaymentDetails(paymentId) {
            fetch(`/payment/${paymentId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const payment = data.payment;
                        const detailsHtml = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Transaction Information</h4>
                                    <div class="space-y-2 text-sm">
                                        <div><span class="font-medium">Transaction ID:</span> ${payment.transaction_id}</div>
                                        <div><span class="font-medium">Order ID:</span> #${payment.order_id}</div>
                                        <div><span class="font-medium">Amount:</span> ${payment.formatted_amount}</div>
                                        <div><span class="font-medium">Payment Type:</span> ${payment.payment_type || 'Midtrans'}</div>
                                        <div><span class="font-medium">Status Code:</span> ${payment.status_code || '-'}</div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Status Information</h4>
                                    <div class="space-y-2 text-sm">
                                        <div><span class="font-medium">Payment Status:</span> <span class="px-2 py-1 rounded text-xs bg-${payment.status_color}-100 text-${payment.status_color}-800">${payment.payment_status}</span></div>
                                        <div><span class="font-medium">Transaction Status:</span> ${payment.transaction_status}</div>
                                        <div><span class="font-medium">Fraud Status:</span> ${payment.fraud_status || '-'}</div>
                                        <div><span class="font-medium">Created:</span> ${payment.created_at}</div>
                                        ${payment.transaction_time ? `<div><span class="font-medium">Paid At:</span> ${payment.transaction_time}</div>` : ''}
                                    </div>
                                </div>
                            </div>
                            ${payment.status_message ? `<div class="mt-4"><span class="font-medium">Message:</span> ${payment.status_message}</div>` : ''}
                        `;
                        document.getElementById('paymentDetails').innerHTML = detailsHtml;
                        document.getElementById('paymentModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load payment details');
                });
        }

        // Refresh payment status
        function refreshPaymentStatus(transactionId) {
            const button = event.target;
            button.disabled = true;
            button.textContent = 'Checking...';
            
            fetch(`/payment/status/${transactionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.is_paid) {
                        alert('Payment has been confirmed as paid!');
                        location.reload();
                    } else if (data.is_failed) {
                        alert('Payment has failed or expired');
                        location.reload();
                    } else if (data.is_pending) {
                        alert('Payment is still pending');
                    } else {
                        alert('Payment status: ' + data.payment_status);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to check payment status');
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'Check Status';
                });
        }

        // View order details
        function viewOrderDetails(orderId) {
            window.open(`/order/${orderId}`, '_blank');
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Auto refresh for pending payments every 2 minutes
        setInterval(function() {
            const pendingRows = document.querySelectorAll('[data-status="pending"]');
            if (pendingRows.length > 0) {
                location.reload();
            }
        }, 120000);
    </script>
@endsection