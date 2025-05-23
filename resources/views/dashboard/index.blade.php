<x-app-layout>
    @section("titre","Station de Base")
    @include('gerant.nav')

    @if (auth()->user()->isGerant() || auth()->user()->isSuperviseur())

    <livewire:dashboard>
        @else
    <livewire:gestion-vente>

        @endif
</x-app-layout>
