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
        Schema::create('rayons', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Ex: "Boissons", "Produits Laitiers"
            $table->string('code')->unique(); // Ex: "RAY-01" pour identification rapide
            $table->text('description')->nullable();
            $table->string('icon')->default('box'); // Pour affichage UI
            $table->integer('ordre_affichage')->default(0);
            $table->timestamps();
        });
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rayons');

        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
