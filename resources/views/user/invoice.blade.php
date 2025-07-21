@extends('layouts.app')

@section('content')
    <section class="px-4 py-6">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <!-- Invoice Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-green-600 mb-2">INVOICE</h1>
                <p class="text-gray-600">Payment Successful</p>
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mt-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Invoice Details</h3>
                    <div class="space-y-1 text-sm">
                        <div><strong>Invoice #:</strong> INV-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div><strong>Order #:</strong> {{ $payment->order->id }}</div>
                        <div><strong>Date:</strong> {{ $payment->payment_date->format('d M Y, H:i') }}</div>
                        <div><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Customer Details</h3>
                    <div class="space-y-1 text-sm">
                        <div><strong>Name:</strong> {{ $payment->order->user->username }}</div>
                        <div><strong>Table:</strong> {{ $payment->order->table_number }}</div>
                        <div><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-8">
                <h3 class="font-semibold text-gray-700 mb-4">Order Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border border-gray-300 px-4 py-2 text-left">Item</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Qty</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Price</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment->order->orderItems as $item)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $item->product->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $item->quantity }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">RP {{ number_format($item->product->price, 0, ',', '.') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-right">RP {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="border-t pt-6">
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between py-2">
                            <span>Subtotal:</span>
                            <span>RP {{ number_format($payment->order->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-2 font-bold text-lg border-t">
                            <span>Total Paid:</span>
                            <span>RP {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        @if($payment->payment_method === 'cash' && $payment->change_amount > 0)
                            <div class="flex justify-between py-2 text-sm text-gray-600">
                                <span>Change:</span>
                                <span>RP {{ number_format($payment->change_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="mt-8 p-4 bg-green-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-green-800 font-semibold">Payment Status: PAID</span>
                </div>
                <p class="text-sm text-green-700 mt-1">
                    Thank you for your payment. Your order has been confirmed and will be processed shortly.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-center space-x-4">
                <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    Print Invoice
                </button>
                <button onclick="downloadPDF()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                    Download PDF
                </button>
                <a href="{{ route('food') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                    New Order
                </a>
            </div>
        </div>
    </section>

    <script>
        function downloadPDF() {
            // Create a simple PDF download simulation
            const element = document.querySelector('.max-w-2xl');
            const opt = {
                margin: 1,
                filename: 'invoice-{{ $payment->id }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            
            // This would require html2pdf.js library
            // For now, just show an alert
            alert('PDF download feature would be implemented with html2pdf.js library');
        }
    </script>

    <style>
        @media print {
            .print\\:hidden {
                display: none !important;
            }
            
            body {
                background: white !important;
            }
            
            .shadow-lg {
                box-shadow: none !important;
            }
        }
    </style>
@endsection