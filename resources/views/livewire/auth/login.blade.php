<div>
    <form wire:submit.prevent="authenticate">
        <!-- Champ matricule -->
        <div>
            <label for="matricule">Matricule</label>
            <input wire:model="form.matricule" type="text" id="matricule">
            @error('form.matricule') <span>{{ $message }}</span> @enderror
        </div>

        <!-- Champ password -->
        <div>
            <label for="password">Mot de passe</label>
            <input wire:model="form.password" type="password" id="password">
            @error('form.password') <span>{{ $message }}</span> @enderror
        </div>

        <!-- Remember me -->
        <div>
            <input wire:model="form.remember" type="checkbox" id="remember">
            <label for="remember">Se souvenir de moi</label>
        </div>

        <button type="submit">Se connecterddddddddddddddddd</button>
    </form>
</div>