@props([
    'actif' => false,
    'href' => '#',
])

<a href="{{ $href }}"
   class="px-3 py-2 rounded-lg text-sm font-semibold transition
          {{ $actif
                ? 'bg-slate-900 text-white'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
   aria-current="{{ $actif ? 'page' : 'false' }}">
    {{ $slot }}
</a>
