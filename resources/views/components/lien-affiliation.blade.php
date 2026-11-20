@props([
    'href',
    'variant' => 'primary',
    'taille' => 'md',
])

@php
    $classes = [
        'primary' => 'bg-orange-600 text-white hover:bg-orange-700 shadow-sm',
        'secondaire' => 'bg-white text-slate-800 border border-slate-300 hover:bg-slate-50',
        'discret' => 'bg-slate-900 text-white hover:bg-slate-800',
    ][$variant] ?? 'bg-orange-600 text-white hover:bg-orange-700';

    $tailles = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ][$taille] ?? 'px-4 py-2 text-sm';
@endphp

{{-- Lien de parrainage sortant : rel sponsored + UTM pour le tracking. --}}
<a href="{{ $href }}"
   target="_blank"
   rel="sponsored noopener noreferrer"
   {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition {$classes} {$tailles}"]) }}>
    {{ $slot }}
</a>
