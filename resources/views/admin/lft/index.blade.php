@extends('layouts.admin')

@section('titre', 'Profils LFT')

@section('contenu')
    <h1 class="text-2xl font-extrabold tracking-tight">Profils LFT</h1>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach (['' => 'Tous', 'en_attente' => 'En attente', 'valide' => 'Validés', 'rejete' => 'Rejetés'] as $valeur => $libelle)
            <a href="{{ route('admin.lft.index', $valeur ? ['statut' => $valeur] : []) }}"
               class="rounded-lg px-3 py-1.5 text-xs font-semibold
                      {{ $statut === ($valeur ?: null) ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700 hover:bg-slate-50' }}">
                {{ $libelle }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($profils as $profil)
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-base font-extrabold">{{ $profil->pseudo }}</p>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase
                                {{ match ($profil->statut) {
                                    'valide' => 'bg-emerald-100 text-emerald-800',
                                    'rejete' => 'bg-red-100 text-red-700',
                                    default => 'bg-amber-100 text-amber-800',
                                } }}">
                                {{ str_replace('_', ' ', $profil->statut) }}
                            </span>
                            @if ($profil->disponible)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-600">dispo</span>
                            @endif
                        </div>

                        <p class="mt-1.5 text-sm text-slate-600">
                            {{ $profil->jeu }}
                            @if ($profil->role) · {{ $profil->role }} @endif
                            @if ($profil->rang) · {{ $profil->rang }} @endif
                            · {{ $profil->pays }} · {{ strtoupper($profil->langue) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            @if ($profil->contact_discord) Discord : {{ $profil->contact_discord }} @endif
                            @if ($profil->contact_whatsapp) · WhatsApp : {{ $profil->contact_whatsapp }} @endif
                        </p>

                        @if ($profil->description)
                            <p class="mt-2 text-sm text-slate-600">{{ $profil->description }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if ($profil->statut !== 'valide')
                            <form method="POST" action="{{ route('admin.lft.valider', $profil) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                    Valider
                                </button>
                            </form>
                        @endif

                        @if ($profil->statut !== 'rejete')
                            <form method="POST" action="{{ route('admin.lft.rejeter', $profil) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Rejeter
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.lft.destroy', $profil) }}"
                              onsubmit="return confirm('Supprimer définitivement ce profil ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-sm text-slate-500">
                Aucun profil pour ce filtre.
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $profils->links() }}</div>
@endsection
