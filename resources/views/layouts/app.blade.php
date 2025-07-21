<!DOCTYPE html>
<html>
<head>
    <title>SelfOrderV11 - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
</head>
<body>
    @include('layouts.header')

    <div class="flex w-full min-h-screen">
        {{-- Sidebar --}}
        @if(auth()->user()->role === 'admin')
            @include('layouts.Asidebar')
        @else
            @include('layouts.Usidebar')
        @endif

        {{-- Main content --}}
        <div class="flex-1 p-4">
            @yield('content')
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
