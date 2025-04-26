@extends('layouts.app')

@section('title', 'Modification de province')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="mb-6">
        <h4 class="text-2xl font-bold text-gray-800 dark:text-white">Modification de monnaie</h4>
        <h6 class="text-gray-600 dark:text-gray-400"></h6>
    </div>

    <!-- Formulaire -->
    <div class="flex flex-col md:flex-row gap-6">
        <div class="w-full md:w-5/12">
            <form id="formSend" action="{{route('monnaie.update',$monnaie->id)}}" method="POST" 
                  class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Nom de la devise<span class="text-red-500 dark:text-red-400">*</span>
                    </label>
                    <input type="text" name="nom" id="nom" value="{{$monnaie->libelle}}" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 @error('nom') border-red-500 @enderror" 
                           placeholder="Saisir le nom" required />
                    @error('nom')
                    <p class="text-red-500 dark:text-red-400 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-6">
                    <button type="submit" id="btnSend" 
                            class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-150 ease-in-out">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('formSend').addEventListener('submit', function(e){
        const btn = document.getElementById('btnSend');
        btn.innerHTML = "En cours ...";
        btn.disabled = true;
        btn.classList.remove('bg-blue-500', 'hover:bg-blue-600', 'dark:bg-blue-600', 'dark:hover:bg-blue-700');
        btn.classList.add('bg-blue-400', 'dark:bg-blue-500', 'cursor-not-allowed');
    });
</script>
@endsection