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
                                <tr class="hover:bg-gray-50" data-order-id="{{ $order->id }}">
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
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                            @switch($order->status)
                                                @case('queue')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('process')
                                                    bg-orange-100 text-orange-800
                                                    @break
                                                @case('ready')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('delivered')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['queue', 'process', 'ready', 'delivered'] as $status)
                                                @php
                                                    $colorClass = match($status) {
                                                        'queue' => 'text-blue-700 bg-blue-100 border-blue-300 hover:bg-blue-200',
                                                        'process' => 'text-orange-700 bg-orange-100 border-orange-300 hover:bg-orange-200',
                                                        'ready' => 'text-yellow-700 bg-yellow-100 border-yellow-300 hover:bg-yellow-200',
                                                        'delivered' => 'text-green-700 bg-green-100 border-green-300 hover:bg-green-200',
                                                    };
                                                    $isDisabled = $order->status === $status ? 'opacity-50 cursor-not-allowed' : '';
                                                @endphp

                                                <button 
                                                    class="status-btn px-3 py-1 w-full text-xs font-medium border rounded-md transition {{ $colorClass }} {{ $isDisabled }}"
                                                    data-order-id="{{ $order->id }}" 
                                                    data-status="{{ $status }}"
                                                    {{ $order->status === $status ? 'disabled' : '' }}
                                                >
                                                    {{ ucfirst($status) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
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

    <!-- Loading overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-600">Updating status...</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusButtons = document.querySelectorAll('.status-btn');
            const loadingOverlay = document.getElementById('loading-overlay');
            
            statusButtons.forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    const orderId = this.dataset.orderId;
                    const newStatus = this.dataset.status;
                    
                    if (this.disabled) return;
                    
                    // Show loading
                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.classList.add('flex');
                    
                    try {
                        const response = await fetch(`/order/${orderId}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Update UI
                            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                            const statusBadge = row.querySelector('td:nth-child(7) span');
                            const actionButtons = row.querySelectorAll('.status-btn');
                            
                            // Update status badge
                            statusBadge.textContent = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);
                            statusBadge.className = 'inline-flex px-3 py-1 text-xs font-semibold rounded-full ' + 
                                getStatusBadgeClass(data.new_status);
                            
                            // Update button states
                            actionButtons.forEach(btn => {
                                if (btn.dataset.status === data.new_status) {
                                    btn.disabled = true;
                                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                                } else {
                                    btn.disabled = false;
                                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                                }
                            });
                            
                            // Show success message
                            showNotification(data.message, 'success');
                        } else {
                            showNotification(data.message || 'Gagal mengubah status', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan saat mengubah status', 'error');
                    } finally {
                        // Hide loading
                        loadingOverlay.classList.add('hidden');
                        loadingOverlay.classList.remove('flex');
                    }
                });
            });
            
            function getStatusBadgeClass(status) {
                const classes = {
                    'queue': 'bg-blue-100 text-blue-800',
                    'process': 'bg-orange-100 text-orange-800', 
                    'ready': 'bg-yellow-100 text-yellow-800',
                    'delivered': 'bg-green-100 text-green-800'
                };
                return classes[status] || 'bg-gray-100 text-gray-800';
            }
            
            function showNotification(message, type) {
                // Remove existing notifications
                const existingNotifications = document.querySelectorAll('.notification');
                existingNotifications.forEach(n => n.remove());
                
                const notification = document.createElement('div');
                notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    </script>
@endsection