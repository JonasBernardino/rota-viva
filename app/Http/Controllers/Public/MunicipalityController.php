<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

class MunicipalityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'municipality' => ['required', 'string'],
        ]);

        /** @var stdClass|null $municipality */
        $municipality = DB::table($this->platformTable('municipios'))
            ->where('slug', $data['municipality'])
            ->where('status', 'active')
            ->first();

        if ($municipality === null) {
            throw ValidationException::withMessages([
                'municipality' => 'Município não encontrado ou inativo.',
            ]);
        }

        $domains = DB::table($this->platformTable('dominios_municipios'))
            ->where('municipio_id', $municipality->id)
            ->orderByDesc('is_principal')
            ->pluck('dominio');

        $domain = $this->preferredDomain($municipality->slug, $domains, $request);

        if ($domain === null) {
            return redirect()->route('home', ['_tenant' => $municipality->slug]);
        }

        return redirect()->away($this->urlForDomain($domain, $request));
    }

    /**
     * @param  Collection<int, string>  $domains
     */
    private function preferredDomain(string $municipalitySlug, Collection $domains, Request $request): ?string
    {
        $currentHost = $this->normalizedHost($request);
        $domains = $domains
            ->map(fn (string $domain): string => strtolower($domain))
            ->values();

        $localRotaVivaDomain = 'rota-viva.'.$municipalitySlug.'.test';
        if ($this->isLocalTestHost($currentHost) && $domains->contains($localRotaVivaDomain)) {
            return $localRotaVivaDomain;
        }

        if ($domains->contains($currentHost) && str_starts_with($currentHost, $municipalitySlug.'.')) {
            return $currentHost;
        }

        $currentParts = explode('.', $currentHost);
        $currentBase = count($currentParts) >= 2
            ? implode('.', array_slice($currentParts, -2))
            : $currentHost;

        $sameBaseDomain = $domains->first(
            fn (string $domain): bool => str_ends_with($domain, '.'.$currentBase)
        );

        if (is_string($sameBaseDomain)) {
            return $sameBaseDomain;
        }

        $slugDomain = $domains->first(
            fn (string $domain): bool => str_starts_with($domain, $municipalitySlug.'.')
        );

        if (is_string($slugDomain)) {
            return $slugDomain;
        }

        return $domains->first();
    }

    private function isLocalTestHost(string $host): bool
    {
        return $host === 'rota-viva.test'
            || str_ends_with($host, '.rota-viva.test')
            || str_starts_with($host, 'rota-viva.')
            || str_ends_with($host, '.test');
    }

    private function platformTable(string $table): string
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            return 'public.'.$table;
        }

        return $table;
    }

    private function urlForDomain(string $domain, Request $request): string
    {
        $port = $request->getPort();
        $scheme = $request->getScheme();
        $host = $this->normalizedHost($request);
        $portSuffix = '';

        if (
            (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1'))
            && (str_contains($domain, 'localhost') || str_contains($domain, '127.0.0.1'))
        ) {
            $portSuffix = $port === 80 ? '' : ':'.$port;
        }

        return "{$scheme}://{$domain}{$portSuffix}";
    }

    private function normalizedHost(Request $request): string
    {
        $host = strtolower($request->headers->get('host', $request->getHost()));

        if (str_contains($host, ':')) {
            return explode(':', $host)[0];
        }

        return $host;
    }
}
