<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilLft;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilLftAdminController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->string('statut')->toString() ?: null;

        $profils = ProfilLft::query()
            ->when($statut, fn ($q, $v) => $q->where('statut', $v))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.lft.index', compact('profils', 'statut'));
    }

    public function valider(ProfilLft $profil)
    {
        $profil->update(['statut' => 'valide', 'valide_at' => now()]);

        return back()->with('statut', "Profil « {$profil->pseudo} » validé.");
    }

    public function rejeter(ProfilLft $profil)
    {
        $profil->update(['statut' => 'rejete', 'valide_at' => null]);

        return back()->with('statut', "Profil « {$profil->pseudo} » rejeté.");
    }

    public function destroy(ProfilLft $profil)
    {
        $profil->delete();

        return back()->with('statut', 'Profil supprimé.');
    }
}
