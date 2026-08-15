<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Tenant\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MunicipalityAppearanceController extends Controller
{
    public function edit(TenantManager $tenantManager): View
    {
        return view('admin.appearance', [
            'municipality' => $tenantManager->current(),
        ]);
    }

    public function update(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $municipality = $tenantManager->currentOrFail();

        $data = $request->validate([
            'brand_name' => ['required', 'string', 'max:80'],
            'brand_logo' => $this->safeLogoRules(),
            'hero_eyebrow' => ['required', 'string', 'max:80'],
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_description' => ['required', 'string', 'max:280'],
            'hero_image' => $this->safeImageRules(),
            'hero_image_alt' => ['nullable', 'string', 'max:180'],
            'hero_search_placeholder' => ['required', 'string', 'max:140'],
            'hero_card_title' => ['required', 'string', 'max:100'],
            'hero_card_tags' => ['nullable', 'string', 'max:160'],
            'local_economy_eyebrow' => ['required', 'string', 'max:80'],
            'local_economy_title' => ['required', 'string', 'max:120'],
            'local_economy_description' => ['required', 'string', 'max:360'],
            'local_economy_stat' => ['nullable', 'string', 'max:120'],
            'local_economy_link_label' => ['nullable', 'string', 'max:100'],
            'local_economy_link_url' => ['nullable', 'string', 'max:255'],
            'local_economy_image' => $this->safeImageRules(),
            'local_economy_image_alt' => ['nullable', 'string', 'max:180'],
        ]);

        if ($request->hasFile('brand_logo')) {
            $this->deleteStoredAsset($municipality->brand_logo_path);

            $data['brand_logo_path'] = $request
                ->file('brand_logo')
                ->store('municipalities/'.$municipality->slug.'/brand', 'public');
        }

        if ($request->hasFile('hero_image')) {
            $this->deleteStoredAsset($municipality->hero_image_path);

            $data['hero_image_path'] = $request
                ->file('hero_image')
                ->store('municipalities/'.$municipality->slug.'/home', 'public');
        }

        if ($request->hasFile('local_economy_image')) {
            $this->deleteStoredAsset($municipality->local_economy_image_path);

            $data['local_economy_image_path'] = $request
                ->file('local_economy_image')
                ->store('municipalities/'.$municipality->slug.'/home', 'public');
        }

        $data['hero_card_tags'] = collect(explode(',', $data['hero_card_tags'] ?? ''))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->take(4)
            ->values()
            ->all();

        unset($data['brand_logo'], $data['hero_image'], $data['local_economy_image']);

        $municipality->update($data);

        return redirect()
            ->route('admin.appearance.edit')
            ->with('status', 'Aparência da cidade atualizada com sucesso.');
    }

    private function deleteStoredAsset(?string $path): void
    {
        if (blank($path) || str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    /**
     * @return array<int, string>
     */
    private function safeImageRules(int $maxKilobytes = 4096): array
    {
        return [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:'.$maxKilobytes,
            'dimensions:min_width=320,min_height=180,max_width=6000,max_height=6000',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function safeLogoRules(): array
    {
        return [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
            'dimensions:min_width=64,min_height=64,max_width=3000,max_height=3000',
        ];
    }
}
