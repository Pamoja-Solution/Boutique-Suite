<x-app-layout>
    @section("titre","Gestion des Produits")
    @include('gerant.nav')

    @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())

    <livewire:dashboard>
        @else
    <livewire:gestion-vente>

        @endif
</x-app-layout>
