<?php

namespace Database\Seeders;

use App\Models\Atrativo;
use App\Models\Categoria;
use App\Models\Estabelecimento;
use App\Models\Evento;
use App\Models\HorarioAtrativo;
use App\Models\ItemRoteiro;
use App\Models\Municipio;
use App\Models\PreferenciaVisitante;
use App\Models\RecursoAcessibilidade;
use App\Models\Roteiro;
use App\Models\User;
use App\Services\Tenant\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LucenaDemoSeeder extends Seeder
{
    public function run(TenantManager $tenantManager): void
    {
        // 1. Garante que o Município existe no cadastro global
        $municipality = Municipio::where('slug', 'lucena')->first();

        if (! $municipality) {
            $municipality = $tenantManager->createTenant([
                'nome' => 'Lucena',
                'slug' => 'lucena',
                'codigo_ibge' => '2508903',
                'uf' => 'PB',
                'nome_schema' => 'tenant_lucena',
                'status' => 'active',
                'fuso_horario' => 'America/Fortaleza',
                'configuracoes' => [
                    'hero_title' => 'Descubra Lucena',
                    'tagline' => 'História, cultura e o mar da Paraíba',
                    'contact_email' => 'turismo@lucena.pb.gov.br',
                    'emergency_phone' => '(83) 3293-1000',
                ],
            ], [
                'lucena.rota-viva.test',
                'lucena.rotaviva.com.br',
                'localhost',
                '127.0.0.1',
            ]);
        }

        // Define o município atual para popular dados com municipio_id
        $tenantManager->switchTo($municipality);
        app(TenantManager::class)->switchTo($municipality);

        try {
            // 2. Categorias
            $catHistory = Categoria::updateOrCreate(['slug' => 'patrimonio-historico'], [
                'nome' => 'Patrimônio Histórico',
            ]);

            $catNature = Categoria::updateOrCreate(['slug' => 'sol-e-natureza'], [
                'nome' => 'Sol e Natureza',
            ]);

            $catCulture = Categoria::updateOrCreate(['slug' => 'cultura-e-tradicao'], [
                'nome' => 'Cultura e Tradição',
            ]);

            $catGastronomy = Categoria::updateOrCreate(['slug' => 'gastronomia-litoranea'], [
                'nome' => 'Gastronomia Litorânea',
            ]);

            $catEcotourism = Categoria::updateOrCreate(['slug' => 'ecoturismo-e-trilhas'], [
                'nome' => 'Ecoturismo e Trilhas',
            ]);

            $catBeaches = Categoria::updateOrCreate(['slug' => 'praias-e-lazer'], [
                'nome' => 'Praias e Lazer',
            ]);

            // 3. Recursos de Acessibilidade
            $featRamp = RecursoAcessibilidade::updateOrCreate(['slug' => 'rampa-de-acesso'], [
                'nome' => 'Rampa de Acesso',
            ]);

            $featBathroom = RecursoAcessibilidade::updateOrCreate(['slug' => 'banheiro-adaptado'], [
                'nome' => 'Banheiro Adaptado',
            ]);

            $featLibras = RecursoAcessibilidade::updateOrCreate(['slug' => 'atendimento-em-libras'], [
                'nome' => 'Atendimento em Libras',
            ]);

            $featTactile = RecursoAcessibilidade::updateOrCreate(['slug' => 'piso-tatil'], [
                'nome' => 'Piso Tátil',
            ]);

            $featAudio = RecursoAcessibilidade::updateOrCreate(['slug' => 'audiodescricao'], [
                'nome' => 'Audiodescrição',
            ]);

            $featBraille = RecursoAcessibilidade::updateOrCreate(['slug' => 'sinalizacao-braille'], [
                'nome' => 'Sinalização em Braille',
            ]);

            // 4. Atrativos Oficiais de Lucena
            $pGuia = Atrativo::updateOrCreate(['slug' => 'igreja-nossa-senhora-da-guia'], [
                'categoria_id' => $catHistory->id,
                'nome' => 'Igreja de Nossa Senhora da Guia',
                'descricao' => 'Um dos mais expressivos e antigos templos católicos do Brasil, construído no século XVI pelos carmelitas sobre uma colina. Destaca-se pelo estilo barroco tropical e vista panorâmica para a foz do Rio Paraíba.',
                'latitude' => -7.0182000,
                'longitude' => -34.8724000,
                'is_ar_livre' => false,
                'adequado_criancas' => true,
                'duracao_minutos' => 60,
                'custo_medio' => 0.00,
                'is_disponivel' => true,
                'intensidade' => 'low',
                'tags' => ['histórico', 'barroco', 'religioso', 'vista panorâmica', 'século XVI'],
            ]);
            $pGuia->recursosAcessibilidade()->sync([$featRamp->id, $featBathroom->id, $featAudio->id]);

            HorarioAtrativo::updateOrCreate(['atrativo_id' => $pGuia->id, 'dia_semana' => 1], ['abre_as' => '08:00', 'fecha_as' => '17:00']);
            HorarioAtrativo::updateOrCreate(['atrativo_id' => $pGuia->id, 'dia_semana' => 2], ['abre_as' => '08:00', 'fecha_as' => '17:00']);
            HorarioAtrativo::updateOrCreate(['atrativo_id' => $pGuia->id, 'dia_semana' => 3], ['abre_as' => '08:00', 'fecha_as' => '17:00']);
            HorarioAtrativo::updateOrCreate(['atrativo_id' => $pGuia->id, 'dia_semana' => 4], ['abre_as' => '08:00', 'fecha_as' => '17:00']);
            HorarioAtrativo::updateOrCreate(['atrativo_id' => $pGuia->id, 'dia_semana' => 5], ['abre_as' => '08:00', 'fecha_as' => '17:00']);
            HorarioAtrativo::updateOrCreate(['atrativo_id' => $pGuia->id, 'dia_semana' => 6], ['abre_as' => '08:00', 'fecha_as' => '18:00']);
            HorarioAtrativo::updateOrCreate(['atrativo_id' => $pGuia->id, 'dia_semana' => 0], ['abre_as' => '08:00', 'fecha_as' => '18:00']);

            $pRuinas = Atrativo::updateOrCreate(['slug' => 'ruinas-do-bom-sucesso'], [
                'categoria_id' => $catHistory->id,
                'nome' => 'Ruínas do Bom Sucesso',
                'descricao' => 'Ruínas históricas de um antigo engenho de açúcar e capela colonial erguidos nos primeiros séculos de colonização na Paraíba, cercadas por vegetação nativa e rica memória secular.',
                'latitude' => -7.0341000,
                'longitude' => -34.8812000,
                'is_ar_livre' => true,
                'adequado_criancas' => true,
                'duracao_minutos' => 45,
                'custo_medio' => 0.00,
                'is_disponivel' => true,
                'intensidade' => 'medium',
                'tags' => ['ruínas', 'século XVII', 'patrimônio', 'natureza', 'engenhos'],
            ]);
            $pRuinas->recursosAcessibilidade()->sync([$featAudio->id]);

            $pCentroCultural = Atrativo::updateOrCreate(['slug' => 'centro-cultural-e-memoria-de-lucena'], [
                'categoria_id' => $catCulture->id,
                'nome' => 'Centro Cultural e Memória de Lucena',
                'descricao' => 'Espaço coberto e climatizado dedicado à valorização da memória local, com exposições permanentes do artesanato das rendeiras de Lucena, acervo fotográfico histórico e oficinas interativas.',
                'latitude' => -6.9034000,
                'longitude' => -34.8687000,
                'is_ar_livre' => false,
                'adequado_criancas' => true,
                'duracao_minutos' => 75,
                'custo_medio' => 10.00,
                'is_disponivel' => true,
                'intensidade' => 'low',
                'tags' => ['cultura', 'artesanato', 'memória', 'oficinas', 'rendeiras'],
            ]);
            $pCentroCultural->recursosAcessibilidade()->sync([$featRamp->id, $featBathroom->id, $featLibras->id, $featTactile->id, $featBraille->id]);

            $pMirante = Atrativo::updateOrCreate(['slug' => 'mirante-do-encontro-pontinha'], [
                'categoria_id' => $catNature->id,
                'nome' => 'Mirante do Encontro e Pontinha',
                'descricao' => 'Mirante ao ar livre em ponto elevado com vista privilegiada para o encontro das águas e para a formação de bancos de areia e piscinas naturais durante a maré baixa.',
                'latitude' => -6.8921000,
                'longitude' => -34.8562000,
                'is_ar_livre' => true,
                'adequado_criancas' => true,
                'duracao_minutos' => 60,
                'custo_medio' => 0.00,
                'is_disponivel' => true,
                'intensidade' => 'low',
                'tags' => ['mirante', 'vista panorâmica', 'piscinas naturais', 'mar', 'pôr do sol'],
            ]);
            $pMirante->recursosAcessibilidade()->sync([$featRamp->id]);

            $pMercado = Atrativo::updateOrCreate(['slug' => 'mercado-de-sabores-e-peixe-fresco'], [
                'categoria_id' => $catGastronomy->id,
                'nome' => 'Mercado de Sabores e Peixe Fresco',
                'descricao' => 'Mercado gastronômico coberto com quiosques de pescados frescos, camarões nativos, tapiocas recheadas e pratos típicos como peixada ao molho de coco e caldinhos da terra.',
                'latitude' => -6.9015000,
                'longitude' => -34.8699000,
                'is_ar_livre' => false,
                'adequado_criancas' => true,
                'duracao_minutos' => 60,
                'custo_medio' => 40.00,
                'is_disponivel' => true,
                'intensidade' => 'low',
                'tags' => ['gastronomia', 'peixada', 'frutos do mar', 'culinária local', 'mercado'],
            ]);
            $pMercado->recursosAcessibilidade()->sync([$featRamp->id, $featBathroom->id, $featTactile->id]);

            $pPraiaLucena = Atrativo::updateOrCreate(['slug' => 'praia-de-lucena-coqueirais'], [
                'categoria_id' => $catBeaches->id,
                'nome' => 'Praia de Lucena e Coqueirais',
                'descricao' => 'Extensa faixa de areia dourada e águas calmas e mornas ladeadas por uma das maiores concentrações de coqueirais do litoral paraibano. Ideal para banho e caminhadas relaxantes.',
                'latitude' => -6.8955000,
                'longitude' => -34.8580000,
                'is_ar_livre' => true,
                'adequado_criancas' => true,
                'duracao_minutos' => 90,
                'custo_medio' => 0.00,
                'is_disponivel' => true,
                'intensidade' => 'low',
                'tags' => ['praia', 'coqueirais', 'águas mornas', 'banho', 'família'],
            ]);
            $pPraiaLucena->recursosAcessibilidade()->sync([$featRamp->id]);

            $pFalesias = Atrativo::updateOrCreate(['slug' => 'falesias-e-trilha-do-amor'], [
                'categoria_id' => $catEcotourism->id,
                'nome' => 'Falésias e Trilha Ecológica do Amor',
                'descricao' => 'Trilha ecológica sombreada que percorre as encostas de falésias coloridas com mirantes naturais sobre o oceano e rica flora costeira.',
                'latitude' => -6.9200000,
                'longitude' => -34.8510000,
                'is_ar_livre' => true,
                'adequado_criancas' => false,
                'duracao_minutos' => 80,
                'custo_medio' => 0.00,
                'is_disponivel' => true,
                'intensidade' => 'medium',
                'tags' => ['trilha', 'falésias', 'ecoturismo', 'fotografia', 'caminhada'],
            ]);

            $pPraca = Atrativo::updateOrCreate(['slug' => 'praca-da-matriz-lucena'], [
                'categoria_id' => $catCulture->id,
                'nome' => 'Praça da Matriz e Casario Colonial',
                'descricao' => 'Coração urbano e comunitário de Lucena, cercado por casario histórico preservado, coreto tradicional, feiras de artesanato e rodas de coco.',
                'latitude' => -6.9045000,
                'longitude' => -34.8670000,
                'is_ar_livre' => true,
                'adequado_criancas' => true,
                'duracao_minutos' => 45,
                'custo_medio' => 0.00,
                'is_disponivel' => true,
                'intensidade' => 'low',
                'tags' => ['praça', 'casario', 'artesanato', 'comunidade', 'vida local'],
            ]);
            $pPraca->recursosAcessibilidade()->sync([$featRamp->id, $featTactile->id]);

            $pMiriri = Atrativo::updateOrCreate(['slug' => 'santuario-manguezal-rio-miriri'], [
                'categoria_id' => $catEcotourism->id,
                'nome' => 'Santuário Ecológico e Manguezal do Rio Miriri',
                'descricao' => 'Área de preservação ambiental com rica biodiversidade aquática, berçário de caranguejos e aves marinhas, com passarelas e passeios de barco com guias locais.',
                'latitude' => -6.8780000,
                'longitude' => -34.8920000,
                'is_ar_livre' => true,
                'adequado_criancas' => true,
                'duracao_minutos' => 90,
                'custo_medio' => 25.00,
                'is_disponivel' => true,
                'intensidade' => 'low',
                'tags' => ['manguezal', 'rio miriri', 'barco', 'natureza', 'ecoturismo', 'aves'],
            ]);
            $pMiriri->recursosAcessibilidade()->sync([$featAudio->id]);

            // 5. Estabelecimentos Comerciais, Hospedagens, Restaurantes e Guias
            Estabelecimento::updateOrCreate(['slug' => 'pousada-recanto-dos-coqueiros'], [
                'nome' => 'Pousada Recanto dos Coqueiros',
                'tipo_estabelecimento' => 'hospedagem',
                'descricao' => 'Pousada à beira-mar com suítes climatizadas, piscina integrada ao coqueiral e café da manhã regional com frutas colhidas no próprio pomar da propriedade.',
                'endereco' => 'Av. Beira Mar, 102 - Praia de Lucena',
                'bairro' => 'Praia de Lucena',
                'latitude' => -6.8970000,
                'longitude' => -34.8570000,
                'telefone' => '(83) 3293-1122',
                'whatsapp' => '(83) 99881-2233',
                'instagram' => '@recantodoscoqueiroslucena',
                'faixa_preco' => '$$$',
                'tem_selo_qualidade' => true,
                'status_validacao' => 'approved',
                'validado_em' => now(),
            ]);

            Estabelecimento::updateOrCreate(['slug' => 'chales-ecologicos-ponta-de-lucena'], [
                'nome' => 'Chalés Ecológicos Ponta de Lucena',
                'tipo_estabelecimento' => 'hospedagem',
                'descricao' => 'Hospedagem sustentável em bangalôs de madeira de reflorestamento com ventilação natural, energia solar e vista deslumbrante para a enseada da Pontinha.',
                'endereco' => 'Rua das Falésias, 45 - Pontinha',
                'bairro' => 'Pontinha',
                'latitude' => -6.8910000,
                'longitude' => -34.8540000,
                'telefone' => '(83) 3293-1450',
                'whatsapp' => '(83) 99650-4411',
                'instagram' => '@pontadelucenachales',
                'faixa_preco' => '$$',
                'tem_selo_qualidade' => true,
                'status_validacao' => 'approved',
                'validado_em' => now(),
            ]);

            Estabelecimento::updateOrCreate(['slug' => 'restaurante-sabores-da-guia'], [
                'nome' => 'Restaurante Sabores da Guia',
                'tipo_estabelecimento' => 'gastronomia',
                'descricao' => 'Tradicional restaurante com vista panorâmica servindo peixe na telha com molho de camarão, pirão caseiro e sobremesas feitas com doces da cana de açúcar local.',
                'endereco' => 'Acesso ao Santuário da Guia, s/n - Alto da Guia',
                'bairro' => 'Nossa Senhora da Guia',
                'latitude' => -7.0175000,
                'longitude' => -34.8715000,
                'telefone' => '(83) 3293-1890',
                'whatsapp' => '(83) 99123-4567',
                'instagram' => '@saboresdaguialucena',
                'faixa_preco' => '$$',
                'tem_selo_qualidade' => true,
                'status_validacao' => 'approved',
                'validado_em' => now(),
            ]);

            Estabelecimento::updateOrCreate(['slug' => 'bar-e-restaurante-peixe-na-palha'], [
                'nome' => 'Bar e Restaurante Peixe na Palha',
                'tipo_estabelecimento' => 'gastronomia',
                'descricao' => 'Quiosque gastronômico de praia com chão de areia, som ambiente regional, água de coco gelada e os famosos petiscos de agulhinha frita crocante e caranguejada.',
                'endereco' => 'Praia de Costinha, s/n',
                'bairro' => 'Costinha',
                'latitude' => -7.0120000,
                'longitude' => -34.8650000,
                'telefone' => '(83) 99344-5566',
                'whatsapp' => '(83) 99344-5566',
                'faixa_preco' => '$',
                'tem_selo_qualidade' => true,
                'status_validacao' => 'approved',
                'validado_em' => now(),
            ]);

            Estabelecimento::updateOrCreate(['slug' => 'guia-tiago-miriri-ecoturismo'], [
                'nome' => 'Tiago Santos — Condutor do Rio Miriri',
                'tipo_estabelecimento' => 'guia_turistico',
                'descricao' => 'Guia credenciado com Selo de Qualidade Municipal e registro CADASTUR, especialista em avifauna, botânica do manguezal e navegação ecológica no estuário do Miriri.',
                'endereco' => 'Comunidade Ribeirinha de Fagundes',
                'bairro' => 'Zona Rural',
                'latitude' => -6.8790000,
                'longitude' => -34.8910000,
                'telefone' => '(83) 98765-4321',
                'whatsapp' => '(83) 98765-4321',
                'instagram' => '@tiago.miriri.guiapb',
                'faixa_preco' => '$$',
                'tem_selo_qualidade' => true,
                'status_validacao' => 'approved',
                'validado_em' => now(),
            ]);

            Estabelecimento::updateOrCreate(['slug' => 'guia-dona-marta-caminhos-da-guia'], [
                'nome' => 'Marta Maria — Guia Cultural e Histórica',
                'tipo_estabelecimento' => 'guia_turistico',
                'descricao' => 'Historiadora e condutora local certificada com 18 anos de experiência contando as memórias da Igreja da Guia, dos povos originários e das antigas famílias de Lucena.',
                'endereco' => 'Rua do Cruzeiro, 12 - Centro',
                'bairro' => 'Centro',
                'latitude' => -6.9040000,
                'longitude' => -34.8680000,
                'telefone' => '(83) 99877-6655',
                'whatsapp' => '(83) 99877-6655',
                'instagram' => '@martaguialucena',
                'faixa_preco' => '$',
                'tem_selo_qualidade' => true,
                'status_validacao' => 'approved',
                'validado_em' => now(),
            ]);

            // 6. Eventos Oficiais e Festividades
            Evento::updateOrCreate(['slug' => 'festa-da-padroeira-nossa-senhora-da-guia-2026'], [
                'nome' => 'Festa Secular de Nossa Senhora da Guia',
                'descricao' => 'A maior e mais tradicional celebração religiosa e cultural de Lucena, com missas solenes, procissão no alto da colina, quermesse paroquial e apresentações de trios pé-de-serra.',
                'nome_local' => 'Santuário de Nossa Senhora da Guia',
                'endereco' => 'Alto da Guia, Lucena - PB',
                'latitude' => -7.0182000,
                'longitude' => -34.8724000,
                'inicia_em' => now()->addDays(15)->setTime(17, 0),
                'termina_em' => now()->addDays(18)->setTime(22, 0),
                'is_gratuito' => true,
                'is_acessivel' => true,
                'categoria' => 'religioso',
                'organizador' => 'Paróquia de Lucena e Secretaria Municipal de Turismo',
                'status' => 'scheduled',
            ]);

            Evento::updateOrCreate(['slug' => 'festival-gastronomico-do-peixe-e-camarao-2026'], [
                'nome' => 'Festival Gastronômico do Pescado de Lucena',
                'descricao' => 'Festival que reúne restaurantes e quiosques da orla preparando receitas exclusivas à base de frutos do mar frescos pescados pelos marisqueiros locais, com aulas-show e shows na praia.',
                'nome_local' => 'Praça de Eventos da Orla de Lucena',
                'endereco' => 'Av. Beira Mar, s/n - Centro',
                'latitude' => -6.8960000,
                'longitude' => -34.8575000,
                'inicia_em' => now()->addDays(28)->setTime(18, 0),
                'termina_em' => now()->addDays(30)->setTime(23, 30),
                'is_gratuito' => true,
                'is_acessivel' => true,
                'categoria' => 'gastronômico',
                'organizador' => 'Prefeitura Municipal de Lucena',
                'status' => 'scheduled',
            ]);

            // 7. Roteiro Oficial Pré-Configurado
            $pref = PreferenciaVisitante::create([
                'descricao_original' => 'Gostaria de conhecer o melhor do patrimônio histórico e das belezas naturais de Lucena em uma manhã tranquila com minha família.',
                'humores' => ['cultural', 'tranquilo', 'familia'],
                'interesses' => ['patrimonio-historico', 'sol-e-natureza', 'cultura-e-tradicao'],
                'minutos_disponiveis' => 240,
                'orcamento' => 120.00,
                'tem_criancas' => true,
                'transporte' => 'carro',
                'requisitos_acessibilidade' => ['rampa-de-acesso'],
                'intensidade' => 'low',
            ]);

            $itinerary = Roteiro::create([
                'preferencia_visitante_id' => $pref->id,
                'titulo' => 'Roteiro Essencial: História, Tradição e Mar em Lucena',
                'resumo' => 'Uma experiência completa que conecta o legado seiscentista carmelita, o artesanato tradicional das rendeiras e o frescor da orla de coqueirais.',
                'duracao_total_minutos' => 225,
                'custo_total_estimado' => 50.00,
                'status' => 'official',
            ]);

            ItemRoteiro::create([
                'roteiro_id' => $itinerary->id,
                'atrativo_id' => $pGuia->id,
                'posicao' => 1,
                'duracao_minutos' => 60,
                'custo_estimado' => 0.00,
                'motivo' => 'Início perfeito pelo monumento histórico mais icônico da Paraíba com vista panorâmica inesquecível.',
            ]);

            ItemRoteiro::create([
                'roteiro_id' => $itinerary->id,
                'atrativo_id' => $pCentroCultural->id,
                'posicao' => 2,
                'duracao_minutos' => 75,
                'custo_estimado' => 10.00,
                'motivo' => 'Imersão no rico acervo de artesanato e história viva das mestras rendeiras em ambiente climatizado e acessível.',
            ]);

            ItemRoteiro::create([
                'roteiro_id' => $itinerary->id,
                'atrativo_id' => $pMercado->id,
                'posicao' => 3,
                'duracao_minutos' => 60,
                'custo_estimado' => 40.00,
                'motivo' => 'Parada para degustação dos sabores típicos do litoral com almoço regional à base de peixes frescos.',
            ]);

            ItemRoteiro::create([
                'roteiro_id' => $itinerary->id,
                'atrativo_id' => $pPraiaLucena->id,
                'posicao' => 4,
                'duracao_minutos' => 30,
                'custo_estimado' => 0.00,
                'motivo' => 'Fechamento revigorante com caminhada sob a sombra dos coqueirais e brisa oceânica.',
            ]);

            // 8. Usuário Gestor Municipal
            User::updateOrCreate(
                ['email' => 'gestor@lucena.pb.gov.br'],
                [
                    'name' => 'Gestor do Turismo de Lucena',
                    'password' => Hash::make('12345678'),
                    'municipio_id' => $municipality->id,
                    'can_access_admin_panel' => true,
                    'can_manage_platform' => false,
                ]
            );

        } finally {
            $tenantManager->reset();
        }
    }
}
