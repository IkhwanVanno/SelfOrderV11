@extends('layouts.app')

@section('content') 
    <section class="w-full py-4">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Payment Management</h2>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Payment ID</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Order ID</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Customer</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Payment Method</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Payment Status</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Paid At</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-base sm:text-lg font-bold text-blue-600">{{ $payment->id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <span class="text-sm sm:text-base font-medium text-gray-900">#{{ $payment->order_id }}</span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <span class="text-sm sm:text-base font-medium text-gray-900">{{ $payment->order->user->username ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <span class="inline-flex px-2 py-1 text-xs sm:text-sm font-medium text-gray-800 bg-gray-100 rounded-full">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <span class="text-base sm:text-lg font-bold text-gray-900">
                                            RP {{ number_format($payment->order->total_amount ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        @if($payment->payment_status == 'paid')
                                            <span class="inline-flex px-3 py-1 text-xs sm:text-sm font-semibold text-green-800 bg-green-100 rounded-full">Paid</span>
                                        @elseif($payment->payment_status == 'pending')
                                            <span class="inline-flex px-3 py-1 text-xs sm:text-sm font-semibold text-orange-800 bg-orange-100 rounded-full">Pending</span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-xs sm:text-sm font-semibold text-red-800 bg-red-100 rounded-full">Failed</span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <span class="text-sm text-gray-500">
                                            {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 align-top">
                                        <div class="flex flex-wrap gap-2">
                                            <button class="px-3 py-1 w-full text-xs sm:text-sm font-medium text-orange-700 bg-orange-100 border border-orange-300 rounded-md hover:bg-orange-200 transition">
                                                Pending
                                            </button>
                                            <button class="px-3 py-1 w-full text-xs sm:text-sm font-medium text-green-700 bg-green-100 border border-green-300 rounded-md hover:bg-green-200 transition">
                                                Paid
                                            </button>
                                            <button class="px-3 py-1 w-full text-xs sm:text-sm font-medium text-red-700 bg-red-100 border border-red-300 rounded-md hover:bg-red-200 transition">
                                                Failed
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 sm:px-6 py-8 text-center text-gray-500">
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
@endsection
