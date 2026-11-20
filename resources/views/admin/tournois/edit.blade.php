@extends('layouts.admin')

@section('titre', "Lien d'affiliation")

@section('contenu')
    <a href="{{ route('admin.tournois.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900">
        <x-icone nom="arrow-left" taille="sm" />
        Tournois
    </a>

    <h1 class="mt-4 flex items-center gap-2 text-2xl font-extrabold tracking-tight">
        <x-icone nom="wallet" taille="lg" class="text-orange-600" />
        Lien d'affiliation
    </h1>
    <p class="mt-1 text-sm text-slate-600">{{ $tournoi->titre }}</p>

    @if ($errors->any())
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4">
            <ul class="list-inside list-disc space-y-1 text-sm text-red-700">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.tournois.update', $tournoi) }}"
              class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-2">
            @csrf
            @method('PUT')

            <label class="block">
                <span class="etiquette">Lien de parrainage pour ce tournoi</span>
                <input type="url"
                       name="lien_affiliation"
                       value="{{ old('lien_affiliation', $tournoi->lien_affiliation) }}"
                       placeholder="https://gamehub-arena.com/tournois/T-XXXXXXXX?ref=GH-XXXXXX"
                       class="champ font-mono text-xs">
                <span class="mt-1.5 block text-xs leading-relaxed text-slate-500">
                    Copiez ce lien depuis votre compte GameHub Arena : le lien de parrainage
                    personnalisé n'est généré qu'après connexion. Les paramètres UTM sont
                    ajoutés automatiquement, sans écraser ceux déjà présents.
                </span>
            </label>

            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Lien effectivement utilisé</p>
                <p class="mt-2 break-all font-mono text-xs text-slate-700">
                    {{ $tournoi->urlInscription('calendrier') }}
                </p>
                <p class="mt-2 text-xs text-slate-500">
                    @if ($tournoi->lien_affiliation)
                        Lien dédié à ce tournoi.
                    @else
                        Aucun lien dédié : repli sur <code>GAMEHUB_AFFILIATE_URL</code>, puis sur la page publique du tournoi (aucune commission).
                    @endif
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('admin.tournois.index') }}"
                   class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    <x-icone nom="check" taille="sm" />
                    Enregistrer
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Informations</p>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Jeu</dt>
                        <dd class="font-semibold">{{ $tournoi->jeu }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Début</dt>
                        <dd class="font-semibold">{{ $tournoi->debut_at?->locale('fr')->translatedFormat('d M · H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Frais</dt>
                        <dd class="font-semibold">
                            {{ $tournoi->payant ? number_format($tournoi->frais_inscription, 0, ',', ' ').' '.$tournoi->devise : 'Gratuit' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Places</dt>
                        <dd class="font-semibold tabular-nums">{{ $tournoi->places_inscrits }}/{{ $tournoi->places_maximum }}</dd>
                    </div>
                </dl>
            </div>

            @if ($tournoi->payant && $tournoi->frais_inscription > 0)
                @php
                    $tauxTournoi = (float) config('arene.affiliation.commission_tournoi', 2);
                    $tauxGlobal = (float) config('arene.affiliation.commission_globale', 5);
                    $parInscription = $tournoi->frais_inscription * $tauxTournoi / 100;
                    $plafond = $parInscription * $tournoi->places_maximum;
                    $fmt = fn (float $v) => number_format($v, 0, ',', ' ').' '.$tournoi->devise;
                @endphp

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-emerald-700">
                        <x-icone nom="trending-up" taille="xs" />
                        Commission estimée
                    </p>
                    <p class="mt-2 text-sm leading-relaxed text-emerald-900">
                        À {{ $tauxTournoi }} % (affiliation par tournoi), une inscription rapporte
                        <strong>{{ $fmt($parInscription) }}</strong>.<br>
                        Tournoi complet ({{ $tournoi->places_maximum }} places) :
                        <strong>{{ $fmt($plafond) }}</strong>.
                    </p>
                    <p class="mt-3 border-t border-emerald-200 pt-3 text-xs leading-relaxed text-emerald-800/80">
                        Le parrainage global (code GH-XXXXXX) rapporte {{ $tauxGlobal }} %
                        sur la première inscription payante d'un filleul, une seule fois par filleul.
                        Les deux dispositifs ne se cumulent pas sur la même inscription.
                    </p>
                </div>
            @endif
        </aside>
    </div>
@endsection