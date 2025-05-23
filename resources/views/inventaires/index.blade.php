<x-app-layout>
    @include('gerant.nav')
    @section("titre","Gestion des Inventaires")

    <div class="px-2 sm:px-3 lg:px-4 py-3">
        <h2 class="font-semibold text-xl  leading-tight">
            {{ __('Gestion des inventaires') }}
        </h2>
    </div>

    <div class="py-12 bg-base-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:inventaires-list />
        </div>
    </div>
</x-app-layout>