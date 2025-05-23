<x-app-layout>
    
    
    @if (request()->path()!= "gerant/dashboard")
        @include('gerant.nav')
    @endif

    <livewire:gestion-vente>
</x-app-layout>
