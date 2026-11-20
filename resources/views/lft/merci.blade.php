@extends('layouts.app')

@section('titre', 'Profil envoyé')

@section('contenu')
    <div class="mx-auto max-w-xl px-4 py-16 sm:px-6 sm:py-20">
        <div class="carte text-center !p-8">
            <div class="mx-auto grid size-14 place-items-center rounded-full border border-ok/40 bg-ok/10 text-ok">
                <x-icone nom="check" taille="xl" />
            </div>

            <h1 class="mt-5 text-xl font-bold text-txt sm:text-2xl">Profil envoyé</h1>

            <p class="mt-3 text-sm leading-relaxed">
                @if (session('pseudo'))
                    Merci <strong class="text-txt">{{ session('pseudo') }}</strong>.
                @endif
                Ton profil sera visible dès sa validation par notre équipe (sous 24 h).
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <a href="{{ route('lft.index') }}" class="btn btn-primary btn-sm">
                    <x-icone nom="users" taille="xs" />
                    Voir les profils
                </a>
                <a href="{{ route('tournois.index') }}" class="btn btn-secondary btn-sm">
                    <x-icone nom="trophy" taille="xs" />
                    Voir les tournois
                </a>
            </div>

            @if (config('arene.site.communaute_whatsapp'))
                <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                   class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-ok transition hover:text-txt">
                    <x-icone nom="whatsapp" taille="sm" />
                    Rejoindre la communauté WhatsApp
                </a>
            @endif
        </div>
    </div>
@endsection