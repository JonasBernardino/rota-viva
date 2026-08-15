<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use App\Models\User;
use App\Services\Tenant\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function dashboard(): View
    {
        return view('platform.dashboard', [
            'municipalities' => Municipio::with('dominios')
                ->withCount('dominios')
                ->orderBy('nome')
                ->get(),
        ]);
    }

    public function createMunicipality(): View
    {
        return view('platform.municipality-form');
    }

    public function storeMunicipality(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:80', 'alpha_dash', 'unique:municipios,slug'],
            'codigo_ibge' => ['nullable', 'string', 'max:20', 'unique:municipios,codigo_ibge'],
            'uf' => ['required', 'string', 'size:2'],
            'dominio' => ['required', 'string', 'max:255', 'unique:dominios_municipios,dominio'],
            'gestor_nome' => ['required', 'string', 'max:255'],
            'gestor_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'gestor_senha' => ['required', 'string', 'min:8'],
        ]);

        $slug = Str::slug($data['slug'] ?: $data['nome']);
        $legacySchemaName = 'tenant_'.str_replace('-', '_', $slug);
        $domain = $this->normalizeDomain($data['dominio']);

        if (Municipio::where('slug', $slug)->exists()) {
            return back()
                ->withErrors(['slug' => 'Já existe um município com este identificador.'])
                ->withInput();
        }

        $municipality = $tenantManager->createTenant([
            'nome' => $data['nome'],
            'slug' => $slug,
            'codigo_ibge' => $data['codigo_ibge'] ?: null,
            'uf' => strtoupper($data['uf']),
            'nome_schema' => $legacySchemaName,
            'status' => 'active',
            'fuso_horario' => 'America/Fortaleza',
            'configuracoes' => [
                'created_from_platform_panel' => true,
            ],
        ], [$domain]);

        User::create([
            'name' => $data['gestor_nome'],
            'email' => $data['gestor_email'],
            'password' => Hash::make($data['gestor_senha']),
            'municipio_id' => $municipality->id,
            'can_access_admin_panel' => true,
            'can_manage_platform' => false,
        ]);

        $tenantManager->reset();

        return redirect()
            ->route('platform.dashboard')
            ->with('status', 'Município criado com domínio, partição lógica e gestor municipal.');
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain) ?? $domain;

        return rtrim($domain, '/');
    }
}
