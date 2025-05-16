<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;
use App\Livewire\FuturistSalesDashboard;
use App\Livewire\GestionVente;
use App\Livewire\VendeurMedicaments;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\Dashboard;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MonnaieController;
use App\Livewire\UserManager;
use App\Livewire\MedicamentManager;
use App\Livewire\FournisseurManager;
use App\Livewire\ClientManager;
use App\Livewire\VenteManager;
use App\Livewire\AchatManager;
use App\Livewire\GestionClients;
use App\Livewire\GestionFournisseurs;
use App\Livewire\RayonManager;
use App\Livewire\Auth\Login;
use App\Livewire\ExpenseManager;
use App\Livewire\GestionStockSimple;
use App\Livewire\GestionTauxChange;
use App\Livewire\LesVentes;
use App\Livewire\MonnaieManager;
use App\Livewire\TauxChangeManager;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout')
        ->middleware('auth');


Route::middleware(['role:vendeur,gerant,superviseur'
    ])->group(function () {
        Route::get('/', function () {
            return redirect()->route('dashboard');
        });
        
        Route::get('/dashboard', function(){
            return view ('dashboard.index');
        })->name('dashboard');
        
        // Routes protégées par le middleware de rôle
        Route::middleware('role:gerant')->group(function () {
            Route::get('/rayons', RayonManager::class)->name('rayons.index');
        });
        
        // Routes accessibles à tous les utilisateurs authentifiés
        Route::get('/produits', function(){
            return view('produit.index');
        })->name('produits.index');
        Route::get('/fournisseurs', GestionFournisseurs::class)->name('fournisseurs.index');
        Route::get('/clients',  GestionClients::class)->name('clients.index');
       // Route::get('/ventes', VenteManager::class)->name('ventes.index');
    });
    Route::prefix('/users')->middleware('role:gerant')->group(function () {
        Route::get('/users', UserManager::class)->name('users.index');
        Route::get('/achats', GestionStockSimple::class)->name('achats.index');
        Route::get('/vente', GestionVente::class)->name('vente.produits');

    });
    
    Route::get('/vendeur/dashboard', [UserController::class, 'dashboard'])
        ->middleware('role:vendeur')
        ->name('vendeur.dashboard');
    
    Route::get('/gerant/dashboard', [UserController::class, 'dashboardgerant'])
        ->middleware('role:gerant')
        ->name('gerant.dashboard');
    
    Route::get('/superviseur/dashboard', [UserController::class, 'dashboardsuperviseur'])
        ->middleware('role:superviseur')
        ->name('superviseur.dashboard');
    
    
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    Route::middleware(['role:vendeur,gerant', 'verified'])->group(function () {
        // ... autres routes
        
        Route::get('/vendeur/produits', GestionVente::class)->name('vendeur.produits');
        Route::get('/vendeur', [HomeController::class, 'Sale'])->name('vendeur.stat');
        Route::get('/statistiques', LesVentes::class)->name('stats');
    });
    Route::get("/depenses", ExpenseManager::class)->middleware(['role:vendeur,gerant,superviseur'])->name("depenses");
// Dans routes/web.php
Route::get('/statistiques-utilisateurs', \App\Livewire\UserStatistics::class)
    ->middleware(['auth','role:gerant,superviseur',])
    ->name('user.statistics');
    Route::group(['prefix' => 'monnaie', 'middleware' => ['auth','role:gerant,superviseur','verified']], function () {
        Route::get("/", MonnaieManager::class)->name("monnaie.index");
        Route::get("/taux", TauxChangeManager::class)->name("taux");
    });
    Route::get('/ventes/{vente}/print-invoice', function(App\Models\Vente $vente) {
        $vente->load(['client', 'details.produit']);
        $logoPath = config('app.logo');
        $entreprise=[
                    'nom' => config('app.name'),
                    'adresse' => config('app.adresse', '123 Rue du Commerce'),
                    'telephone' => config('app.telephone', '+1234567890'),
                    'email' => config('app.email', 'contact@example.com'),
                    'site_web' => config('app.url'),
                    'logo' => (public_path($logoPath)) 
        ];
        
        $pdf = Pdf::loadView('pdf.invoice2', compact('vente','entreprise'))
                ->setPaper([0, 0, 226.77, 425.19]); // 80mm x 150mm
        
        // Affiche le PDF directement dans le navigateur
        return $pdf->stream("facture_{$vente->id}.pdf");
    })->name('ventes.print-invoice');
    
    
    
    
require __DIR__.'/auth.php';
