@php
    $brandMunicipality = $currentTenant ?? null;
    $brandName = $homeContent['brand_name'] ?? $brandMunicipality?->brandName() ?? 'ROTA VIVA';
    $brandLogoUrl = $homeContent['brand_logo_url'] ?? $brandMunicipality?->brandLogoUrl();
@endphp

<a class="brand brand--with-logo {{ $light ?? false ? 'brand--light' : '' }}" href="{{ route('home') }}" aria-label="{{ $brandName }} — página inicial">
    @if ($brandLogoUrl)
        <img class="brand__logo" src="{{ $brandLogoUrl }}" alt="" aria-hidden="true">
    @endif

    <span>{{ $brandName }}</span>
</a>
