@extends('layouts.public')
@section('title', $title . ' — Rota Viva')
@section('description', $description)

@section('content')
    <section class="page-hero page-hero--compact">
        <div class="page-container page-hero__content">
            <p class="eyebrow">Institucional · Rota Viva</p>
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container" style="max-width: 840px; margin: 0 auto; line-height: 1.8; color: #334155;">
            @php
                $municipalityName = $currentTenant->nome ?? 'município';
                $municipalityState = $currentTenant->uf ?? null;
                $municipalityLabel = $municipalityState ? $municipalityName.' - '.$municipalityState : $municipalityName;
                $contactEmail = 'turismo@'.str($municipalityName)->slug().'.gov.br';
            @endphp

            @if (Route::currentRouteName() === 'about')
                <article class="editorial-content">
                    <h2>O que é o Rota Viva?</h2>
                    <p>O <strong>Rota Viva</strong> é uma plataforma municipal de turismo inteligente e governança territorial desenvolvida para transformar a forma como visitantes planejam suas experiências e como as cidades gerenciam seu patrimônio turístico.</p>
                    
                    <h3>Princípios Fundamentais</h3>
                    <ul>
                        <li><strong>Dados Oficiais e Validados:</strong> Todas as informações de atrativos, horários, acessibilidade e comércios locais são homologadas pela gestão pública municipal, eliminando alucinações e informações desatualizadas.</li>
                        <li><strong>Rotas Adaptativas e Dinâmicas:</strong> Se o contexto do visitante mudar durante a jornada (como chuva, restrições de mobilidade ou tempo), a rota se reorganiza preservando o tempo e o orçamento.</li>
                        <li><strong>Inclusão e Acessibilidade Radical:</strong> Mapeamento detalhado de recursos como rampas, piso tátil, intérpretes de Libras, braille e audiodescrição em todos os atrativos.</li>
                        <li><strong>Fortalecimento do Trade Local:</strong> Visibilidade qualificada para condutores nativos, restaurantes regionais, artesãos e pousadas credenciadas com o Selo de Qualidade Municipal.</li>
                    </ul>
                </article>

            @elseif (Route::currentRouteName() === 'contact')
                <article class="editorial-content">
                    <h2>Canais Oficiais de Atendimento</h2>
                    <p>Entre em contato direto com a equipe técnica da Secretaria Municipal de Turismo de {{ $municipalityName }}.</p>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin: 24px 0;">
                        <h3 style="margin-top: 0; color: #0f172a;">Secretaria Municipal de Turismo de {{ $municipalityName }}</h3>
                        <p style="margin-bottom: 8px;">📍 <strong>Endereço:</strong> Praça Central, s/n — Centro, {{ $municipalityLabel }}</p>
                        <p style="margin-bottom: 8px;">✉ <strong>E-mail:</strong> <a href="mailto:{{ $contactEmail }}" style="color: #0284c7;">{{ $contactEmail }}</a></p>
                        <p style="margin-bottom: 8px;">📞 <strong>Telefone Geral:</strong> (83) 3293-1000</p>
                        <p style="margin-bottom: 0;">⏱ <strong>Horário de Atendimento:</strong> Segunda a Sexta, das 08h às 14h</p>
                    </div>
                </article>

            @elseif (Route::currentRouteName() === 'accessibility.resources')
                <article class="editorial-content">
                    <h2>Recursos de Acessibilidade no Território</h2>
                    <p>No Rota Viva, a acessibilidade é um critério de planejamento territorial. Nossos dados detalham as condições reais de cada local:</p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin: 24px 0;">
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; border-radius: 8px;">
                            <strong style="color: #166534; display: block; margin-bottom: 4px;">♿ Mobilidade Física</strong>
                            <p style="font-size: 0.9rem; margin: 0;">Rampas de acesso padronizadas, calçadas acessíveis e banheiros adaptados para cadeirantes.</p>
                        </div>
                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 16px; border-radius: 8px;">
                            <strong style="color: #1e40af; display: block; margin-bottom: 4px;">🤟 Acessibilidade Comunicativa</strong>
                            <p style="font-size: 0.9rem; margin: 0;">Equipes e condutores com capacitação básica em Libras nos principais equipamentos culturais.</p>
                        </div>
                        <div style="background: #fefce8; border: 1px solid #fef08a; padding: 16px; border-radius: 8px;">
                            <strong style="color: #854d0e; display: block; margin-bottom: 4px;">👁️ Acessibilidade Sensorial</strong>
                            <p style="font-size: 0.9rem; margin: 0;">Piso tátil direcional e de alerta, sinalização em braille e audiodescrição para monumentos.</p>
                        </div>
                    </div>
                </article>

            @elseif (Route::currentRouteName() === 'accessibility.statement')
                <article class="editorial-content">
                    <h2>Declaração de Compromisso com a Acessibilidade</h2>
                    <p>O município de {{ $municipalityName }} e a plataforma Rota Viva têm o compromisso de assegurar a acessibilidade digital e territorial para todas as pessoas, incluindo idosos e pessoas com deficiência (PcD).</p>
                    
                    <h3>Conformidade Digital</h3>
                    <p>Este portal segue as diretrizes do Modelo de Acessibilidade em Governo Eletrônico (eMAG) e WCAG 2.1 nível AA, incluindo:</p>
                    <ul>
                        <li>Atalhos de teclado e links para salto direto de conteúdo (Skip to content).</li>
                        <li>Contraste visual ajustável com alternador de Alto Contraste.</li>
                        <li>Controle dinâmico de redimensionamento de fonte sem quebra de layout.</li>
                        <li>Estrutura semântica HTML5 compatível com leitores de tela NVDA, TalkBack e JAWS.</li>
                    </ul>
                </article>

            @elseif (Route::currentRouteName() === 'help')
                <article class="editorial-content">
                    <h2>Dúvidas Frequentes (FAQ)</h2>
                    
                    <h3>Como crio uma rota personalizada?</h3>
                    <p>Acesse a seção <strong>Criar Rota</strong> e digite em linguagem natural o que deseja (exemplo: <em>"Tenho 3 horas, estou com meus filhos e quero gastar até R$ 80"</em>). O sistema interpreta suas preferências e monta o itinerário ideal com atrativos abertos no horário.</p>

                    <h3>O que acontece se começar a chover durante o passeio?</h3>
                    <p>Na tela da sua rota, clique no botão <strong>"Começou a chover"</strong>. O motor de rota substitui imediatamente atividades ao ar livre por experiências cobertas, mantendo o restante do trajeto.</p>

                    <h3>O que significa o Selo de Qualidade Municipal?</h3>
                    <p>O selo identifica pousadas, restaurantes e guias cadastrados e fiscalizados pela Secretaria de Turismo, garantindo segurança e preços justos.</p>
                </article>

            @elseif (Route::currentRouteName() === 'privacy')
                <article class="editorial-content">
                    <h2>Privacidade e Proteção de Dados (LGPD)</h2>
                    <p>O Rota Viva foi desenvolvido sob o princípio de <em>Privacy by Design</em>, em estrita conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018):</p>
                    <ul>
                        <li><strong>Geração Anônima:</strong> Turistas podem criar rotas sem obrigatoriedade de cadastro pessoal ou coleta de CPF.</li>
                        <li><strong>Geolocalização Voluntária:</strong> O acesso ao GPS do visitante é utilizado exclusivamente no navegador para traçar trajetos e nunca é gravado no servidor.</li>
                        <li><strong>Transparência Governamental:</strong> Os dados gerados são consolidados de forma agregada e anônima para geração de mapas de calor e políticas públicas de turismo.</li>
                    </ul>
                </article>

            @else
                <article class="editorial-content">
                    <h2>Termos de Uso da Plataforma</h2>
                    <p>Ao utilizar o portal Rota Viva, o visitante concorda com os termos de uso dos serviços públicos de informação turística.</p>
                    <p>Todas as informações sobre horários de funcionamento, taxas de visitação e acessibilidade são validadas periodicamente junto aos administradores dos atrativos e estabelecimentos credenciados.</p>
                </article>
            @endif

            <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
                <a class="text-link" href="{{ route('home') }}">
                    ← Voltar para a página inicial
                </a>
            </div>
        </div>
    </section>
@endsection
