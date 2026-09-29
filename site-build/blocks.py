"""Minimal serializer for core WordPress blocks.

Every helper emits exactly the markup the block's save() function produces,
so pages open in the block editor without "invalid block" warnings. The
output is verified with @wordpress/blocks (see validate.cjs).
"""
import json
from html import escape, unescape
from urllib.parse import quote

from data import WA, TEL, BRAND

IMG_MAP = {}  # drive_id or existing key -> {"id": int, "url": str}


def _attrs(d):
    d = {k: v for k, v in d.items() if v is not None}
    return (" " + json.dumps(d, ensure_ascii=False, separators=(",", ":"))) if d else ""


def cls(*names):
    return " ".join(n for n in names if n)


def wa_link(text):
    return f"https://wa.me/{WA}?text={quote(text, safe='')}"


def wa_text(topic=None):
    if topic:
        return f"Hello {BRAND}, I am interested in {topic}. Please arrange a free home visit."
    return f"Hello {BRAND}, I would like to book a free home visit and measurement."


# ------------------------------------------------------------------ text
def p(html, class_name=None):
    a = _attrs({"className": class_name})
    c = f' class="{class_name}"' if class_name else ""
    return f"<!-- wp:paragraph{a} -->\n<p{c}>{html}</p>\n<!-- /wp:paragraph -->"


def kicker(text):
    # Accepts plain text or text with entities; normalise so "&" renders once.
    return p(escape(unescape(text), quote=False), "dce-kicker")


def lead(html):
    return p(html, "dce-lead")


def h(level, html, class_name=None):
    attrs = {}
    if level != 2:
        attrs["level"] = level
    if class_name:
        attrs["className"] = class_name
    return (f"<!-- wp:heading{_attrs(attrs)} -->\n"
            f'<h{level} class="{cls("wp-block-heading", class_name)}">{html}</h{level}>\n'
            f"<!-- /wp:heading -->")


def ul(items):
    lis = "\n".join(f"<!-- wp:list-item -->\n<li>{i}</li>\n<!-- /wp:list-item -->" for i in items)
    return f'<!-- wp:list -->\n<ul class="wp-block-list">{lis}</ul>\n<!-- /wp:list -->'


def link(href, text):
    return f'<a href="{escape(href)}">{text}</a>'


# --------------------------------------------------------------- layout
def group(inner, class_name=None, tag="div", anchor=None):
    attrs = {}
    if tag != "div":
        attrs["tagName"] = tag
    if anchor:
        attrs["anchor"] = anchor
    if class_name:
        attrs["className"] = class_name
    idattr = f' id="{anchor}"' if anchor else ""
    body = "".join(inner) if isinstance(inner, (list, tuple)) else inner
    return (f"<!-- wp:group{_attrs(attrs)} -->\n"
            f'<{tag} class="{cls("wp-block-group", class_name)}"{idattr}>{body}</{tag}>\n'
            f"<!-- /wp:group -->")


def section(inner, class_name="dce-sec", anchor=None):
    return group(inner, class_name, "section", anchor)


def columns(cols):
    body = "".join(
        f'<!-- wp:column -->\n<div class="wp-block-column">{"".join(c)}</div>\n<!-- /wp:column -->' for c in cols
    )
    return f'<!-- wp:columns -->\n<div class="wp-block-columns">{body}</div>\n<!-- /wp:columns -->'


# --------------------------------------------------------------- media
def _img(key):
    if key not in IMG_MAP:
        raise KeyError(f"image not imported: {key}")
    return IMG_MAP[key]


def image(key, alt, href=None):
    m = _img(key)
    attrs = {"id": m["id"], "sizeSlug": "large", "linkDestination": "custom" if href else "none"}
    img = f'<img src="{escape(m["url"])}" alt="{escape(alt)}" class="wp-image-{m["id"]}"/>'
    if href:
        img = f'<a href="{escape(href)}">{img}</a>'
    return (f"<!-- wp:image{_attrs(attrs)} -->\n"
            f'<figure class="wp-block-image size-large">{img}</figure>\n'
            f"<!-- /wp:image -->")


def gallery(keys, alt, columns_n=3):
    keys = [k for k in keys if k in IMG_MAP]
    if not keys:
        return ""
    inner = "".join(image(k, f"{alt} – photo {i}") for i, k in enumerate(keys, 1))
    attrs = {"columns": columns_n, "linkTo": "none"}
    return (f"<!-- wp:gallery{_attrs(attrs)} -->\n"
            f'<figure class="wp-block-gallery has-nested-images columns-{columns_n} is-cropped">{inner}</figure>\n'
            f"<!-- /wp:gallery -->")


# --------------------------------------------------------------- buttons
def button(label, href, style=None, new_tab=False):
    attrs = {"className": style} if style else {}
    extra = ' target="_blank" rel="noreferrer noopener"' if new_tab else ""
    return (f"<!-- wp:button{_attrs(attrs)} -->\n"
            f'<div class="{cls("wp-block-button", style)}"><a class="wp-block-button__link wp-element-button" href="{escape(href)}"{extra}>{label}</a></div>\n'
            f"<!-- /wp:button -->")


def buttons(*btns):
    return f'<!-- wp:buttons -->\n<div class="wp-block-buttons">{"".join(btns)}</div>\n<!-- /wp:buttons -->'


def wa_button(topic=None, label="WhatsApp us"):
    return button(label, wa_link(wa_text(topic)), "dce-wa", True)


def call_button(label=None):
    from data import PHONE
    return button(label or f"Call {PHONE}", f"tel:{TEL}", "is-style-outline")


# --------------------------------------------------------------- misc
def details(q, a_html):
    return (f'<!-- wp:details -->\n<details class="wp-block-details"><summary>{q}</summary>'
            f"{p(a_html)}</details>\n<!-- /wp:details -->")


def shortcode(sc):
    return f"<!-- wp:shortcode -->\n{sc}\n<!-- /wp:shortcode -->"
