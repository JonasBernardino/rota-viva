<?php

namespace Database\Seeders;

use App\Models\AccessibilityFeature;
use App\Models\Business;
use App\Models\Category;
use App\Models\Event;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Municipality;
use App\Models\Place;
use App\Models\PlaceSchedule;
use App\Models\User;
use App\Models\VisitorPreference;
use App\Services\Tenant\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LucenaDemoSeeder extends Seeder
{
    public function run(TenantManager $tenantManager): void
    {
        // 1. Ensure Tenant Municipality exists in public schema
        $municipality = Municipality::where('slug', 'lucena')->first();

        if (! $municipality) {
            $municipality = $tenantManager->createTenant([
                'name' => 'Lucena',
                'slug' => 'lucena',
                'ibge_code' => '2508903',
                'state' => 'PB',
                'schema_name' => 'tenant_lucena',
                'status' => 'active',
                'timezone' => 'America/Fortaleza',
                'settings' => [
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
        } else {
            $tenantManager->migrateTenant($municipality);
        }

        // Switch to tenant schema to populate data
        $tenantManager->switchTo($municipality);

        try {
            // 2. Categories
            $catHistory = Category::updateOrCreate(['slug' => 'patrimonio-historico'], [
                'name' => 'Patrimônio Histórico',
            ]);

            $catNature = Category::updateOrCreate(['slug' => 'sol-e-natureza'], [
                'name' => 'Sol e Natureza',
            ]);

            $catCulture = Category::updateOrCreate(['slug' => 'cultura-e-tradicao'], [
                'name' => 'Cultura e Tradição',
            ]);

            $catGastronomy = Category::updateOrCreate(['slug' => 'gastronomia-litoranea'], [
                'name' => 'Gastronomia Litorânea',
            ]);

            $catEcotourism = Category::updateOrCreate(['slug' => 'ecoturismo-e-trilhas'], [
                'name' => 'Ecoturismo e Trilhas',
            ]);

            $catBeaches = Category::updateOrCreate(['slug' => 'praias-e-lazer'], [
                'name' => 'Praias e Lazer',
            ]);

            // 3. Accessibility Features
            $featRamp = AccessibilityFeature::updateOrCreate(['slug' => 'rampa-de-acesso'], [
                'name' => 'Rampa de Acesso',
            ]);

            $featBathroom = AccessibilityFeature::updateOrCreate(['slug' => 'banheiro-adaptado'], [
                'name' => 'Banheiro Adaptado',
            ]);

            $featLibras = AccessibilityFeature::updateOrCreate(['slug' => 'atendimento-em-libras'], [
                'name' => 'Atendimento em Libras',
            ]);

            $featTactile = AccessibilityFeature::updateOrCreate(['slug' => 'piso-tatil'], [
                'name' => 'Piso Tátil',
            ]);

            $featAudio = AccessibilityFeature::updateOrCreate(['slug' => 'audiodescricao'], [
                'name' => 'Audiodescrição',
            ]);

            $featBraille = AccessibilityFeature::updateOrCreate(['slug' => 'sinalizacao-braille'], [
                'name' => 'Sinalização em Braille',
            ]);

            // 4. Places (Atrativos Oficiais e Históricos)
            $pGuia = Place::updateOrCreate(['slug' => 'igreja-nossa-senhora-da-guia'], [
                'category_id' => $catHistory->id,
                'name' => 'Igreja de Nossa Senhora da Guia',
                'description' => 'Um dos mais expressivos e antigos templos católicos do Brasil, construído no século XVI pelos carmelitas sobre uma colina. Destaca-se pelo estilo barroco tropical e vista panorâmica para a foz do Rio Paraíba.',
                'latitude' => -7.0182000,
                'longitude' => -34.8724000,
                'is_outdoor' => false,
                'suitable_for_children' => true,
                'duration_minutes' => 60,
                'average_cost' => 0.00,
                'is_available' => true,
                'intensity' => 'low',
                'tags' => ['histórico', 'barroco', 'religioso', 'vista panorâmica', 'século XVI'],
            ]);
            $pGuia->accessibilityFeatures()->sync([$featRamp->id, $featBathroom->id, $featAudio->id]);

            PlaceSchedule::updateOrCreate(['place_id' => $pGuia->id, 'day_of_week' => 1], ['opens_at' => '08:00', 'closes_at' => '17:00']);
            PlaceSchedule::updateOrCreate(['place_id' => $pGuia->id, 'day_of_week' => 2], ['opens_at' => '08:00', 'closes_at' => '17:00']);
            PlaceSchedule::updateOrCreate(['place_id' => $pGuia->id, 'day_of_week' => 3], ['opens_at' => '08:00', 'closes_at' => '17:00']);
            PlaceSchedule::updateOrCreate(['place_id' => $pGuia->id, 'day_of_week' => 4], ['opens_at' => '08:00', 'closes_at' => '17:00']);
            PlaceSchedule::updateOrCreate(['place_id' => $pGuia->id, 'day_of_week' => 5], ['opens_at' => '08:00', 'closes_at' => '17:00']);
            PlaceSchedule::updateOrCreate(['place_id' => $pGuia->id, 'day_of_week' => 6], ['opens_at' => '08:00', 'closes_at' => '18:00']);
            PlaceSchedule::updateOrCreate(['place_id' => $pGuia->id, 'day_of_week' => 7], ['opens_at' => '08:00', 'closes_at' => '18:00']);

            $pRuinas = Place::updateOrCreate(['slug' => 'ruinas-do-bom-sucesso'], [
                'category_id' => $catHistory->id,
                'name' => 'Ruínas do Bom Sucesso',
                'description' => 'Ruínas históricas de um antigo engenho de açúcar e capela colonial erguidos nos primeiros séculos de colonização na Paraíba, cercadas por vegetação nativa e rica memória secular.',
                'latitude' => -7.0341000,
                'longitude' => -34.8812000,
                'is_outdoor' => true,
                'suitable_for_children' => true,
                'duration_minutes' => 45,
                'average_cost' => 0.00,
                'is_available' => true,
                'intensity' => 'medium',
                'tags' => ['ruínas', 'século XVII', 'patrimônio', 'natureza', 'engenhos'],
            ]);
            $pRuinas->accessibilityFeatures()->sync([$featAudio->id]);

            $pCentroCultural = Place::updateOrCreate(['slug' => 'centro-cultural-e-memoria-de-lucena'], [
                'category_id' => $catCulture->id,
                'name' => 'Centro Cultural e Memória de Lucena',
                'description' => 'Espaço coberto e climatizado dedicado à valorização da memória local, com exposições permanentes do artesanato das rendeiras de Lucena, acervo fotográfico histórico e oficinas interativas.',
                'latitude' => -6.9034000,
                'longitude' => -34.8687000,
                'is_outdoor' => false,
                'suitable_for_children' => true,
                'duration_minutes' => 75,
                'average_cost' => 10.00,
                'is_available' => true,
                'intensity' => 'low',
                'tags' => ['cultura', 'artesanato', 'memória', 'oficinas', 'rendeiras'],
            ]);
            $pCentroCultural->accessibilityFeatures()->sync([$featRamp->id, $featBathroom->id, $featLibras->id, $featTactile->id, $featBraille->id]);

            $pMirante = Place::updateOrCreate(['slug' => 'mirante-do-encontro-pontinha'], [
                'category_id' => $catNature->id,
                'name' => 'Mirante do Encontro e Pontinha',
                'description' => 'Mirante ao ar livre em ponto elevado com vista privilegiada para o encontro das águas e para a formação de bancos de areia e piscinas naturais durante a maré baixa.',
                'latitude' => -6.8921000,
                'longitude' => -34.8562000,
                'is_outdoor' => true,
                'suitable_for_children' => true,
                'duration_minutes' => 60,
                'average_cost' => 0.00,
                'is_available' => true,
                'intensity' => 'low',
                'tags' => ['mirante', 'vista panorâmica', 'piscinas naturais', 'mar', 'pôr do sol'],
            ]);
            $pMirante->accessibilityFeatures()->sync([$featRamp->id]);

            $pMercado = Place::updateOrCreate(['slug' => 'mercado-de-sabores-e-peixe-fresco'], [
                'category_id' => $catGastronomy->id,
                'name' => 'Mercado de Sabores e Peixe Fresco',
                'description' => 'Mercado gastronômico coberto com quiosques de pescados frescos, camarões nativos, tapiocas recheadas e pratos típicos como peixada ao molho de coco e caldinhos da terra.',
                'latitude' => -6.9015000,
                'longitude' => -34.8699000,
                'is_outdoor' => false,
                'suitable_for_children' => true,
                'duration_minutes' => 60,
                'average_cost' => 40.00,
                'is_available' => true,
                'intensity' => 'low',
                'tags' => ['gastronomia', 'peixada', 'frutos do mar', 'culinária local', 'mercado'],
            ]);
            $pMercado->accessibilityFeatures()->sync([$featRamp->id, $featBathroom->id, $featTactile->id]);

            $pPraiaLucena = Place::updateOrCreate(['slug' => 'praia-de-lucena-coqueirais'], [
                'category_id' => $catBeaches->id,
                'name' => 'Praia de Lucena e Coqueirais',
                'description' => 'Extensa faixa de areia dourada e águas calmas e mornas ladeadas por uma das maiores concentrações de coqueirais do litoral paraibano. Ideal para banho e caminhadas relaxantes.',
                'latitude' => -6.8955000,
                'longitude' => -34.8580000,
                'is_outdoor' => true,
                'suitable_for_children' => true,
                'duration_minutes' => 90,
                'average_cost' => 0.00,
                'is_available' => true,
                'intensity' => 'low',
                'tags' => ['praia', 'coqueirais', 'águas mornas', 'banho', 'família'],
            ]);
            $pPraiaLucena->accessibilityFeatures()->sync([$featRamp->id]);

            $pFalesias = Place::updateOrCreate(['slug' => 'falesias-e-trilha-do-amor'], [
                'category_id' => $catEcotourism->id,
                'name' => 'Falésias e Trilha Ecológica do Amor',
                'description' => 'Trilha ecológica sombreada que percorre as encostas de falésias coloridas com mirantes naturais sobre o oceano e rica flora costeira.',
                'latitude' => -6.9200000,
                'longitude' => -34.8510000,
                'is_outdoor' => true,
                'suitable_for_children' => false,
                'duration_minutes' => 80,
                'average_cost' => 0.00,
                'is_available' => true,
                'intensity' => 'medium',
                'tags' => ['trilha', 'falésias', 'ecoturismo', 'fotografia', 'caminhada'],
            ]);

            $pPraca = Place::updateOrCreate(['slug' => 'praca-da-matriz-lucena'], [
                'category_id' => $catCulture->id,
                'name' => 'Praça da Matriz e Casario Colonial',
                'description' => 'Coração urbano e comunitário de Lucena, cercado por casario histórico preservado, coreto tradicional, feiras de artesanato e rodas de coco.',
                'latitude' => -6.9045000,
                'longitude' => -34.8670000,
                'is_outdoor' => true,
                'suitable_for_children' => true,
                'duration_minutes' => 45,
                'average_cost' => 0.00,
                'is_available' => true,
                'intensity' => 'low',
                'tags' => ['praça', 'casario', 'artesanato', 'comunidade', 'vida local'],
            ]);
            $pPraca->accessibilityFeatures()->sync([$featRamp->id, $featTactile->id]);

            $pMiriri = Place::updateOrCreate(['slug' => 'santuario-manguezal-rio-miriri'], [
                'category_id' => $catEcotourism->id,
                'name' => 'Santuário Ecológico e Manguezal do Rio Miriri',
                'description' => 'Área de preservação ambiental com rica biodiversidade aquática, berçário de caranguejos e aves marinhas, com passarelas e passeios de barco com guias locais.',
                'latitude' => -6.8780000,
                'longitude' => -34.8920000,
                'is_outdoor' => true,
                'suitable_for_children' => true,
                'duration_minutes' => 90,
                'average_cost' => 25.00,
                'is_available' => true,
                'intensity' => 'low',
                'tags' => ['manguezal', 'rio miriri', 'biodiversidade', 'passeio de barco', 'ecologia'],
            ]);
            $pMiriri->accessibilityFeatures()->sync([$featRamp->id, $featBathroom->id]);

            // 5. Businesses (Trade Turístico Validado: Hospedagem, Gastronomia, Guias e Passeios)

            // Hospedagem (Onde Ficar)
            Business::updateOrCreate(['slug' => 'pousada-dos-coqueirais-lucena'], [
                'name' => 'Pousada dos Coqueirais de Lucena',
                'business_type' => 'lodging',
                'description' => 'Hospedagem aconchegante à beira-mar com café da manhã regional, piscina, redário entre coqueiros e atendimento familiar acolhedor.',
                'address' => 'Rua dos Pescadores, 88',
                'neighborhood' => 'Praia de Lucena',
                'latitude' => -6.8960000,
                'longitude' => -34.8585000,
                'phone' => '(83) 99881-2233',
                'whatsapp' => '(83) 99881-2233',
                'instagram' => '@pousadadoscoqueiraislucena',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'hospedagem-mar-de-lucena'], [
                'name' => 'Hospedagem & Chalés Mar de Lucena',
                'business_type' => 'lodging',
                'description' => 'Chalés equipados com cozinha compacta, ar-condicionado e varanda privativa com vista para o mar. Excelente opção para famílias e estadias prolongadas.',
                'address' => 'Avenida Litorânea, 420',
                'neighborhood' => 'Praia de Fagundes',
                'latitude' => -6.9100000,
                'longitude' => -34.8550000,
                'phone' => '(83) 99344-1122',
                'whatsapp' => '(83) 99344-1122',
                'instagram' => '@mardelucenachales',
                'price_range' => '$$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'pousada-recanto-da-guia'], [
                'name' => 'Pousada Recanto da Guia',
                'business_type' => 'lodging',
                'description' => 'Ambiente tranquilo no alto da colina, próximo ao patrimônio histórico, com vista privilegiada para o pôr do sol e pomar de frutas tropicais.',
                'address' => 'Estrada da Guia, s/n',
                'neighborhood' => 'Colina da Guia',
                'latitude' => -7.0175000,
                'longitude' => -34.8718000,
                'phone' => '(83) 98877-3344',
                'whatsapp' => '(83) 98877-3344',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'reserva-verde-chales-ecologicos'], [
                'name' => 'Chalés Ecológicos Reserva Verde',
                'business_type' => 'lodging',
                'description' => 'Conceito sustentável integrado à mata costeira, com energia solar, captação de água de chuva e trilhas privativas para observação de aves.',
                'address' => 'Sítio Boa Vista, km 4',
                'neighborhood' => 'Zona Rural',
                'latitude' => -6.9320000,
                'longitude' => -34.8740000,
                'phone' => '(83) 99112-9988',
                'whatsapp' => '(83) 99112-9988',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            // Gastronomia (Onde Comer)
            Business::updateOrCreate(['slug' => 'restaurante-sabores-da-guia'], [
                'name' => 'Restaurante Sabores da Guia',
                'business_type' => 'gastronomy',
                'description' => 'Especializado em culinária litorânea paraibana: peixada ao leite de coco fresco, camarão na moranga, caldinho de sururu e doces caseiros de frutas nativas.',
                'address' => 'Avenida Principal, 210',
                'neighborhood' => 'Centro',
                'latitude' => -6.9030000,
                'longitude' => -34.8690000,
                'phone' => '(83) 98770-4455',
                'whatsapp' => '(83) 98770-4455',
                'instagram' => '@saboresdaguialucena',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'quiosque-peixe-na-telha-pontinha'], [
                'name' => 'Quiosque Peixe na Telha — Pontinha',
                'business_type' => 'gastronomy',
                'description' => 'Pratos com pescados assados na brasa e servidos na telha de barro, caranguejo cozido e água de coco gelada colhida na hora.',
                'address' => 'Orla da Pontinha, Box 04',
                'neighborhood' => 'Pontinha',
                'latitude' => -6.8910000,
                'longitude' => -34.8560000,
                'phone' => '(83) 99655-7788',
                'whatsapp' => '(83) 99655-7788',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'bar-e-restaurante-mar-e-brisa'], [
                'name' => 'Bar e Restaurante Mar & Brisa',
                'business_type' => 'gastronomy',
                'description' => 'Mesas sob a copa de coqueiros com vista para o mar calmo, servindo petiscos de frutos do mar, caranguejos selecionados e sucos regionais.',
                'address' => 'Rua Beira-Mar, 15',
                'neighborhood' => 'Praia de Lucena',
                'latitude' => -6.8970000,
                'longitude' => -34.8575000,
                'phone' => '(83) 98822-6611',
                'whatsapp' => '(83) 98822-6611',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'cafe-colonial-e-tapiocaria-da-terra'], [
                'name' => 'Café Colonial e Tapiocaria da Terra',
                'business_type' => 'gastronomy',
                'description' => 'Mais de 20 variedades de tapiocas doces e salgadas feitas com goma fresca artesanal, cuscuz recheado, bolo de macaxeira e café passado na hora.',
                'address' => 'Rua do Comércio, 45',
                'neighborhood' => 'Centro Histórico',
                'latitude' => -6.9040000,
                'longitude' => -34.8680000,
                'phone' => '(83) 99144-8833',
                'whatsapp' => '(83) 99144-8833',
                'price_range' => '$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            // Guias Turísticos (Profissionais Validados)
            Business::updateOrCreate(['slug' => 'guia-joao-ribeiro-condutor'], [
                'name' => 'João Ribeiro — Condutor de Experiências Culturais',
                'business_type' => 'tour_guide',
                'description' => 'Guia credenciado Cadastur e morador nativo, especialista na história dos engenhos, barroco da Igreja da Guia e lendas orais de Lucena.',
                'neighborhood' => 'Centro',
                'phone' => '(83) 99123-4567',
                'whatsapp' => '(83) 99123-4567',
                'instagram' => '@joaoribeiroguiapb',
                'price_range' => '$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'guia-mariana-costa-ecoturismo'], [
                'name' => 'Mariana Costa — Guia de Natureza e Ecoturismo',
                'business_type' => 'tour_guide',
                'description' => 'Bióloga e condutora de turismo ecológico, especializada em observação de aves costeiras, manguezais do Rio Miriri e caminhadas pelas falésias.',
                'neighborhood' => 'Praia de Lucena',
                'phone' => '(83) 99311-2244',
                'whatsapp' => '(83) 99311-2244',
                'instagram' => '@marianacosta.eco',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'guia-carlos-mendes-historia'], [
                'name' => 'Carlos Mendes — Especialista em Patrimônio Colonial',
                'business_type' => 'tour_guide',
                'description' => 'Historiador e pesquisador com mais de 15 anos conduzindo roteiros patrimoniais pelas igrejas, ruínas e sítios arqueológicos da costa norte da Paraíba.',
                'neighborhood' => 'Centro Histórico',
                'phone' => '(83) 98765-4321',
                'whatsapp' => '(83) 98765-4321',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            // Passeios & Experiências (Atividades Locais)
            Business::updateOrCreate(['slug' => 'passeio-ecologico-rio-miriri'], [
                'name' => 'Passeio Ecológico no Rio Miriri e Manguezais',
                'business_type' => 'activity',
                'description' => 'Passeio de barco tradicional navegando pelas águas calmas do Rio Miriri, com paradas para banho de argila natural, visita guiada ao manguezal e degustação de frutas locais.',
                'address' => 'Píer dos Pescadores de Miriri, s/n',
                'neighborhood' => 'Foz do Miriri',
                'latitude' => -6.8770000,
                'longitude' => -34.8910000,
                'phone' => '(83) 99188-5522',
                'whatsapp' => '(83) 99188-5522',
                'price_range' => '$$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'caminhada-historica-dos-engenhos'], [
                'name' => 'Caminhada Histórica pelos Engenhos e Ruínas',
                'business_type' => 'activity',
                'description' => 'Circuito a pé guiado conectando a Igreja da Guia, Ruínas do Bom Sucesso e antigos caminhos de engenho, com narrativas históricas sobre a formação territorial.',
                'address' => 'Ponto de Encontro: Igreja da Guia',
                'neighborhood' => 'Colina da Guia',
                'latitude' => -7.0180000,
                'longitude' => -34.8720000,
                'phone' => '(83) 99123-4567',
                'whatsapp' => '(83) 99123-4567',
                'price_range' => '$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            Business::updateOrCreate(['slug' => 'vivencia-com-as-rendeiras-de-lucena'], [
                'name' => 'Oficina e Vivência com as Rendeiras Tradicionais',
                'business_type' => 'activity',
                'description' => 'Experiência imersiva no Centro Cultural para aprender os pontos tradicionais da renda de bilro e labirinto com as mestras artesãs do município.',
                'address' => 'Centro Cultural e Memória de Lucena',
                'neighborhood' => 'Centro',
                'latitude' => -6.9034000,
                'longitude' => -34.8687000,
                'phone' => '(83) 3293-1450',
                'whatsapp' => '(83) 3293-1450',
                'price_range' => '$',
                'has_seal_of_quality' => true,
                'validation_status' => 'approved',
                'validated_at' => now(),
            ]);

            // 6. Events (Agenda Cultural e Festividades)
            Event::updateOrCreate(['slug' => 'festa-tradicional-da-guia-2026'], [
                'name' => 'Festa Tradicional de Nossa Senhora da Guia 2026',
                'description' => 'Patrimônio imaterial de Lucena, a celebração reúne novenário solene, procissão luminosa na colina, apresentações de coco de roda, feira de comidas típicas e shows culturais.',
                'location_name' => 'Santuário da Colina da Guia',
                'address' => 'Colina da Guia, s/n',
                'latitude' => -7.0182000,
                'longitude' => -34.8724000,
                'starts_at' => now()->addDays(15)->setTime(18, 0),
                'ends_at' => now()->addDays(18)->setTime(23, 30),
                'is_free' => true,
                'is_accessible' => true,
                'category' => 'cultural',
                'organizer' => 'Secretaria Municipal de Turismo e Paróquia da Guia',
                'capacity' => 5000,
                'status' => 'scheduled',
            ]);

            Event::updateOrCreate(['slug' => 'festival-gastronomico-frutos-do-mar'], [
                'name' => 'Festival Gastronômico Frutos do Mar de Lucena',
                'description' => 'Concurso de receitas tradicionais entre restaurantes locais, aulas-show com chefs convidados, barracas com petiscos a preços populares e shows acústicos à beira-mar.',
                'location_name' => 'Orla Central de Lucena',
                'address' => 'Avenida Beira-Mar, s/n',
                'latitude' => -6.8970000,
                'longitude' => -34.8575000,
                'starts_at' => now()->addDays(28)->setTime(12, 0),
                'ends_at' => now()->addDays(30)->setTime(22, 0),
                'is_free' => true,
                'is_accessible' => true,
                'category' => 'gastronomy',
                'organizer' => 'Trade Turístico e Secretaria de Desenvolvimento Econômico',
                'capacity' => 3000,
                'status' => 'scheduled',
            ]);

            Event::updateOrCreate(['slug' => 'encontro-de-coco-de-roda-e-ciranda'], [
                'name' => 'Encontro Regional de Mestres de Coco de Roda e Ciranda',
                'description' => 'Grande roda cultural sob o luar de Lucena com mestres e mestras da Paraíba celebrando a tradição afro-indígena litorânea com muito ritmo, tambores e passos marcados.',
                'location_name' => 'Praça da Matriz',
                'address' => 'Praça da Matriz, Centro',
                'latitude' => -6.9045000,
                'longitude' => -34.8670000,
                'starts_at' => now()->addDays(40)->setTime(19, 0),
                'ends_at' => now()->addDays(40)->setTime(23, 45),
                'is_free' => true,
                'is_accessible' => true,
                'category' => 'cultural',
                'organizer' => 'Coletivo de Cultura Popular de Lucena',
                'capacity' => 1200,
                'status' => 'scheduled',
            ]);

            Event::updateOrCreate(['slug' => 'trilha-ecologica-da-lua-cheia'], [
                'name' => 'Trilha Ecológica da Lua Cheia nas Falésias',
                'description' => 'Caminhada noturna guiada pelas falésias e praias sob a luz da lua cheia, com contação de causos de pescadores e fogueira comunitária.',
                'location_name' => 'Mirante do Encontro',
                'address' => 'Pontinha de Lucena',
                'latitude' => -6.8921000,
                'longitude' => -34.8562000,
                'starts_at' => now()->addDays(22)->setTime(20, 0),
                'ends_at' => now()->addDays(22)->setTime(22, 30),
                'is_free' => true,
                'is_accessible' => false,
                'category' => 'nature',
                'organizer' => 'Condutores Ambientais de Lucena',
                'capacity' => 60,
                'status' => 'scheduled',
            ]);

            // 7. Official Itineraries (Roteiros Oficiais com Curadoria Municipal)
            $prefHist = VisitorPreference::create([
                'original_description' => 'Roteiro oficial de patrimônio histórico, igrejas e cultura colonial em Lucena.',
                'moods' => ['cultural', 'tranquilo'],
                'interests' => ['historia', 'patrimonio', 'cultura'],
                'available_minutes' => 180,
                'budget' => 50.00,
                'has_children' => true,
                'transport' => 'car',
                'accessibility_requirements' => ['rampa-de-acesso'],
                'intensity' => 'low',
            ]);

            $itHist = Itinerary::updateOrCreate(['title' => 'Roteiro Oficial: História e Fé na Costa Norte'], [
                'visitor_preference_id' => $prefHist->id,
                'summary' => 'Uma jornada emocionante pelas raízes coloniais, arquitetura barroca e ruínas seculares de Lucena com acessibilidade e pontos de contemplação.',
                'total_duration_minutes' => 180,
                'total_estimated_cost' => 10.00,
                'status' => 'official',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itHist->id, 'position' => 1], [
                'place_id' => $pGuia->id,
                'duration_minutes' => 60,
                'estimated_cost' => 0.00,
                'reason' => 'Visita à histórica Igreja da Guia e mirante panorâmico sobre o estuário.',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itHist->id, 'position' => 2], [
                'place_id' => $pRuinas->id,
                'duration_minutes' => 45,
                'estimated_cost' => 0.00,
                'reason' => 'Caminhada pelas ruínas do antigo engenho e capela colonial do Bom Sucesso.',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itHist->id, 'position' => 3], [
                'place_id' => $pCentroCultural->id,
                'duration_minutes' => 75,
                'estimated_cost' => 10.00,
                'reason' => 'Imersão no acervo histórico, artesanato de rendas e café cultural.',
            ]);

            $prefSabores = VisitorPreference::create([
                'original_description' => 'Roteiro oficial gastronômico de pescados frescos, rendas e cafés típicos.',
                'moods' => ['gastronômico', 'familiar'],
                'interests' => ['gastronomia', 'artesanato'],
                'available_minutes' => 165,
                'budget' => 100.00,
                'has_children' => true,
                'transport' => 'car',
                'accessibility_requirements' => [],
                'intensity' => 'low',
            ]);

            $itSabores = Itinerary::updateOrCreate(['title' => 'Roteiro Oficial: Sabores e Tradições de Lucena'], [
                'visitor_preference_id' => $prefSabores->id,
                'summary' => 'O melhor da gastronomia praiana e tradição artesanal para vivenciar em família com calma e sabor.',
                'total_duration_minutes' => 165,
                'total_estimated_cost' => 50.00,
                'status' => 'official',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itSabores->id, 'position' => 1], [
                'place_id' => $pCentroCultural->id,
                'duration_minutes' => 60,
                'estimated_cost' => 10.00,
                'reason' => 'Conheça o trabalho das rendeiras locais e a história do artesanato de Lucena.',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itSabores->id, 'position' => 2], [
                'place_id' => $pMercado->id,
                'duration_minutes' => 60,
                'estimated_cost' => 40.00,
                'reason' => 'Almoço gastronômico com peixada paraibana fresca e caldinhos no mercado.',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itSabores->id, 'position' => 3], [
                'place_id' => $pPraca->id,
                'duration_minutes' => 45,
                'estimated_cost' => 0.00,
                'reason' => 'Passeio pelo casario colonial e parada para café com tapioca artesanal.',
            ]);

            $prefNature = VisitorPreference::create([
                'original_description' => 'Roteiro oficial de natureza viva, piscinas naturais e passeio ecológico de manguezal.',
                'moods' => ['natureza', 'aventura_leve'],
                'interests' => ['natureza', 'ecoturismo', 'praia'],
                'available_minutes' => 240,
                'budget' => 80.00,
                'has_children' => true,
                'transport' => 'car',
                'accessibility_requirements' => [],
                'intensity' => 'low',
            ]);

            $itNature = Itinerary::updateOrCreate(['title' => 'Roteiro Oficial: Sol, Manguezal e Natureza Viva'], [
                'visitor_preference_id' => $prefNature->id,
                'summary' => 'Vivencie as belezas naturais preservadas de Lucena, desde as piscinas da Pontinha até os canais ecológicos do Rio Miriri.',
                'total_duration_minutes' => 240,
                'total_estimated_cost' => 25.00,
                'status' => 'official',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itNature->id, 'position' => 1], [
                'place_id' => $pMirante->id,
                'duration_minutes' => 60,
                'estimated_cost' => 0.00,
                'reason' => 'Observação das piscinas naturais e bancos de areia na maré baixa.',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itNature->id, 'position' => 2], [
                'place_id' => $pPraiaLucena->id,
                'duration_minutes' => 90,
                'estimated_cost' => 0.00,
                'reason' => 'Banho de mar em águas mornas sob a sombra dos extensos coqueirais.',
            ]);
            ItineraryItem::updateOrCreate(['itinerary_id' => $itNature->id, 'position' => 3], [
                'place_id' => $pMiriri->id,
                'duration_minutes' => 90,
                'estimated_cost' => 25.00,
                'reason' => 'Passeio de barco pelo santuário do manguezal com condutor ambiental.',
            ]);

            // 8. Users in tenant
            User::updateOrCreate(['email' => 'gestor@lucena.pb.gov.br'], [
                'name' => 'Secretário de Turismo de Lucena',
                'password' => Hash::make('12345678'),
                'can_access_admin_panel' => true,
            ]);

            User::updateOrCreate(['email' => 'turista@exemplo.com'], [
                'name' => 'Turista Visitante',
                'password' => Hash::make('12345678'),
                'can_access_admin_panel' => false,
            ]);

        } finally {
            $tenantManager->reset();
        }
    }
}
