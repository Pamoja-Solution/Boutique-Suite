<?php

use App\Models\Categorie;
use App\Models\Fournisseur;
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
        
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code_barre')->unique()->nullable();
            $table->string('reference_interne')->unique(); // Ex: "COCA33CL"
            
            // Stock & Prix
            $table->decimal('prix_vente', 10, 2);
            $table->decimal('prix_achat', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('seuil_alerte')->default(3);
            
            // Relations d'emplacement
            //$table->foreignId('sous_rayon_id')->constrained()->onDelete('SET NULL');
            $table->foreignId('sous_rayon_id')->nullable()->constrained()->onDelete('SET NULL');
            $table->foreignId('fournisseur_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date_expiration')->nullable(); // Seulement pour certains produits
            // Metadata
            $table->string('unite_mesure')->default('unité');
            $table->boolean('taxable')->default(true);
            $table->boolean('rupture_stock')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
