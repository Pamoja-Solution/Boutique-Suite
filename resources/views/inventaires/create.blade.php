<x-app-layout>
    @include('gerant.nav')

    <div class="px-2 sm:px-3 lg:px-4 py-3">
        <h2 class="font-semibold text-xl  leading-tight">
            {{ __('Créer un inventaire') }}
        </h2>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:inventaire-form />
        </div>
    </div>
</x-app-layout>