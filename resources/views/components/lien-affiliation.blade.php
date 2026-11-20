@props([
    'href',
    'variant' => 'primary',
    'taille' => 'md',
])

@php
    $variantes = [
        'primary' => 'btn-primary',
        'neon' => 'btn-neon',
        'secondaire' => 'btn-secondary',
        'danger' => 'btn-danger',
    ];

    $tailles = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];

    $classeVariante = $variantes[$variant] ?? $variantes['primary'];
    $classeTaille = $tailles[$taille] ?? '';

    $classes = trim("btn {$classeVariante} {$classeTaille}");
@endphp

{{-- Lien de parrainage sortant : rel sponsored + UTM pour le tracking. --}}
<a href="{{ $href }}"
   target="_blank"
   rel="sponsored noopener noreferrer"
   {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>