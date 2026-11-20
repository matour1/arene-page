@extends('layouts.admin')

@section('titre', "Lien d'affiliation")

@section('contenu')
    <a href="{{ route('admin.tournois.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-mut transition hover:text-cy">
        <x-icone nom="arrow-left" taille="sm" />
        Tournois
    </a>

    <h1 class="mt-4 flex items-center gap-2 text-xl font-bold text-txt sm:text-2xl">
        <x-icone nom="wallet" taille="lg" class="text-cy" />
        Lien d'affiliation
    </h1>
    <p class="mt-1 text-sm">{{ $tournoi->titrePropre() }}</p>

    @if ($errors->any())
        <div class="alerte alerte-err mt-5">
            <x-icone nom="alert" taille="sm" class="mt-0.5 shrink-0" />
            <ul class="list-inside list-disc space-y-0.5">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.tournois.update', $tournoi) }}"
              class="carte space-y-5 lg:col-span-2">
            @csrf
            @method('PUT')

            <label class="block">
                <span class="etiquette">Lien de parrainage pour ce tournoi</span>
                <input type="url"
                       name="lien_affiliation"
                       value="{{ old('lien_affiliation', $tournoi->lien_affiliation) }}"
                       placeholder="https://gamehub-arena.com/tournois/T-XXXXXXXX?ref=GH-XXXXXX"
                       class="champ font-mono !text-[0.8rem]">
                <span class="astuce mt-1.5">
                    Copiez ce lien depuis votre compte GameHub Arena : le lien personnalisé
                    n'est généré qu'après connexion. Les UTM sont ajoutés automatiquement.
                </span>
            </label>

            <div class="rounded-lg border border-line bg-elevated p-4">
                <p class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Lien effectivement utilisé</p>
                <p class="mt-2 break-all font-mono text-[0.75rem] text-cy">
                    {{ $tournoi->urlInscription('calendrier') }}
                </p>
                <p class="mt-2 text-xs">
                    @if ($tournoi->lien_affiliation)
                        Lien dédié à ce tournoi.
                    @else
                        Aucun lien dédié : repli sur <code>GAMEHUB_AFFILIATE_URL</code>,
                        puis sur la page publique (aucune commission).
                    @endif
                </p>
            </div>

            <div class="flex justify-end gap-2 border-t border-line pt-5">
                <a href="{{ route('admin.tournois.index') }}" class="btn btn-secondary btn-sm">Annuler</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <x-icone nom="check" taille="xs" />
                    Enregistrer
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <div class="carte !p-4">
                <p class="font-mono text-[0.68rem] uppercase tracking-wider text-dim">Informations</p>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt>Jeu</dt>
                        <dd class="font-semibold text-txt">{{ $tournoi->jeu }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt>Début</dt>
                        <dd class="chiffre text-[0.8rem] text-txt">{{ $tournoi->debut_at?->locale('fr')->translatedFormat('d M · H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt>Frais</dt>
                        <dd class="chiffre text-[0.8rem] text-txt">
                            {{ $tournoi->payant ? number_format($tournoi->frais_inscription, 0, ',', ' ').' '.$tournoi->devise : 'Gratuit' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt>Places</dt>
                        <dd class="chiffre text-[0.8rem] text-txt">{{ $tournoi->places_inscrits }}/{{ $tournoi->places_maximum }}</dd>
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

                <div class="carte !border-ok/30 !bg-ok/5">
                    <p class="flex items-center gap-1.5 font-mono text-[0.68rem] uppercase tracking-wider text-ok">
                        <x-icone nom="trending-up" taille="xs" />
                        Commission estimée
                    </p>
                    <p class="mt-2 text-sm leading-relaxed">
                        À {{ $tauxTournoi }} % (affiliation par tournoi), une inscription rapporte
                        <strong class="text-txt">{{ $fmt($parInscription) }}</strong>.<br>
                        Tournoi complet ({{ $tournoi->places_maximum }} places) :
                        <strong class="text-txt">{{ $fmt($plafond) }}</strong>.
                    </p>
                    <p class="mt-3 border-t border-ok/20 pt-3 text-[0.72rem] leading-relaxed">
                        Le parrainage global (code GH-XXXXXX) rapporte {{ $tauxGlobal }} %
                        sur la première inscription payante d'un filleul, une seule fois.
                    </p>
                </div>
            @endif
        </aside>
    </div>
@endsection