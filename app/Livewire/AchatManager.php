<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')] // Exemple : layouts/app.blade.php
class AchatManager extends Component
{
    public function render()
    {
        return view('livewire.achat-manager');
    }
}
