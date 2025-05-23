<!DOCTYPE html>
<html lang="fr" class="h-full dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accès refusé</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 4s infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br  flex items-center justify-center">
    <div class="max-w-md mx-4 p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl text-center transform transition-all duration-300 hover:shadow-2xl">
        <!-- Icône animée -->
        <div class="float-animation mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        
        <!-- Message principal -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">Accès refusé</h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
            Vous n'avez pas les autorisations nécessaires pour accéder à cette page.
        </p>
        
        <!-- Message secondaire -->
        <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg text-left">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 dark:text-red-300">
                        Veuillez contacter l'administrateur système pour obtenir les droits d'accès.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="space-y-4">
            <a href="/" wire:navigate class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Retour à l'accueil
            </a>
            
            <a href="mailto:admin@votredomaine.com" wire:navigate class="w-full flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Contacter l'admin
            </a>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-600 text-sm text-gray-500 dark:text-gray-400">
            <p>Code erreur : <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">403_FORBIDDEN</span></p>
            <p class="mt-2">Si vous pensez qu'il s'agit d'une erreur, merci de nous signaler le problème.</p>
        </div>
    </div>

    <div class="fixed inset-0 overflow-hidden -z-10">
        <div class="absolute inset-0 bg-gradient-to-br from-red-200 to-white opacity-50"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNlZTFlMWUiIGZpbGwtb3BhY2l0eT0iMC4yIj48cGF0aCBkPSJNMzYgMzRjMC0xLjEuOS0yIDItMmgxNmMxLjEgMCAyIC45IDIgMnYxNmMwIDEuMS0uOSAyLTIgMkgzOGMtMS4xIDAtMi0uOS0yLTJWMzR6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-10"></div>
    </div>
</body>
<script>
    // Redirection après 10 secondes
    let seconds = 10;
    const timer = setInterval(() => {
        seconds--;
        document.getElementById('countdown')?.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            window.location.href = '/';
        }
    }, 1000);
</script>
<script>
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    }
</script>
</html>
