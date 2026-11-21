{{-- Metadonnees SEO : titre, description, canonical, Open Graph, Twitter Card
     et donnees structurees schema.org. Le controleur peut surcharger chaque
     champ via App\Services\Seo\Metadonnees. --}}

<title>{{ $seo->titreComplet() }}</title>
<meta name="description" content="{{ $seo->descriptionFinale() }}">

@if ($seo->estNoindex())
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
@endif

<link rel="canonical" href="{{ $seo->urlCanonical() }}">

{{-- Langues : le site est monolingue, mais on declare les variantes hreflang
     pour eviter que les moteurs devinent. --}}
<link rel="alternate" hreflang="fr" href="{{ $seo->urlCanonical() }}">
<link rel="alternate" hreflang="x-default" href="{{ $seo->urlCanonical() }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ config('arene.site.nom') }}">
<meta property="og:type" content="{{ $seo->typePage() }}">
<meta property="og:title" content="{{ $seo->titreComplet() }}">
<meta property="og:description" content="{{ $seo->descriptionFinale() }}">
<meta property="og:url" content="{{ $seo->urlCanonical() }}">
<meta property="og:locale" content="{{ config('arene.seo.langue') }}">
<meta property="og:image" content="{{ $seo->urlImage() }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $seo->titreComplet() }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->titreComplet() }}">
<meta name="twitter:description" content="{{ $seo->descriptionFinale() }}">
<meta name="twitter:image" content="{{ $seo->urlImage() }}">
@if (config('arene.seo.twitter'))
    <meta name="twitter:site" content="{{ config('arene.seo.twitter') }}">
@endif

{{-- Verifications de propriete (Search Console / Bing). --}}
@if (config('arene.seo.google_verification'))
    <meta name="google-site-verification" content="{{ config('arene.seo.google_verification') }}">
@endif
@if (config('arene.seo.bing_verification'))
    <meta name="msvalidate.01" content="{{ config('arene.seo.bing_verification') }}">
@endif

@if ($seo->estNoindex())
    {{-- Aucune donnee structuree sur une page non indexable. --}}
@else
    @foreach (array_merge($seo->schemasGlobaux(), $seo->schemas()) as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endforeach
@endif