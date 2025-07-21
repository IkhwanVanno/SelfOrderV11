<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SelfOrderV11</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">

        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

        {{-- Success message --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 border border-green-300 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error message for login failure --}}
        @if($errors->has('login'))
            <div class="bg-red-100 text-red-700 border border-red-300 px-4 py-3 rounded mb-4">
                {{ $errors->first('login') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="bg-red-100 text-red-700 border border-red-300 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('username')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" id="password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200">
                Login
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('register') }}" class="text-blue-500 hover:underline">
                    Belum punya akun? Daftar di sini
                </a>
            </div>
        </form>
    </div>
</body>
</html>
