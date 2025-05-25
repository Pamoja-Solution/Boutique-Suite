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
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\InventaireController;
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
use App\Livewire\GestionCodesBarres;
use App\Livewire\GestionStockSimple;
use App\Livewire\GestionTauxChange;
use App\Livewire\LesVentes;
use App\Livewire\MonnaieManager;
use App\Livewire\SalesReport;
use App\Livewire\TauxChangeManager;
use App\Models\Monnaie;

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
        Route::get('/rapports', SalesReport::class)->name('rapports');
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
        
        $entreprise = [
            'nom' => config('app.name'),
            'adresse' => config('app.adresse'),
            'telephone' => config('app.telephone'),
            'rccm' => config('app.rccm'),
            'site_web' => config('app.url'),
            'logo' => public_path(config('app.logo'))
        ];
    
        // Calcul précis de la hauteur (en points - 1mm = 2.83 points)
        $baseHeight = 150; // Hauteur de base en mm (sans articles)
        $perItemHeight = 6; // Hauteur par article en mm
        $totalHeight = $baseHeight + (count($vente->details) * $perItemHeight);
        
        // Conversion mm en points (1mm = 2.83 points)
        $widthInPoints = 80 * 2.83;  // 80mm en points
        $heightInPoints = $totalHeight * 2.83;
        $monnaie= Monnaie::where('code', 'USD')->first();
        
        $pdf = Pdf::loadView('pdf.invoice2', compact('vente','entreprise','monnaie'))
                ->setPaper([0, 0, $widthInPoints, $heightInPoints], 'portrait')
                ->setOption('margin-top', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0);
    
        return $pdf->stream("facture_{$vente->matricule}.pdf");
    })->name('ventes.print-invoice');
    


    Route::get('/ventes/{vente}/direct-print', function(App\Models\Vente $vente) {
        $vente->load(['client', 'details.produit']);
        
        $entreprise = [
            'nom' => config('app.name'),
            'adresse' => config('app.adresse'),
            'telephone' => config('app.telephone'),
            'email' => config('app.email'),
            'site_web' => config('app.url'),
            'logo' => public_path(config('app.logo'))
        ];
    
        // Calcul précis de la hauteur (en points - 1mm = 2.83 points)
        $baseHeight = 150; // Hauteur de base en mm (sans articles)
        $perItemHeight = 3; // Hauteur par article en mm
        $totalHeight = $baseHeight + (count($vente->details) * $perItemHeight);
        
        // Conversion mm en points (1mm = 2.83 points)
        $widthInPoints = 80 * 2.83;  // 80mm en points
        $heightInPoints = $totalHeight * 2.83;
    
        $pdf = Pdf::loadView('pdf.invoice2', compact('vente','entreprise'))
                ->setPaper([0, 0, $widthInPoints, $heightInPoints], 'portrait')
                ->setOption('margin-top', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0)
                ->setOption('margin-right', 0);
        return $pdf->stream("facture_{$vente->matricule}.pdf");
    })->name('ventes.direct-print');


 
    Route::middleware(['auth'])->group(function () {
        // Autres routes...
        
        // Routes pour la gestion des inventaires
        Route::prefix('inventaires')->name('inventaires.')->group(function () {
            Route::get('/', [InventaireController::class, 'index'])->name('index');
            Route::get('/create', [InventaireController::class, 'create'])->name('create');
            Route::get('/{id}', [InventaireController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [InventaireController::class, 'edit'])->name('edit');
            Route::get('/{inventaireId}/mouvements', [InventaireController::class, 'mouvements'])->name('mouvements');
        });
    });
    
    

    // Route pour afficher la page du scanner
Route::get('/scanner', [BarcodeController::class, 'index'])->name('scanner.index');

// Route pour traiter les scans
Route::post('/scan-barcode', [BarcodeController::class, 'processScan'])->name('scan.barcode');
    //Route::get('/gestion-codes-barres', GestionCodesBarres::class)->name('gestion.codes-barres');
    // routes/web.php
Route::get('/produits/codes-barres', \App\Livewire\ProduitsCodeBarres::class)->name('gestion.codes-barres');
require __DIR__.'/auth.php';
