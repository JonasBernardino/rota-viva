from __future__ import annotations

from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm, mm
from reportlab.platypus import (
    Flowable,
    ListFlowable,
    ListItem,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path(__file__).resolve().parents[1]
OUTPUT_DIR = ROOT / "output" / "pdf"
OUTPUT_PATH = OUTPUT_DIR / "documentacao-rota-viva.pdf"

GREEN = colors.HexColor("#003f35")
GREEN_2 = colors.HexColor("#0b5a4b")
CREAM = colors.HexColor("#f4f0e8")
CREAM_2 = colors.HexColor("#fbf8f2")
RUST = colors.HexColor("#c75b3a")
INK = colors.HexColor("#171717")
MUTED = colors.HexColor("#6e6b63")
LINE = colors.HexColor("#d9d0c2")
WHITE = colors.white


class SectionDivider(Flowable):
    def __init__(self, title: str, subtitle: str):
        super().__init__()
        self.title = title
        self.subtitle = subtitle
        self.height = 145

    def draw(self):
        c = self.canv
        width = self._doctemplate.width
        c.saveState()
        c.setFillColor(GREEN)
        c.roundRect(0, 0, width, self.height, 8, stroke=0, fill=1)
        c.setFillColor(RUST)
        c.setFont("Helvetica-Bold", 9)
        c.drawString(22, 98, "ROTA VIVA")
        c.setFillColor(WHITE)
        c.setFont("Times-Roman", 28)
        c.drawString(22, 63, self.title)
        c.setFillColor(colors.HexColor("#d9ebe4"))
        c.setFont("Helvetica", 10)
        c.drawString(22, 38, self.subtitle)
        c.restoreState()


class ArchitectureDiagram(Flowable):
    def __init__(self):
        super().__init__()
        self.height = 255

    def draw_box(self, c, x, y, w, h, title, subtitle="", fill=CREAM_2, stroke=LINE):
        c.setFillColor(fill)
        c.setStrokeColor(stroke)
        c.roundRect(x, y, w, h, 7, stroke=1, fill=1)
        c.setFillColor(GREEN)
        c.setFont("Helvetica-Bold", 8.5)
        c.drawString(x + 9, y + h - 17, title)
        if subtitle:
            c.setFillColor(MUTED)
            c.setFont("Helvetica", 7.3)
            for index, line in enumerate(subtitle.split("\n")):
                c.drawString(x + 9, y + h - 32 - (index * 10), line)

    def draw_arrow(self, c, x1, y1, x2, y2):
        c.setStrokeColor(GREEN_2)
        c.setLineWidth(1)
        c.line(x1, y1, x2, y2)
        c.setFillColor(GREEN_2)
        c.circle(x2, y2, 2.2, stroke=0, fill=1)

    def draw(self):
        c = self.canv
        width = self._doctemplate.width
        c.saveState()
        self.draw_box(c, 0, 190, width, 45, "Entrada HTTP", "rota-viva.test, rota-viva.lucena.test, rota-viva.cabedelo.test", fill=CREAM)
        self.draw_box(c, 0, 115, 145, 52, "ResolveTenantMiddleware", "Identifica municipio por dominio\nou slug local.")
        self.draw_box(c, 170, 115, 145, 52, "Laravel monolito modular", "Controllers, Services, DTOs,\nPolicies e Requests.")
        self.draw_box(c, 340, 115, 145, 52, "Banco PostgreSQL", "Tabelas compartilhadas com\nmunicipio_id.")
        self.draw_box(c, 0, 25, 145, 58, "Portal publico", "Home, catalogo, mapa,\nrotas e empreendedores.")
        self.draw_box(c, 170, 25, 145, 58, "Painel municipal", "Gestao de conteudo,\naparencia e indicadores.")
        self.draw_box(c, 340, 25, 145, 58, "IA estruturada", "JSON validado, fallback local,\nOllama/Gemini/DeepSeek.")
        self.draw_arrow(c, width / 2, 190, 72, 167)
        self.draw_arrow(c, 145, 141, 170, 141)
        self.draw_arrow(c, 315, 141, 340, 141)
        self.draw_arrow(c, 72, 115, 72, 83)
        self.draw_arrow(c, 242, 115, 242, 83)
        self.draw_arrow(c, 412, 115, 412, 83)
        c.restoreState()


def stylesheet():
    styles = getSampleStyleSheet()
    styles.add(ParagraphStyle("CoverTitle", parent=styles["Title"], fontName="Times-Roman", fontSize=40, leading=42, textColor=WHITE, alignment=TA_LEFT, spaceAfter=12))
    styles.add(ParagraphStyle("CoverSubtitle", parent=styles["BodyText"], fontName="Helvetica", fontSize=12, leading=18, textColor=colors.HexColor("#e6f2ee")))
    styles.add(ParagraphStyle("Eyebrow", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=8, leading=10, textColor=RUST, spaceAfter=8))
    styles.add(ParagraphStyle("Heading", parent=styles["Heading1"], fontName="Times-Roman", fontSize=25, leading=29, textColor=GREEN, spaceBefore=10, spaceAfter=8))
    styles.add(ParagraphStyle("Subheading", parent=styles["Heading2"], fontName="Helvetica-Bold", fontSize=12, leading=15, textColor=GREEN, spaceBefore=8, spaceAfter=5))
    styles.add(ParagraphStyle("Body", parent=styles["BodyText"], fontName="Helvetica", fontSize=9.2, leading=14, textColor=INK, spaceAfter=6))
    styles.add(ParagraphStyle("Small", parent=styles["BodyText"], fontName="Helvetica", fontSize=7.6, leading=10, textColor=MUTED))
    styles.add(ParagraphStyle("CardTitle", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=10, leading=12, textColor=GREEN, spaceAfter=4))
    styles.add(ParagraphStyle("Centered", parent=styles["BodyText"], fontName="Helvetica", fontSize=9, leading=13, textColor=INK, alignment=TA_CENTER))
    return styles


def bullet_list(items, styles):
    return Table(
        [[Paragraph("- " + item, styles["Body"])] for item in items],
        colWidths=[15.4 * cm],
        style=TableStyle([
            ("LEFTPADDING", (0, 0), (-1, -1), 0),
            ("RIGHTPADDING", (0, 0), (-1, -1), 0),
            ("TOPPADDING", (0, 0), (-1, -1), 1),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 1),
        ]),
    )


