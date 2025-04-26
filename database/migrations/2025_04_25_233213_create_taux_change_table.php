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
        Schema::create('taux_change', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monnaie_source_id')->constrained('monnaies')->cascadeOnDelete();
            $table->foreignId('monnaie_cible_id')->constrained('monnaies')->cascadeOnDelete();
            $table->decimal('taux', 10, 6);
            $table->date('date_effet');
            $table->timestamps();
            
            $table->unique(['monnaie_source_id', 'monnaie_cible_id', 'date_effet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taux_change');
    }
};
