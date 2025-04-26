<div class="">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 overflow-x-auto whitespace-nowrap">
        <div class="flex space-x-6 animate-scroll-slow">
            @forelse ($tauxChanges as $taux)
                <div class="flex items-center space-x-2">
                    <span class="font-semibold dark:text-white">{{ $taux->monnaieSource->code }} → {{ $taux->monnaieCible->code }}</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ number_format($taux->taux, 6) }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">({{ \Carbon\Carbon::parse($taux->date_effet)->format('d/m/Y') }})</span>
                </div>
            @empty
                <span class="text-gray-400 dark:text-gray-500">Aucun taux disponible.</span>
            @endforelse
        </div>
    </div>

    <style>
    @keyframes scroll-slow {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-scroll-slow {
        display: inline-flex;
        animation: scroll-slow 60s linear infinite;
    }
    </style>
</div>
