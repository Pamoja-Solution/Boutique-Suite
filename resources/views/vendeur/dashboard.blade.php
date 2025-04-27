<x-app-layout>
    
    @section("titre","Station de Travail")
    @if (request()->path()!= "gerant/dashboard")
        @include('gerant.nav')
    @endif

    <livewire:gestion-vente>
</x-app-layout>
