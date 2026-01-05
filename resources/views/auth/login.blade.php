<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIPETER</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen flex items-start justify-center p-4 pt-20 sm:pt-24">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg p-3">
                <img src="{{ asset('images/STABA.png') }}" alt="Logo STABA" class="w-full h-full object-contain">
            </div>
            <h1 class="text-3xl font-bold text-blue-900 mb-2">SIPETER</h1>
            <p class="text-blue-700 opacity-90">Sistem Pengajuan Terpadu Dokumen STABA</p>
        </div>


        <div class="bg-white rounded-xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login Internal</h2>


            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <div class="font-bold">Login Gagal!</div>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            @if(str_contains($error, 'match') || str_contains($error, 'credentials'))
                                <li>Email/NIP/NIDN atau Password salah.</li>
                            @else
                                <li>{{ $error }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                 <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                     <p>{{ session('error') }}</p>
                 </div>
            @endif


            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-1">
                        Email / NIP / NIDN
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-user text-gray-400"></i>
                        </span>

                        <input
                            type="text"
                            id="login"
                            name="login"
                            value="{{ old('login') }}"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan Email/NIP/NIDN"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-lock text-gray-400"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan Password"
                            required
                        >
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" id="togglePassword">
                            <i class="fas fa-eye text-lg text-gray-400 hover:text-blue-600 transition-colors"></i>
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-gray-700">
                            Ingat saya
                        </label>
                    </div>

                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                        Lupa password?
                    </a>
                </div>

                <div>

                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-md text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5 hover:shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2 mt-1"></i>
                        Login
                    </button>
                </div>
            </form>
        </div>

        <footer class="text-center mt-8 text-blue-700 text-sm opacity-75">
            © {{ date('Y') }} STABA. All rights reserved.
        </footer>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = this.querySelector('i');
            if (type === 'password') {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>
</html>
