<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('titre', 'Boutique')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/sort@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
        <script src="https://cdn.tailwindcss.com"></script>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
           @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
        <style>
                   
           @font-face {
             font-family: 'Google';
             src: url('{{asset('ProductSans-Light.ttf')}}');
             font-weight: 500;
             
         }
         body{
             font-family: 'Google' !important;
         }
         </style>
    </head>
    <body class="">
            {{ $slot }}
           
    </body>
</html>
