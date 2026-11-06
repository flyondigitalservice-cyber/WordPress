"""Gutenberg block helpers matching the Custom Floor Mats theme layout."""
import html

SITE = "https://customfloormats.ae"


def esc(t):
    return html.escape(t, quote=False)


def p(text, cls=None):
    if cls:
        return f'<!-- wp:paragraph {{"className":"{cls}"}} -->\n<p class="{cls}">{text}</p>\n<!-- /wp:paragraph -->\n\n'
    return f'<!-- wp:paragraph -->\n<p>{text}</p>\n<!-- /wp:paragraph -->\n\n'


def h(text, level=2):
    if level == 2:
        return f'<!-- wp:heading -->\n<h2 class="wp-block-heading">{text}</h2>\n<!-- /wp:heading -->\n\n'
    return f'<!-- wp:heading {{"level":{level}}} -->\n<h{level} class="wp-block-heading">{text}</h{level}>\n<!-- /wp:heading -->\n\n'


def ul(items, cls="cfm-checks"):
    lis = "".join(f'<!-- wp:list-item -->\n<li>{i}</li>\n<!-- /wp:list-item -->' for i in items)
    return f'<!-- wp:list {{"className":"{cls}"}} -->\n<ul class="wp-block-list {cls}">{lis}</ul>\n<!-- /wp:list -->\n\n'


def sc(code):
    return f'<!-- wp:shortcode -->\n{code}\n<!-- /wp:shortcode -->\n\n'


def group(cls, inner):
    return f'<!-- wp:group {{"className":"{cls}"}} -->\n<div class="wp-block-group {cls}">{inner}</div>\n<!-- /wp:group -->\n\n'


def cols(*columns, cls=None):
    inner = "".join(f'<!-- wp:column -->\n<div class="wp-block-column">{c}</div>\n<!-- /wp:column -->' for c in columns)
    if cls:
        return f'<!-- wp:columns {{"className":"{cls}"}} -->\n<div class="wp-block-columns {cls}">{inner}</div>\n<!-- /wp:columns -->\n\n'
    return f'<!-- wp:columns -->\n<div class="wp-block-columns">{inner}</div>\n<!-- /wp:columns -->\n\n'


def buttons(second_label="View catalogues", second_href=SITE + "/catalogues/"):
    return ('<!-- wp:buttons -->\n<div class="wp-block-buttons"><!-- wp:button -->\n<div class="wp-block-button">'
            '<a class="wp-block-button__link wp-element-button" href="#whatsapp">Get a quote on WhatsApp</a></div>\n'
            '<!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} -->\n<div class="wp-block-button is-style-outline">'
            f'<a class="wp-block-button__link wp-element-button" href="{second_href}">{second_label}</a></div>\n'
            '<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n')


def outline_button(label, href):
    return ('<!-- wp:buttons -->\n<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->\n'
            f'<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="{href}">{label}</a></div>\n'
            '<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n')


def intro(eyebrow, heading, paras, form_title, second=None):
    left = p(eyebrow, "cfm-eyebrow") + h(heading) + "".join(p(x) for x in paras)
    left += buttons(*second) if second else buttons()
    right = sc(f'[cfm_quote_form title="{form_title}"]')
    return group("cfm-section cfm-intro", cols(left, right, cls="cfm-intro__cols"))


def split(e1, h1, l1, e2, h2_, l2):
    a = p(e1, "cfm-eyebrow") + h(h1) + ul(l1)
    b = p(e2, "cfm-eyebrow") + h(h2_) + ul(l2)
    return group("cfm-section cfm-split", cols(a, b))


def features(eyebrow, heading, items, extra_cls=""):
    """items: list of (title, text). Rendered 4 per row like the theme's feature grid."""
    out = p(eyebrow, "cfm-eyebrow") + h(heading)
    for start in range(0, len(items), 4):
        chunk = items[start:start + 4]
        columns = []
        for i, (t, txt) in enumerate(chunk, start + 1):
            columns.append(p(f"{i:02d}", "cfm-num") + h(t, 3) + p(txt))
        out += cols(*columns, cls="cfm-features__grid")
    return group(("cfm-section cfm-features " + extra_cls).strip(), out)


def prose(eyebrow, heading, paras, bullets=None, cls="cfm-section cfm-local"):
    inner = p(eyebrow, "cfm-eyebrow") + h(heading) + "".join(p(x) for x in paras)
    if bullets:
        inner += ul(bullets)
    return group(cls, inner)


def table(head, rows, caption=None):
    th = "".join(f"<th>{c}</th>" for c in head)
    trs = "".join("<tr>" + "".join(f"<td>{c}</td>" for c in r) + "</tr>" for r in rows)
    cap = f'<figcaption class="wp-element-caption">{caption}</figcaption>' if caption else ""
    return ('<!-- wp:table {"hasFixedLayout":false,"className":"cfm-table"} -->\n'
            f'<figure class="wp-block-table cfm-table"><table><thead><tr>{th}</tr></thead><tbody>{trs}</tbody></table>{cap}</figure>\n'
            '<!-- /wp:table -->\n\n')


def faq(heading, qa):
    inner = p("FAQs", "cfm-eyebrow") + h(heading)
    for q, a in qa:
        inner += (f'<!-- wp:details -->\n<details class="wp-block-details"><summary>{q}</summary>'
                  f'<!-- wp:paragraph -->\n<p>{a}</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n\n')
    return group("cfm-section cfm-faq", inner)


def children(eyebrow, heading, parent, limit=None, style=None, after=""):
    attrs = f'parent="{parent}"'
    if limit:
        attrs += f' limit="{limit}"'
    if style:
        attrs += f' style="{style}"'
    return group("cfm-section cfm-products", p(eyebrow, "cfm-eyebrow") + h(heading) + sc(f"[cfm_children {attrs}]") + after)


def areas_pills(heading="Other areas we serve", parent="areas-we-serve", eyebrow="Nearby"):
    return group("cfm-section cfm-areas", p(eyebrow, "cfm-eyebrow") + h(heading) + sc(f'[cfm_children parent="{parent}" style="pills"]'))


def links_section(eyebrow, heading, links):
    items = [f'<a href="{SITE}{href}">{label}</a>' for label, href in links]
    return group("cfm-section cfm-related", p(eyebrow, "cfm-eyebrow") + h(heading) + ul(items))


def cta(title):
    return sc(f'[cfm_cta title="{title}"]')
