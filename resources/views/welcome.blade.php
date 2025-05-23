<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pharmacie - Accueil</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-100 to-green-300 dark:from-green-900 dark:to-green-700 font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center text-gray-800 dark:text-white px-4">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold mb-4">Bienvenue à la Boutique</h1>
            <p class="text-lg">Connectez-vous ou accédez directement à votre espace de travail.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-10 w-full max-w-md space-y-6">
            @if (Route::has('login'))
                <div class="text-center space-y-4">
                    @auth
                        @if (auth()->user()->isVendeur() )
                            <a href="{{ route('dashboard') }}"
                            class="inline-block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition" wire:navigate>
                            Accéder au Dashboard
                            </a>
                        @endif
                        @if (auth()->user()->isGerant() )
                            <a href="{{ route('gerant.dashboard') }}"
                            class="inline-block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition" wire:navigate>
                            Accéder au Dashboard
                            </a>
                        @endif
                        
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-block w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition" wire:navigate>
                            Se connecter
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 rounded-lg transition" wire:navigate>
                                S'inscrire
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>

        <footer class="mt-10 text-sm text-gray-600 dark:text-gray-300">
            &copy; {{ date('Y') }} Boutique. Tous droits réservés.
        </footer>
    </div>
</body>
</html>
