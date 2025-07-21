@extends('layouts.app')

@section('content')    
    <section class="w-full py-4">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                
                <!-- Header -->
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Order Management</h2>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Order ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Table Number</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Order Items</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Price</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Order Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-base font-bold text-blue-600">{{ $order->id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900">{{ $order->user->username ?? 'N/A' }}</p>
                                            <p class="text-gray-500 text-xs">{{ $order->user->role ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-sm font-medium text-gray-900">{{ $order->table_number ?? 'Not Set' }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-1 text-sm">
                                            @foreach($order->orderItems as $item)
                                                <p class="font-medium text-gray-900">{{ $item->product->name ?? 'Product not found' }}</p>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-1 text-sm">
                                            @foreach($order->orderItems as $item)
                                                <p class="font-medium text-gray-900">{{ $item->quantity }}</p>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-base font-bold text-gray-900">
                                            RP {{ number_format($order->total_price ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
                                                @php
                                                    $colorClass = match($status) {
                                                        'pending' => 'text-yellow-700 bg-yellow-100 border-yellow-300 hover:bg-yellow-200',
                                                        'processing' => 'text-orange-700 bg-orange-100 border-orange-300 hover:bg-orange-200',
                                                        'completed' => 'text-green-700 bg-green-100 border-green-300 hover:bg-green-200',
                                                        'cancelled' => 'text-red-700 bg-red-100 border-red-300 hover:bg-red-200',
                                                    };
                                                @endphp

                                                <button class="px-3 py-1 w-full text-xs font-medium border rounded-md transition {{ $colorClass }}">
                                                    {{ ucfirst($status) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                        <div class="text-lg mb-2">No orders found</div>
                                        <div class="text-sm">There are no order records to display.</div>
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
