<?php

namespace App\Http\Controllers;

use App\Models\Monnaie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonnaieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $monnaies = Monnaie::all();
        //$monnaies = Monnaie::where('statut', '1')->first();
        //return $monnaies;
        return view('monnaie.index', compact('monnaies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|unique:monnaies,libelle|max:255',
        ]);

        if (Auth::user()->hasRole("admin")) {
            $m = new Monnaie();
            $m->libelle = $request->nom;
            $m->save();
            if($m){
                toastr()->success($this->messageSave, 'Gestion monnaie');
                return back();
            }else{
                toastr()->error($this->messageError, 'Gestion monnaie');
                return back();
            }
        } else {
            toastr()->error($this->messageCheckPermision);
            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $monnaie = Monnaie::find($id);
        if ($monnaie == null){
            toastr()->error("Impossible de traiter cette rêquette",'Gestion de utilisateur');
            return back();
        }
        return view('monnaie.edit', compact('monnaie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nom' => 'required|max:255',
        ]);

        if (Auth::user()->hasRole("admin")) {
            $m = Monnaie::find($id);
            if ($m == null){
                toastr()->error("Impossible de traiter cette rêquette",'Gestion de utilisateur');
                return back();
            }
            $m->libelle = $request->nom;
            $m->save();
            if($m){
                toastr()->success($this->messageUpdate, 'Gestion monnaie');
                return back();
            }else{
                toastr()->error("Error lors de l'enregistrement", 'Gestion monnaie');
                return back();
            }
        } else {
            toastr()->error($this->messageCheckPermision);
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function active($id)
    {
        if (Auth::user()->hasRole("admin")) {
            $m = Monnaie::find($id);
            if ($m == null){
                toastr()->error("Impossible de traiter cette rêquette",'Getion de utilisateur');
                return back();
            }
            if ($m->statut === '0' ){
                $m->statut = '1';
            }else if ($m->statut === '1'){
                $m->statut = '0';
            }
            $m->save();
            if($m){
                toastr()->success($this->messageUpdate, 'Gestion monnaie');
                return back();
            }else{
                toastr()->error($this->messageError, 'Gestion monnaie');
                return back();
            }
        } else {
            toastr()->error($this->messageCheckPermision);
            return back();
        }
    }
}
