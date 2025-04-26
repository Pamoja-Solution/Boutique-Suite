<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monnaies', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->unique();
            $table->string('symbole')->unique();
            $table->string('code')->unique(); // Ajout d'un code ISO (ex: USD, EUR)
            $table->decimal('taux_change', 10, 6)->default(1.000000);
            $table->enum('statut',['0','1'])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monnaies');
    }
};
