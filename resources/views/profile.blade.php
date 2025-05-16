    <x-app-layout>
    @include('gerant.nav')

    <div class="flex justify-between items-center mt-6">

        <h1 class="text-2xl font-bold">
                {{ __('Gerer Mon profil') }}
            </h1>
        </div>
    
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-base-100 shadow rounded-box">
                    <div class="max-w-xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>
    
                <div class="p-4 sm:p-8 bg-base-100 shadow rounded-box">
                    <div class="max-w-xl">
                        <livewire:profile.update-password-form />
                    </div>
                </div>
    
                <div class="p-4 sm:p-8 bg-base-100 shadow rounded-box">
                    <div class="max-w-xl">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>