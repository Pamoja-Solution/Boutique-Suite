<?php

use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historique_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Produit::class)->constrained()->onDelete('cascade');
            $table->integer('quantite');
            $table->integer('stock_avant');
            $table->integer('stock_apres');
            $table->enum('type_mouvement', ['inventaire', 'vente', 'achat', 'ajustement'])->default('ajustement');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->morphs('source'); // Pour relier à différentes tables (ventes, inventaires, etc.)
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historique_stocks');
    }
};