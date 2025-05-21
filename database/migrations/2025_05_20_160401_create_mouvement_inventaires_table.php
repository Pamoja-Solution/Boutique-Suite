<?php

use App\Models\Inventaire;
use App\Models\Produit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mouvement_inventaires', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Inventaire::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Produit::class)->constrained()->cascadeOnDelete();
            $table->integer('quantite_theorique')->default(0);
            $table->integer('quantite_reelle')->nullable();
            $table->integer('ecart')->nullable();
            $table->string('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mouvement_inventaires');
    }
};