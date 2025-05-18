<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('titre','Boutique - Gest')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Scripts -->
        <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/sort@3.x.x/dist/cdn.min.js"></script>
    
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
         @livewireStyles()
         <link href="/css/app.css" rel="stylesheet">
        </head>
        <body class="antialiased bg-base-300">
            <div class="min-h-screen flex flex-col">
                <livewire:layout.navigation />
    
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-base-300 shadow-sm">
                        <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif
    
                <!-- Page Content -->
                <main class="flex-grow container mx-auto px-2 sm:px-3 lg:px-4 py-3">
                    {{ $slot }}
                </main>
    
                <!-- Footer (optionnel) -->
                <footer class="bg-base-200 text-base-content py-6 mt-auto">
                    <div class="container mx-auto px-4 text-center">
                        <p>© {{ date('Y') }} Boutique - Gestion. Tous droits réservés.</p>
                    </div>
                </footer>
            </div>
    
            @livewireScripts()
            
            <!-- Theme toggle script -->
           
        </body>
</html>