def table(data, widths):
    header_style = ParagraphStyle(
        "TableHeader",
        fontName="Helvetica-Bold",
        fontSize=8,
        leading=10,
        textColor=WHITE,
    )
    cell_style = ParagraphStyle(
        "TableCell",
        fontName="Helvetica",
        fontSize=8,
        leading=10.5,
        textColor=INK,
    )
    wrapped_data = []
    for row_index, row in enumerate(data):
        style = header_style if row_index == 0 else cell_style
        wrapped_data.append([
            cell if hasattr(cell, "wrap") else Paragraph(str(cell), style)
            for cell in row
        ])

    t = Table(wrapped_data, colWidths=widths, hAlign="LEFT")
    t.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), GREEN),
        ("TEXTCOLOR", (0, 0), (-1, 0), WHITE),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 8),
        ("LEADING", (0, 0), (-1, -1), 10),
        ("TEXTCOLOR", (0, 1), (-1, -1), INK),
        ("BACKGROUND", (0, 1), (-1, -1), CREAM_2),
        ("GRID", (0, 0), (-1, -1), 0.35, LINE),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("LEFTPADDING", (0, 0), (-1, -1), 7),
        ("RIGHTPADDING", (0, 0), (-1, -1), 7),
        ("TOPPADDING", (0, 0), (-1, -1), 6),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
    ]))
    return t


def card(title, body, styles):
    return Table(
        [[Paragraph(title, styles["CardTitle"]), Paragraph(body, styles["Small"])]],
        colWidths=[4.3 * cm, 10.9 * cm],
        style=TableStyle([
            ("BACKGROUND", (0, 0), (-1, -1), CREAM_2),
            ("BOX", (0, 0), (-1, -1), 0.45, LINE),
            ("LEFTPADDING", (0, 0), (-1, -1), 9),
            ("RIGHTPADDING", (0, 0), (-1, -1), 9),
            ("TOPPADDING", (0, 0), (-1, -1), 8),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
            ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ]),
    )


