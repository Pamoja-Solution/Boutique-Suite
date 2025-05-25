<div class="bg-secondary rounded-box shadow overflow-hidden">
  <div class="relative overflow-x-hidden">
      <div class="flex space-x-4 animate-infinite-scroll whitespace-nowrap py-2">
          @forelse ($monnaies as $monnaie)
              <div class="inline-flex items-center gap-2 bg-neutral/10 p-3 rounded-box">
                  <span class="font-semibold">{{ $monnaie->code }} → CDF</span>
                  <span class="text-neutral text-md font-bold">{{ number_format($monnaie->taux_change, 2) }}</span>
                  <span class="badge text-sm text-neutral/50">Taux actuel</span>
              </div>
          @empty
              <span class="text-neutral-content/50">Aucun taux disponible.</span>
          @endforelse
          
          <!-- On duplique le contenu pour une animation fluide -->
          @foreach ($monnaies as $monnaie)
              <div class="inline-flex items-center gap-2 bg-neutral/10 p-3 rounded-box">
                  <span class="font-semibold">{{ $monnaie->code }} → CDF</span>
                  <span class="text-neutral text-md font-bold">{{ number_format($monnaie->taux_change, 2) }}</span>
                  <span class="badge text-sm text-neutral/50">Taux actuel</span>
              </div>
          @endforeach
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