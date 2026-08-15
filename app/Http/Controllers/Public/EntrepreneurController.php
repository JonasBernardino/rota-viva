<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Estabelecimento;
use App\Services\Tenant\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EntrepreneurController extends Controller
{
    public function create(): View
    {
        return view('pages.entrepreneurs');
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $tenantManager->currentOrFail();

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'tipo_estabelecimento' => ['required', 'in:hospedagem,gastronomia,atividade,guia_turistico,artesanato,produtor_cultural,outro'],
            'descricao' => ['required', 'string', 'max:1200'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'instagram' => ['nullable', 'string', 'max:80'],
            'website' => ['nullable', 'url', 'max:255'],
            'faixa_preco' => ['required', 'in:$,$$,$$$,$$$$'],
            'responsavel_nome' => ['required', 'string', 'max:255'],
            'responsavel_email' => ['required', 'email', 'max:255'],
            'imagem' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                'dimensions:min_width=320,min_height=180,max_width=6000,max_height=6000',
            ],
            'aceite_privacidade' => ['accepted'],
        ]);

        $imagePath = $request->hasFile('imagem')
            ? $request->file('imagem')->store('entrepreneur-submissions', 'public')
            : null;

        try {
            Estabelecimento::create([
                'nome' => $data['nome'],
                'slug' => str($data['nome'].'-'.uniqid())->slug(),
                'tipo_estabelecimento' => $data['tipo_estabelecimento'],
                'descricao' => $data['descricao'],
                'endereco' => $data['endereco'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'website' => $data['website'] ?? null,
                'faixa_preco' => $data['faixa_preco'],
                'tem_selo_qualidade' => false,
                'status_validacao' => 'pending',
                'notas_validacao' => 'Solicitação pública enviada por '.$data['responsavel_nome'].' <'.$data['responsavel_email'].'>. Publicação depende de validação municipal.',
                'validado_em' => null,
                'imagem_capa' => $imagePath,
            ]);
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('entrepreneurs.create')
            ->with('status', 'Solicitação enviada. A gestão municipal fará a validação antes de publicar no portal.');
    }
}
