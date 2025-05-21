<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventaires', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('motif');
            $table->text('notes')->nullable();
            $table->enum('statut', ['brouillon', 'en_cours', 'terminé', 'annulé'])->default('brouillon');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventaires');
    }
};