def page_decor(canvas, doc):
    canvas.saveState()
    canvas.setFillColor(CREAM_2)
    canvas.rect(0, 0, A4[0], A4[1], fill=1, stroke=0)
    canvas.setFillColor(GREEN)
    canvas.rect(0, 0, A4[0], 12 * mm, fill=1, stroke=0)
    canvas.setFillColor(MUTED)
    canvas.setFont("Helvetica", 7.5)
    canvas.drawString(18 * mm, 7.2 * mm, "Rota Viva - Documentacao do Sistema")
    canvas.drawRightString(A4[0] - 18 * mm, 7.2 * mm, f"Pagina {doc.page}")
    canvas.restoreState()


def cover(canvas, doc):
    canvas.saveState()
    canvas.setFillColor(GREEN)
    canvas.rect(0, 0, A4[0], A4[1], fill=1, stroke=0)
    image_path = ROOT / "public" / "images" / "rota-viva-hero.webp"
    if image_path.exists():
        canvas.drawImage(str(image_path), A4[0] * 0.44, 0, A4[0] * 0.56, A4[1], preserveAspectRatio=False, mask="auto")
        canvas.setFillColor(colors.Color(0, 0.22, 0.18, alpha=0.62))
        canvas.rect(A4[0] * 0.44, 0, A4[0] * 0.56, A4[1], fill=1, stroke=0)
    canvas.setFillColor(CREAM)
    canvas.rect(0, 0, A4[0] * 0.58, A4[1], fill=1, stroke=0)
    canvas.setFillColor(GREEN)
    canvas.setFont("Helvetica-Bold", 24)
    canvas.drawString(24 * mm, A4[1] - 43 * mm, "ROTA VIVA")
    canvas.setFillColor(RUST)
    canvas.setFont("Helvetica-Bold", 8.5)
    canvas.drawString(24 * mm, A4[1] - 67 * mm, "DOCUMENTACAO TECNICA E ARQUITETURA")
    canvas.setFillColor(INK)
    canvas.setFont("Times-Roman", 31)
    canvas.drawString(24 * mm, A4[1] - 88 * mm, "Sistema de turismo")
    canvas.drawString(24 * mm, A4[1] - 103 * mm, "inteligente municipal")
    canvas.setFillColor(MUTED)
    canvas.setFont("Helvetica", 10)
    canvas.drawString(24 * mm, A4[1] - 121 * mm, "MVP com portal publico, painel municipal")
    canvas.drawString(24 * mm, A4[1] - 127 * mm, "rotas adaptativas, IA estruturada")
    canvas.drawString(24 * mm, A4[1] - 133 * mm, "e seguranca aplicada.")
    canvas.setFillColor(GREEN)
    canvas.roundRect(24 * mm, 36 * mm, 70 * mm, 15 * mm, 4, fill=1, stroke=0)
    canvas.setFillColor(WHITE)
    canvas.setFont("Helvetica-Bold", 8)
    canvas.drawCentredString(59 * mm, 42 * mm, "Versao tecnica - Agosto de 2026")
    canvas.restoreState()


