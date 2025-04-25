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
        Schema::create('sous_rayons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rayon_id')->constrained()->cascadeOnDelete();
            $table->string('nom'); // Ex: "Eaux Minérales", "Yaourts"
            $table->string('code_emplacement')->unique(); // Ex: "A3-B2" (Allée 3, Étagère B2)
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sous_rayons');

    }
};
