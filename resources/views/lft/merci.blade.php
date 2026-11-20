@extends('layouts.app')

@section('titre', 'Profil envoyé')

@section('contenu')
    <div class="mx-auto max-w-xl px-4 py-20 sm:px-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <x-icone nom="check" taille="xl" />
            </div>

            <h1 class="mt-5 text-2xl font-extrabold tracking-tight">Profil envoyé</h1>

            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                @if (session('pseudo'))
                    Merci <strong>{{ session('pseudo') }}</strong>.
                @endif
                Ton profil sera visible dès sa validation par notre équipe (sous 24 h).
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('lft.index') }}" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Voir les profils
                </a>
                <a href="{{ route('tournois.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                    Voir les tournois
                </a>
            </div>

            @if (config('arene.site.communaute_whatsapp'))
                <a href="{{ config('arene.site.communaute_whatsapp') }}" target="_blank" rel="noopener"
                   class="mt-6 inline-block text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                    Rejoindre la communauté WhatsApp →
                </a>
            @endif
        </div>
    </div>
@endsection
