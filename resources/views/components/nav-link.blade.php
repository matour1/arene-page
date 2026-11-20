@props([
    'actif' => false,
    'href' => '#',
])

<a href="{{ $href }}"
   class="rounded-lg border-l-2 px-3 py-2 text-sm font-semibold transition duration-150
          {{ $actif
                ? 'border-cy bg-cy/5 text-cy'
                : 'border-transparent text-mut hover:bg-elevated hover:text-txt' }}"
   aria-current="{{ $actif ? 'page' : 'false' }}">
    {{ $slot }}
</a>