def build_story():
    styles = stylesheet()
    story = []

    story.append(PageBreak())
    story.append(Paragraph("RESUMO EXECUTIVO", styles["Eyebrow"]))
    story.append(Paragraph("O que o Rota Viva entrega", styles["Heading"]))
    story.append(Paragraph("O Rota Viva e uma plataforma municipal de turismo inteligente. O visitante descreve como deseja viver a cidade, recebe uma rota personalizada, informa mudancas de contexto como chuva e visualiza uma rota adaptada com explicacao transparente. A prefeitura acompanha os padroes de uso por indicadores agregados e utiliza esse conhecimento para melhorar a gestao do territorio.", styles["Body"]))
    story.append(table([
        ["Ambiente", "Responsabilidade"],
        ["Portal publico", "Home dinamica por municipio, catalogo, detalhes, mapa, criacao de rota, adaptacao e area de empreendedores."],
        ["Painel municipal", "Gestao de atrativos, eventos, estabelecimentos, aparencia da home, uploads e indicadores."],
        ["Superadmin", "Criacao de novas cidades, dominios locais e usuarios gestores."],
        ["Motor inteligente", "Pontuacao deterministica, validacao de restricoes, IA com JSON estruturado e fallback local."],
    ], [4 * cm, 12 * cm]))
    story.append(Spacer(1, 12))
    story.append(Paragraph("Stack obrigatoria atendida", styles["Subheading"]))
    story.append(table([
        ["Camada", "Tecnologia aplicada"],
        ["Backend", "PHP 8.3+ e Laravel 13.x"],
        ["Banco", "PostgreSQL com multi-tenancy por coluna municipio_id"],
        ["Interface", "Blade, Bootstrap 5, CSS autoral e JavaScript"],
        ["Build", "Vite"],
        ["Mapa", "Leaflet, Leaflet Heat e OpenStreetMap"],
        ["IA", "Providers DeepSeek, Gemini e Ollama com saida JSON validada"],
        ["Qualidade", "PHPUnit, rate limits, headers de seguranca, auditoria e soft deletes"],
        ["Versionamento", "Git"],
    ], [4 * cm, 12 * cm]))

    story.append(PageBreak())
    story.append(SectionDivider("Arquitetura", "Monolito modular, tenant por coluna e dominios municipais."))
    story.append(Spacer(1, 12))
    story.append(ArchitectureDiagram())
    story.append(Paragraph("Modelo arquitetural", styles["Subheading"]))
    story.append(Paragraph("A aplicacao segue um monolito modular. Essa escolha reduz complexidade operacional para o MVP, preserva velocidade de desenvolvimento e mantem os dominios de negocio separados por controllers, services, models, policies e middlewares.", styles["Body"]))
    story.append(bullet_list([
        "Resolucao do municipio por dominio no ResolveTenantMiddleware e TenantManager.",
        "Todas as tabelas municipais compartilham o mesmo banco, isoladas por municipio_id.",
        "O painel municipal usa Gates para impedir acesso cruzado entre cidades.",
        "A camada de IA nunca e fonte de verdade para locais, horarios ou precos; ela apenas interpreta e explica.",
    ], styles))
    story.append(PageBreak())
    story.append(Paragraph("BANCO DE DADOS", styles["Eyebrow"]))
    story.append(Paragraph("Multi-tenant por coluna", styles["Heading"]))
    story.append(Paragraph("A arquitetura foi ajustada de schemas por cidade para multi-tenancy por coluna. Essa decisao e mais adequada para milhares de municipios, porque simplifica migrations, indices, relatorios, backups e operacao do banco. Particionamento fisico pode ser introduzido no futuro caso volume e custo justifiquem.", styles["Body"]))
    story.append(table([
        ["Grupo", "Tabelas e responsabilidades"],
        ["Plataforma", "municipios, dominios_municipios, users e permissoes de acesso por flags."],
        ["Conteudo turistico", "categorias, atrativos, horarios, midias, acessibilidade, estabelecimentos e eventos."],
        ["Rotas", "preferencias_visitantes, roteiros, itens de roteiro, adaptacoes e itens de adaptacao."],
        ["Inteligencia municipal", "eventos_jornada, logs_auditoria e indicadores agregados."],
        ["Aparencia", "campos de branding, banner, logo e economia local em municipios."],
    ], [4.3 * cm, 11.7 * cm]))
    story.append(Spacer(1, 10))
    story.append(card("Regra de isolamento", "O usuario nao informa municipio_id manualmente. O tenant e resolvido pelo dominio e aplicado pelo backend nos filtros, criacoes e permissoes.", styles))

    story.append(PageBreak())
    story.append(Paragraph("FLUXOS PRINCIPAIS", styles["Eyebrow"]))
    story.append(Paragraph("Jornada do visitante", styles["Heading"]))
    story.append(table([
        ["Etapa", "Comportamento do sistema"],
        ["1. Entrada", "A home do municipio apresenta identidade, banner, busca e atalhos para descobrir experiencias."],
        ["2. Preferencias", "Visitante informa humor, interesses, tempo, orcamento, companhia, mobilidade, acessibilidade e intensidade."],
        ["3. Geracao", "Motor deterministico filtra e pontua atrativos, calcula tempo/custo e salva a rota. A IA apoia interpretacao do texto livre, titulo, resumo e justificativas."],
        ["4. Mudanca", "Evento RAIN_STARTED identifica paradas externas afetadas."],
        ["5. Adaptacao", "Sistema preserva o que continua valido, substitui o necessario, recalcula e explica a mudanca."],
        ["6. Inteligencia", "Eventos agregados alimentam indicadores administrativos sem expor dados sensiveis."],
    ], [4 * cm, 12 * cm]))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Criacao de rota com IA segura", styles["Subheading"]))
    story.append(Paragraph("O diferencial da criacao de rota e combinar inteligencia artificial com regras confiaveis de negocio. O visitante pode escrever em linguagem natural - por exemplo, quero uma experiencia tranquila, cultural, com crianca, quatro horas e orcamento de R$ 150 - e o sistema converte essa intencao em criterios objetivos sem abrir mao de validacoes locais.", styles["Body"]))
    story.append(bullet_list([
        "A IA interpreta preferencias subjetivas, como humor, companhia, intensidade desejada e interesses culturais.",
        "O backend mantem a autoridade sobre dados oficiais: atrativos, horarios, custos, acessibilidade, duracao e disponibilidade.",
        "Antes de qualquer resposta ser exibida, os IDs retornados sao validados contra o banco do municipio atual.",
        "Se a IA falhar, demorar ou retornar JSON invalido, a rota continua funcionando com pontuacao deterministica e explicacoes pre-formatadas.",
    ], styles))
    story.append(Spacer(1, 6))
    story.append(table([
        ["Camada", "Papel na rota"],
        ["Visitante", "Expressa desejo em texto livre ou formulario guiado."],
        ["IA", "Transforma intencao em criterios, nomeia a experiencia e redige motivos compreensiveis."],
        ["Motor deterministico", "Filtra locais impossiveis, pontua candidatos, controla tempo/orcamento e ordena as paradas."],
        ["Persistencia", "Salva preferencias, roteiro, itens, custos, duracao, justificativas e eventos para analise posterior."],
    ], [4 * cm, 12 * cm]))
    story.append(Paragraph("Motor de adaptacao", styles["Subheading"]))
    story.append(bullet_list([
        "Primeiro evento implementado: RAIN_STARTED.",
        "Atividades internas sao preservadas; atividades externas sao reavaliadas.",
        "Substituicoes registram o que saiu, o que entrou e o motivo da mudanca.",
    ], styles))

    story.append(PageBreak())
    story.append(Paragraph("INTELIGENCIA ARTIFICIAL", styles["Eyebrow"]))
    story.append(Paragraph("IA com saida estruturada", styles["Heading"]))
    story.append(Paragraph("A IA atua como camada de interpretacao e comunicacao. Ela transforma linguagem natural em criterios, sugere titulo e resumo, e escreve justificativas. O backend valida IDs, restricoes, horarios e precos antes de aceitar qualquer resposta.", styles["Body"]))
    story.append(table([
        ["Provider", "Uso"],
        ["Ollama", "Execucao local para desenvolvimento e demonstracao sem dependencia externa."],
        ["Gemini", "Provider alternativo configuravel por variaveis de ambiente."],
        ["DeepSeek", "Provider alternativo configuravel por variaveis de ambiente."],
        ["Fallback", "Explicacoes pre-formatadas e rota funcional quando a API falha ou excede timeout."],
    ], [4 * cm, 12 * cm]))
    story.append(Spacer(1, 10))
    story.append(card("Aviso ao usuario", "As telas de rota exibem aviso de que a IA pode apoiar explicacoes, mas as informacoes oficiais sao validadas pelo municipio.", styles))
    story.append(Spacer(1, 8))
    story.append(Paragraph("Por que isso e diferente de uma busca comum", styles["Subheading"]))
    story.append(bullet_list([
        "A experiencia nao lista lugares isolados; ela monta uma sequencia viavel dentro do tempo e do orcamento.",
        "Cada parada tem motivo claro, ligado ao perfil informado pelo visitante.",
        "A rota pode mudar durante o percurso sem jogar fora tudo que ainda faz sentido.",
        "A prefeitura aprende com a jornada sem depender de dados pessoais identificaveis.",
    ], styles))

    story.append(PageBreak())
    story.append(Paragraph("SEGURANCA", styles["Eyebrow"]))
    story.append(Paragraph("Controles implementados", styles["Heading"]))
    story.append(table([
        ["Controle", "Aplicacao"],
        ["Autenticacao", "Login exclusivo para gestao e superadmin. Area do gestor aparece apenas para usuarios autorizados."],
        ["Autorizacao", "Gates access-admin-panel e manage-platform controlam painel municipal e plataforma."],
        ["Rate limit", "Login, geracao de rota, adaptacao e acoes administrativas possuem limites dedicados."],
        ["CSP e headers", "SecurityHeadersMiddleware aplica CSP, X-Frame-Options, nosniff, Referrer-Policy e Permissions-Policy."],
        ["Uploads", "Imagens restritas a jpg, jpeg, png e webp, com tamanho e dimensoes controladas. SVG bloqueado."],
        ["Auditoria", "Acoes administrativas geram logs de auditoria."],
        ["Soft delete", "Atrativos, estabelecimentos, eventos e roteiros usam exclusao logica."],
        ["Privacidade", "Politica publica descreve finalidade, dados tratados, retencao e direitos."],
        ["Analytics", "Eventos de jornada sao agregados; valores sensiveis como orcamento e tempo exatos nao sao expostos nos indicadores."],
    ], [4 * cm, 12 * cm]))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Recomendacao para producao: revisar CSP final para remover fontes de desenvolvimento, habilitar HTTPS, configurar storage privado/publico com politicas explicitas e ativar monitoramento de erros.", styles["Body"]))

    story.append(PageBreak())
    story.append(Paragraph("RELATORIOS E INTELIGENCIA MUNICIPAL", styles["Eyebrow"]))
    story.append(Paragraph("Diferencial dos relatorios", styles["Heading"]))
    story.append(Paragraph("O painel municipal nao serve apenas para contar acessos. Ele transforma as escolhas dos visitantes em sinais de gestao: quais interesses aparecem com mais frequencia, quais atrativos sao mais recomendados, quais paradas precisam ser substituidas por chuva, que tipos de acessibilidade sao mais demandados e onde existem lacunas de oferta turistica.", styles["Body"]))
    story.append(table([
        ["Indicador", "Decisao que apoia"],
        ["Rotas geradas", "Mede demanda real por experiencias e periodos de maior procura."],
        ["Interesses mais selecionados", "Ajuda a priorizar campanhas, conteudo e novos roteiros tematicos."],
        ["Atrativos mais recomendados", "Mostra quais locais sustentam melhor os perfis de visitantes."],
        ["Adaptacoes por chuva", "Revela dependencia de atividades ao ar livre e necessidade de opcoes cobertas."],
        ["Demandas de acessibilidade", "Orienta melhorias em infraestrutura, comunicacao e atendimento."],
        ["Demandas nao atendidas", "Aponta oportunidades para novos negocios, eventos ou parcerias locais."],
    ], [4.4 * cm, 11.6 * cm]))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Como os relatorios sao protegidos", styles["Subheading"]))
    story.append(bullet_list([
        "Os dados exibidos no painel sao agregados por municipio, perfil e evento de jornada.",
        "Valores sensiveis como orcamento e tempo exatos sao convertidos em faixas para reduzir exposicao.",
        "A prefeitura visualiza tendencias operacionais, nao uma linha do tempo pessoal identificavel do visitante.",
        "O registro de adaptacoes permite entender o que precisou mudar e por que, sem depender de relatos manuais.",
    ], styles))
    story.append(Spacer(1, 8))
    story.append(card("Valor estrategico", "A rota deixa de ser apenas um produto para o visitante e vira um sensor territorial. Cada jornada ajuda a cidade a entender demanda, fragilidades e oportunidades de economia local.", styles))

    story.append(PageBreak())
    story.append(Paragraph("MODULOS FUNCIONAIS", styles["Eyebrow"]))
    story.append(Paragraph("O que cada area cobre", styles["Heading"]))
    for title, body in [
        ("Home municipal", "Conteudo, logo, banner e economia local configuraveis por cidade."),
        ("Catalogo", "Cards com imagens, detalhes, horarios, custos, tags, acessibilidade e mapa."),
        ("Criar rota", "Formulario guiado, leitura de texto livre com IA, validacao deterministica, loading, persistencia e resultado visual com timeline."),
        ("Adaptacao", "Botao de chuva, troca controlada de atrativos e comparacao antes/depois."),
        ("Painel municipal", "Dashboard com indicadores agregados, leitura de demanda, adaptacoes, acessibilidade, conteudo e aparencia."),
        ("Superadmin", "Criacao de cidades, dominio principal e usuario gestor inicial."),
        ("Empreendedores", "Cadastro publico de negocios locais como oportunidade futura de curadoria."),
        ("Mapa da cidade", "Leaflet e OpenStreetMap exibem atrativos oficiais por municipio."),
    ]:
        story.append(card(title, body, styles))
        story.append(Spacer(1, 6))

    story.append(PageBreak())
    story.append(Paragraph("OPERACAO LOCAL", styles["Eyebrow"]))
    story.append(Paragraph("Como executar e demonstrar", styles["Heading"]))
    story.append(table([
        ["Objetivo", "Comando ou configuracao"],
        ["Instalar dependencias PHP", "composer install"],
        ["Instalar dependencias JS", "npm install"],
        ["Recriar banco", "php artisan migrate:fresh --seed"],
        ["Aplicar migrations", "php artisan migrate"],
        ["Compilar assets", "npm run build"],
        ["Modo desenvolvimento", "npm run dev"],
        ["Testes", "php artisan test"],
        ["Hosts Windows", "127.0.0.1 rota-viva.test; 127.0.0.1 rota-viva.lucena.test; 127.0.0.1 rota-viva.cabedelo.test"],
    ], [4.3 * cm, 11.7 * cm]))
    story.append(Spacer(1, 10))
    story.append(Paragraph("Roteiro de demonstracao", styles["Subheading"]))
    story.append(bullet_list([
        "Acessar o portal municipal.",
        "Criar uma rota para familia, cultura, tranquilidade, quatro horas e orcamento controlado.",
        "Exibir explicacoes e mapa.",
        "Acionar o evento de chuva.",
        "Mostrar substituicao parcial e indicadores no painel.",
    ], styles))

    story.append(PageBreak())
    story.append(SectionDivider("Conclusao", "Uma experiencia integrada, confiavel e memoravel."))
    story.append(Spacer(1, 14))
    story.append(Paragraph("O MVP cobre a jornada essencial do hackathon: descoberta, personalizacao, adaptacao e inteligencia municipal. A arquitetura atual privilegia escalabilidade operacional para muitas cidades, mantendo simplicidade de deploy e isolamento logico por municipio_id.", styles["Body"]))
    story.append(Paragraph("Mensagem de posicionamento: Rota Viva nao apresenta apenas lugares. Ele cria experiencias que acompanham o visitante e transforma cada jornada em inteligencia para o municipio.", styles["Body"]))
    return story


def main():
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    doc = SimpleDocTemplate(
        str(OUTPUT_PATH),
        pagesize=A4,
        rightMargin=18 * mm,
        leftMargin=18 * mm,
        topMargin=18 * mm,
        bottomMargin=20 * mm,
        title="Documentacao do Sistema Rota Viva",
        author="Rota Viva",
        subject="Arquitetura e documentacao tecnica do MVP",
    )
    doc.build(build_story(), onFirstPage=cover, onLaterPages=page_decor)
    print(OUTPUT_PATH)


if __name__ == "__main__":
    main()
