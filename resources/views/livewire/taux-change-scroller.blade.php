<div class="bg-secondary rounded-box shadow  overflow-hidden">
    <div class="relative overflow-x-hidden">
      <div class="flex space-x-4 animate-infinite-scroll whitespace-nowrap py-2">
        @forelse ($tauxChanges as $taux)
          <div class=" inline-flex items-center gap-2 bg-neutral/10 p-3 rounded-box">
            <span class="font-semibold">{{ $taux->monnaieSource->code }} → {{ $taux->monnaieCible->code }}</span>
            <span class="text-neutral text-md font-bold">{{ number_format($taux->taux, 2) }}</span>
            <span class="badge text-sm text-neutral/50">{{ \Carbon\Carbon::parse($taux->date_effet)->format('d/m/Y') }}</span>
          </div>
        @empty
          <span class="text-neutral-content/50">Aucun taux disponible.</span>
        @endforelse
      </div>
    </div>

  <style>
    .animate-infinite-scroll {
      animation: scroll-horizontal 30s linear infinite;
    }
    @keyframes scroll-horizontal {
      from { transform: translateX(0); }
      to { transform: translateX(-50%); }
    }
    </style>
  </div>
  