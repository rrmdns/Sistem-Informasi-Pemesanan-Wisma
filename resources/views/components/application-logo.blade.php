@if (request()->is('login') || request()->is('register'))
    <div class="text-center">
        <img src="{{ asset('images/logo-pusri.png') }}" alt="Logo Pusri" class="w-24 h-auto mx-auto mb-4">
        <h1 class="text-xl font-semibold text-gray-800">
            Selamat Datang di <span class="text-indigo-600">Wisma Pusri</span>
        </h1>
        <p class="text-sm text-gray-600 mt-1">
            Silakan login untuk melakukan pemesanan atau mengelola reservasi.
        </p>
    </div>
@endif
