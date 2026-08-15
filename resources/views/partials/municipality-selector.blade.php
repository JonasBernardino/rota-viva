<form class="municipality-selector" action="{{ route('municipalities.select') }}" method="get">
    <label class="visually-hidden" for="municipality-selector">Selecionar município</label>

    <span class="municipality-selector__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
    </span>

    <select id="municipality-selector" name="municipality" aria-label="Selecionar município" onchange="this.form.submit()">
        <option value="">Selecione o município</option>
        @foreach ($municipalityOptions ?? [] as $municipality)
            <option value="{{ $municipality->slug }}" @selected(($currentTenant?->slug ?? null) === $municipality->slug)>
                {{ $municipality->nome }}
            </option>
        @endforeach
    </select>

    <noscript>
        <button class="utility-button" type="submit">Selecionar</button>
    </noscript>
</form>
