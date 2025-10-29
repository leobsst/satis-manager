@props(['code' => 500, 'title'])

<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} - {{ config('app.name', $title) }}</title>
    <meta property="robots" content="nodindex, nofollow">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center p-5 bg-linear-to-br from-indigo-500 to-purple-600">
    <div class="bg-white rounded-3xl shadow-2xl p-10 md:p-15 text-center max-w-2xl w-full">
        @hasSection('icon')
        <!-- Icon -->
        <div class="w-30 h-30 mx-auto mb-8 animate-pulse-error">
            @yield('icon')
        </div>
        @endif

        <!-- Error Code -->
        <div class="text-8xl md:text-9xl font-bold gradient-text leading-none mb-5">
            {{ $code }}
        </div>

        <!-- Error Title -->
        <h1 class="text-3xl md:text-4xl font-semibold text-gray-800 mb-4">
            {{ $title }}
        </h1>

        @hasSection('message')
        <!-- Error Message -->
        <p class="text-lg text-gray-600 mb-8 leading-relaxed flex flex-col gap-8">
            @yield('message')
        </p>
        @endif

        @if($code >= 400 && $code < 500)
        <!-- Home Button for 4xx errors -->
        <div class="mt-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-linear-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-full hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                @lang('back_to_home')
            </a>
        </div>
        @endif
    </div>
</body>
</html>
