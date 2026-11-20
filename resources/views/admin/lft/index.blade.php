@extends('layouts.admin')

@section('titre', 'Profils LFT')

@section('contenu')
    <div>
        <span class="font-mono text-[0.72rem] uppercase tracking-[0.15em] text-cy">Moderation</span>
        <h1 class="mt-1 flex items-center gap-2 text-xl font-bold text-txt sm:text-2xl">
            <x-icone nom="users" taille="lg" class="text-cy" />
            Profils LFT
        </h1>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach (['' => 'Tous', 'en_attente' => 'En attente', 'valide' => 'Validés', 'rejete' => 'Rejetés'] as $valeur => $libelle)
            <a href="{{ route('admin.lft.index', $valeur ? ['statut' => $valeur] : []) }}"
               class="btn btn-sm {{ $statut === ($valeur ?: null) ? 'btn-primary' : 'btn-secondary' }}">
                {{ $libelle }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($profils as $profil)
            <div class="carte !p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[0.95rem] font-bold text-txt">{{ $profil->pseudo }}</p>

                            @if ($profil->statut === 'valide')
                                <span class="badge badge-ok">Validé</span>
                            @elseif ($profil->statut === 'rejete')
                                <span class="badge badge-danger">Rejeté</span>
                            @else
                                <span class="badge badge-yellow">En attente</span>
                            @endif

                            @if ($profil->disponible)
                                <span class="badge badge-neutral">Dispo</span>
                            @endif
                        </div>

                        <p class="mt-1.5 text-sm">
                            {{ $profil->jeu }}
                            @if ($profil->role) · {{ $profil->role }} @endif
                            @if ($profil->rang) · {{ $profil->rang }} @endif
                            · {{ $profil->pays }} · {{ strtoupper($profil->langue) }}
                        </p>

                        <p class="mt-1 font-mono text-[0.72rem] text-dim">
                            @if ($profil->contact_discord) Discord : {{ $profil->contact_discord }} @endif
                            @if ($profil->contact_whatsapp) · WhatsApp : {{ $profil->contact_whatsapp }} @endif
                        </p>

                        @if ($profil->description)
                            <p class="mt-2 text-sm">{{ $profil->description }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if ($profil->statut !== 'valide')
                            <form method="POST" action="{{ route('admin.lft.valider', $profil) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <x-icone nom="check" taille="xs" />
                                    Valider
                                </button>
                            </form>
                        @endif

                        @if ($profil->statut !== 'rejete')
                            <form method="POST" action="{{ route('admin.lft.rejeter', $profil) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <x-icone nom="x" taille="xs" />
                                    Rejeter
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.lft.destroy', $profil) }}"
                              onsubmit="return confirm('Supprimer définitivement ce profil ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <x-icone nom="trash" taille="xs" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="carte px-6 py-12 text-center">
                <x-icone nom="users" taille="xl" class="mx-auto text-dim" />
                <p class="mt-3 text-sm font-semibold text-txt">Aucun profil pour ce filtre.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $profils->links() }}</div>
@endsection