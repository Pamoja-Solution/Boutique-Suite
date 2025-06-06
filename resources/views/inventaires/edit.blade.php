<x-app-layout>
    @section("titre",'Editer Inventaire')

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier un inventaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:inventaire-form :inventaireId="$inventaireId" />
        </div>
    </div>
</x-app-layout>