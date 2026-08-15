<?php

namespace App\Http\Middleware;

use App\Models\Municipio;
use App\Services\Tenant\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantMiddleware
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantIdentifier = $request->header('X-Tenant') ?? $request->query('_tenant');

        if ($tenantIdentifier) {
            /** @var Municipio|null $municipality */
            $municipality = Municipio::where('slug', $tenantIdentifier)
                ->orWhere('uuid', $tenantIdentifier)
                ->first();
        } else {
            $municipality = $this->tenantManager->resolveByDomain($request->getHost());
        }

        if ($municipality && $municipality->isActive()) {
            $this->tenantManager->switchTo($municipality);
            $request->attributes->set('tenant', $municipality);
        }

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $this->tenantManager->reset();
    }
}
