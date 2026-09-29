"""Build all 45 pages as block markup.

    python3 build.py [imagemap.json]

Writes out/pages.json (for the REST batch upload), out/html/<key>.html
(for validation) and out/import.json (Drive images to import).
Without an image map, placeholder IDs are used so the markup can be
validated before anything is uploaded.
"""
import json
import os
import re
import sys
from html import escape

import blocks as B
from blocks import (p, h, kicker, lead, ul, link, group, section, columns, image, gallery, button,
                    buttons, wa_button, call_button, details, shortcode, wa_link, wa_text)
from data import (IMAGES, EXISTING, CATALOGUES, PROJECTS, WAVE_INSTALL, INSTALL_PHOTO, drive_view, PHONE,
                  EMAIL, ADDRESS, HOURS, PARENT, PARENT_LEGAL, PARENT_URL, MAP_URL, BRAND, TEL)
from products import CURTAINS, BLINDS, PRODUCT_BY_KEY
from areas import AREAS

OUT = os.path.join(os.path.dirname(__file__), "out")

# ------------------------------------------------------------------ images
TOKEN_BASE = 7000000
TOKEN_URL = "https://dce-token.invalid/"
MIN_SIDE = 360  # skip photos too small to look sharp in the layout


def load_image_map(path):
    """Every Drive image gets a stable placeholder ID + URL. The installer on the
    server swaps them for the real attachment ID / URL (matched by Drive ID).
    Known dimensions are used to pick sharp hero images and drop tiny ones."""
    dims = {}
    if path and os.path.exists(path):
        with open(path) as f:
            dims = {k: v for k, v in json.load(f).items() if isinstance(v, dict) and v.get("w")}
    for n, did in enumerate(IMAGES, 1):
        d = dims.get(did)
        if d and min(d["w"], d["h"]) < MIN_SIDE:
            continue
        B.IMG_MAP[did] = {"id": TOKEN_BASE + n, "url": TOKEN_URL + did,
                          "w": d["w"] if d else 0, "h": d["h"] if d else 0, "drive": did}
    for key, (aid, url, _alt) in EXISTING.items():
        B.IMG_MAP[key] = {"id": aid, "url": url, "w": 1600, "h": 1200}


def tokenize(text):
    """Replace placeholder IDs / URLs with {{id:X}} / {{url:X}} for the installer."""
    text = re.sub(re.escape(TOKEN_URL) + r"([A-Za-z0-9_-]+)", r"{{url:\1}}", text)
    ids = {m["id"]: m["drive"] for m in B.IMG_MAP.values() if "drive" in m}
    return re.sub(r"(?<![0-9])(7\d{6})(?![0-9])", lambda mt: "{{id:" + ids[int(mt.group(1))] + "}}" if int(mt.group(1)) in ids else mt.group(1), text)


def ok(key):
    return key in B.IMG_MAP


def alt_of(key):
    if key in EXISTING:
        return EXISTING[key][2]
    return IMAGES[key][1]


def best(keys, n=1):
    """Pick the n largest imported images (by pixel area), keeping order stable."""
    keys = [k for k in keys if ok(k)]
    ranked = sorted(keys, key=lambda k: -(B.IMG_MAP[k].get("w", 0) * B.IMG_MAP[k].get("h", 0)))
    return ranked[:n]


def product_images(prod):
    return [k for k in prod.get("existing", []) if ok(k)] + [k for k in prod["imgs"] if ok(k)]


# ------------------------------------------------------------------ tree
PAGES = []  # dicts: key,title,slug,parent,excerpt,featured,build


def page(key, title, slug, parent=None, excerpt="", featured=None, build=None, existing_id=None, menu=None):
    PAGES.append(dict(key=key, title=title, slug=slug, parent=parent, excerpt=excerpt, featured=featured,
                      build=build, existing_id=existing_id, menu=menu or title))


def path_of(key):
    by = {pg["key"]: pg for pg in PAGES}
    if key == "home":
        return "/"
    parts, cur = [], by[key]
    while cur:
        parts.append(cur["slug"])
        cur = by.get(cur["parent"]) if cur["parent"] else None
    return "/" + "/".join(reversed(parts)) + "/"


def url(key):
    return path_of(key)


# ------------------------------------------------------------------ sections
def hero(kick, title_html, lead_html, img_key, topic=None, extra_btn=None):
    text = [kicker(kick), h(1, title_html), lead(lead_html),
            buttons(wa_button(topic, "WhatsApp for a free visit"), extra_btn or call_button())]
    cols = [text, [image(img_key, alt_of(img_key))]] if img_key and ok(img_key) else [text]
    return section(columns(cols), "dce-hero-b")


def trust():
    return group(ul(["Free home visit &amp; measurement across the UAE", "Made to measure", "Professional installation",
                     f'Part of <a href="{PARENT_URL}">{PARENT}</a>']), "dce-trust")


def process(dark=False):
    steps = [
        ("Message or call", f'Send your window photos on <a href="{wa_link(wa_text())}">WhatsApp</a> or call {PHONE}.'),
        ("Home visit &amp; measuring", "We visit, measure every window and bring fabric and blind samples."),
        ("Choose &amp; confirm", "Pick fabrics, headings and controls, and receive a clear quotation."),
        ("Stitch &amp; install", "Your order is made to measure and installed by our team."),
    ]
    cards = "".join(group([h(3, t), p(d)], "dce-step") for t, d in steps)
    return section([kicker("How it works"), h(2, "From first message <em>to finished window</em>"),
                    group(cards, "dce-steps")], "dce-sec dce-sec-dark" if dark else "dce-sec")


def cta_form(topic=None, title="Book your <em>free home visit</em>"):
    service_attr = f' service="{escape(topic)}"' if topic else ""
    return section([kicker("Get in touch"), h(2, title),
                    lead(f"Fill in the form and it opens WhatsApp with your details ready to send — and a copy is emailed to our team. Or message us directly on WhatsApp, or call {PHONE}."),
                    buttons(wa_button(topic, "WhatsApp now"), call_button()),
                    shortcode(f"[dce_lead_form{service_attr}]")], "dce-cta", anchor="contact")


def parent_band():
    return group([p(f'<strong>{BRAND}</strong> is the curtain and blinds studio of <strong>{PARENT}</strong> ({PARENT_LEGAL}) — for furniture, décor and complete interiors, visit our parent company.'),
                  buttons(button(f"Visit {PARENT}", PARENT_URL, "is-style-outline", True))], "dce-parent")


def card(key, title, text, img_key):
    inner = []
    if img_key and ok(img_key):
        inner.append(image(img_key, f"{title} – {BRAND}", url(key)))
    inner += [h(3, link(url(key), title)), p(text), p(link(url(key), "Explore →"), "dce-more")]
    return group(inner, "dce-card")


def product_card(k):
    pr = PRODUCT_BY_KEY[k]
    imgs = product_images(pr)
    return card(k, pr["label"], pr["lead"], imgs[0] if imgs else None)


def product_grid(keys, cols=3):
    return group("".join(product_card(k) for k in keys), "dce-grid dce-grid-4" if cols == 4 else "dce-grid")


def faq_block(items):
    return group("".join(details(q, a) for q, a in items), "dce-faq")


# ------------------------------------------------------------------ builders
def build_product(pr, hub_key):
    imgs = product_images(pr)
    hero_img = best(imgs[:4] or imgs, 1)
    hero_img = hero_img[0] if hero_img else None
    rest = [k for k in imgs if k != hero_img]
    split_img = rest[0] if rest else hero_img
    hub = "Curtains" if hub_key == "curtains" else "Blinds"
    out = [
        hero(f"{hub} · Dubai &amp; UAE", pr["h1"], pr["lead"], hero_img, pr["label"]),
        trust(),
        section(columns([
            [image(split_img, alt_of(split_img))] if split_img else [],
            [kicker(f"About {pr['label'].lower()}"), h(2, pr["title"])] + [p(x) for x in pr["intro"]]
            + [h(3, "Features &amp; options"), ul(pr["features"])],
        ]), "dce-sec dce-split"),
    ]
    gal = rest[1:10] if len(rest) > 1 else []
    if gal:
        out.append(section([kicker("Gallery"), h(2, f"{pr['label']} <em>ideas</em>"),
                            gallery(gal, pr["title"], 3)], "dce-sec dce-sec-alt"))
    out.append(section(columns([
        [kicker("Best for"), h(2, "Where they work <em>best</em>"), ul(pr["best"])],
        [kicker("Fabrics &amp; samples"), h(2, "See it <em>before</em> you order"),
         p(f'Browse our {link(url("catalogue"), "fabric catalogues")} online, then we bring physical samples to your home visit so you can judge colour and texture in your own light.'),
         buttons(button("Open catalogues", url("catalogue"), "is-style-outline"))],
    ]), "dce-sec"))
    out.append(process(dark=True))
    out.append(section([kicker("Questions"), h(2, f"{pr['label']} <em>FAQ</em>"), faq_block(pr["faq"])], "dce-sec"))
    out.append(section([kicker("You may also like"), h(2, "Related <em>products</em>"), product_grid(pr["related"], 4)], "dce-sec dce-sec-alt"))
    out.append(cta_form(pr["label"]))
    return "\n\n".join(out)


def build_area(ar, idx):
    photos = [k for k in PROJECTS if ok(k)]
    pick = [photos[(idx * 3 + i) % len(photos)] for i in range(3)] if photos else []
    hero_img = best([PRODUCT_BY_KEY[ar["picks"][0]]["imgs"][0]] if PRODUCT_BY_KEY[ar["picks"][0]]["imgs"] else [], 1)
    hero_img = hero_img[0] if hero_img else (pick[0] if pick else None)
    name = ar["name"]
    visit_line = f"Free home visit and measurement in {name} — send your location on WhatsApp and we will book a time that suits you."
    out = [
        hero(f"{'Dubai' if ar['dubai'] else 'UAE'} · Service area", f"Curtains &amp; blinds in <em>{name}</em>",
             ar["intro"], hero_img, f"curtains and blinds in {name}"),
        trust(),
        section(columns([
            [kicker("Local know-how"), h(2, f"Window dressing for {name} homes")] + [
                p(f"<strong>Typical properties:</strong> {ar['homes']}"),
                p(visit_line),
                p(f'Our showroom: {ADDRESS}. Open {HOURS}.'),
                buttons(wa_button(f"curtains and blinds in {name}", "WhatsApp us"), call_button())],
            [gallery(pick, f"Curtain and blind installation for {name}", 2)] if pick else [],
        ]), "dce-sec dce-split"),
        section([kicker("Recommended"), h(2, f"Popular choices in <em>{name}</em>"), product_grid(ar["picks"])], "dce-sec dce-sec-alt"),
        process(dark=True),
        section([kicker("Questions"), h(2, f"Curtains &amp; blinds in {name} <em>FAQ</em>"), faq_block([
            (f"Do you serve {name}?", f"Yes. {visit_line}"),
            ("Can I see fabrics before ordering?", f'Yes — browse the {link(url("catalogue"), "online catalogues")}, visit our Deira showroom, or ask us to bring samples to your visit.'),
            ("How do I get a quotation?", f"Send your window sizes and photos on WhatsApp ({PHONE}) or book a measurement visit, and we will prepare a quotation for your chosen products."),
        ])], "dce-sec"),
        section([kicker("Nearby"), h(2, "Other areas <em>we serve</em>"),
                 p(" · ".join(link(url(a["key"]), a["name"]) for a in AREAS if a["key"] != ar["key"]))], "dce-sec dce-sec-alt"),
        cta_form(f"curtains and blinds in {name}"),
    ]
    return "\n\n".join(out)


def build_hub(kind):
    items = CURTAINS if kind == "curtains" else BLINDS
    label = "Curtains" if kind == "curtains" else "Blinds"
    hero_keys = [k for pr in items for k in product_images(pr)[:1]]
    hero_img = PROJECTS[0] if kind == "curtains" and ok(PROJECTS[0]) else (best(hero_keys, 1) or [None])[0]
    intro = ("Every curtain is made to measure for your windows — from modern wave and sheer layers to tailored pinch pleats, blackout for bedrooms, motorized tracks and specialist curtains for cinemas, children and hospitals."
             if kind == "curtains" else
             "Blinds bring precise light control in a slim footprint — roller, sunscreen, zebra, Roman, vertical, wooden, aluminium, bamboo, printed and branded options, all made to measure.")
    out = [
        hero(f"The collection · {label}", f"{label} in Dubai, <em>made to measure</em>", intro, hero_img, label.lower()),
        trust(),
        section([kicker(f"{len(items)} {label.lower()} styles"), h(2, f"Choose your <em>{label.lower()}</em>"),
                 lead("Tap a style to see photos, options and answers to common questions."), product_grid([pr["key"] for pr in items])], "dce-sec"),
        process(dark=True),
        section([kicker("Not sure?"), h(2, "Send us a photo of your window"),
                 lead("Tell us the room and how much light and privacy you want, and we will recommend the best options."),
                 buttons(wa_button(label.lower(), "Ask on WhatsApp"), button("See the catalogues", url("catalogue"), "is-style-outline"))], "dce-sec dce-sec-alt dce-center"),
        cta_form(label.lower()),
    ]
    return "\n\n".join(out)


def build_home():
    installs = [k for k in PROJECTS if ok(k)]
    hero_img = PROJECTS[0] if ok(PROJECTS[0]) else None
    featured_c = ["wave", "pinch-pleat", "blackout", "sheer", "motorized", "american"]
    featured_b = ["zebra", "blackout-roller", "roman-b", "wooden", "sunscreen", "logo"]
    out = [
        hero("Curtains &amp; blinds · Dubai &amp; UAE", "Curtains &amp; blinds, <em>made to measure in Dubai</em>",
             f"Made-to-measure curtains and blinds for homes and businesses across Dubai and the UAE — measured at your home, made to order and professionally installed. Part of {link(PARENT_URL, PARENT)}.",
             hero_img, None, button("Explore curtains", url("curtains"), "is-style-outline")),
        trust(),
        section([kicker("Curtains"), h(2, "Curtains for <em>every room</em>"),
                 lead("Wave, pinch pleat, blackout, sheer, motorized and more — tailored to your windows."),
                 product_grid(featured_c),
                 buttons(button("View all curtains", url("curtains"), "is-style-outline"))], "dce-sec"),
        section([kicker("Blinds"), h(2, "Blinds for <em>precise light control</em>"),
                 lead("Zebra, roller, Roman, wooden, sunscreen and branded blinds — made to measure."),
                 product_grid(featured_b),
                 buttons(button("View all blinds", url("blinds"), "is-style-outline"))], "dce-sec dce-sec-alt"),
        section(columns([
            [image(WAVE_INSTALL[0], alt_of(WAVE_INSTALL[0]))] if ok(WAVE_INSTALL[0]) else [],
            [kicker("About us"), h(2, f"The curtain studio of <em>{PARENT}</em>"),
             p(f"{BRAND} is part of {link(PARENT_URL, PARENT)} ({PARENT_LEGAL}), with a showroom at Empire Plaza on Naif Road, Deira."),
             p("We visit your home, measure every window, help you choose fabrics and systems, then stitch and install the finished curtains and blinds."),
             buttons(button("About us", url("about"), "is-style-outline"), button(f"Visit {PARENT}", PARENT_URL, "is-style-outline", True))],
        ]), "dce-sec dce-split"),
        section([kicker("Our work"), h(2, "Recent <em>installations</em>"),
                 gallery(installs[:9], "Curtain installation in Dubai", 3),
                 buttons(button("See all projects", url("projects"), "is-style-outline"))], "dce-sec dce-sec-alt"),
        process(dark=True),
        section([kicker("Service areas"), h(2, "Across Dubai <em>and the UAE</em>"),
                 p(" · ".join(link(url(a["key"]), a["name"]) for a in AREAS)),
                 buttons(button("All service areas", url("areas"), "is-style-outline"))], "dce-sec"),
        section([kicker("Catalogues"), h(2, "Browse fabrics <em>online</em>"),
                 lead("Open our curtain, outdoor and custom-print catalogues before your visit."),
                 buttons(button("Open catalogues", url("catalogue")), wa_button("fabric catalogues", "Ask about a fabric"))], "dce-sec dce-sec-alt dce-center"),
        section([kicker("Questions"), h(2, "Frequently <em>asked</em>"), faq_block([
            ("Is the home visit free?", "Yes — we visit homes and businesses across the UAE free of charge to measure and show samples. Contact us on WhatsApp to book a time."),
            ("Do you install the curtains and blinds?", "Yes. Our team installs the tracks, rods and blinds along with your made-to-measure curtains."),
            ("Can I see fabrics before the visit?", f'Yes — open the {link(url("catalogue"), "online catalogues")} or visit our showroom at {ADDRESS}.'),
            ("What are your opening hours?", HOURS + "."),
        ])], "dce-sec"),
        section([parent_band()], "dce-sec"),
        cta_form(None),
    ]
    return "\n\n".join(out)


def build_about():
    img = WAVE_INSTALL[1] if ok(WAVE_INSTALL[1]) else None
    out = [
        hero("About us", f"The curtain studio of <em>{PARENT}</em>",
             f"{BRAND} makes and installs made-to-measure curtains and blinds for homes and businesses across Dubai and the UAE.", img, None),
        trust(),
        section(columns([
            [image(INSTALL_PHOTO[0], alt_of(INSTALL_PHOTO[0]))] if ok(INSTALL_PHOTO[0]) else [],
            [kicker("Who we are"), h(2, "Focused on <em>the window</em>"),
             p(f"We are part of {link(PARENT_URL, PARENT)}, operated by {PARENT_LEGAL}. Our showroom is at {ADDRESS}."),
             p("Our work starts with a visit to your address: we measure each window, look at the light, and bring fabric and blind samples so you can choose in your own room."),
             p("Once you confirm, your curtains and blinds are made to measure and installed by our team — tracks, rods, motors and all."),
             ul(["Curtains: wave, pinch pleat, eyelet, American style, Roman, blackout, sheer, motorized, beaded, cinema, kids and hospital",
                 "Blinds: roller, sunscreen, zebra, Roman, vertical, wooden, aluminium, bamboo, printed and logo blinds",
                 "Homes, offices, shops, clinics and hospitality projects"])],
        ]), "dce-sec dce-split"),
        process(dark=True),
        section([kicker("Our parent company"), h(2, f"Part of <em>{PARENT}</em>"),
                 lead(f"{PARENT} brings furniture, décor and interiors together — {BRAND} is its dedicated curtains and blinds studio."),
                 parent_band()], "dce-sec"),
        section([kicker("Visit us"), h(2, "Showroom &amp; <em>hours</em>"),
                 p(f"<strong>Address:</strong> {link(MAP_URL, ADDRESS)}"), p(f"<strong>Hours:</strong> {HOURS}"),
                 p(f'<strong>Phone:</strong> {link("tel:" + TEL, PHONE)} · <strong>Email:</strong> {link("mailto:" + EMAIL, EMAIL)}')], "dce-sec dce-sec-alt"),
        cta_form(None),
    ]
    return "\n\n".join(out)


def build_services():
    svc = [
        ("Free home visit &amp; measuring", "We come to your home anywhere in the UAE, measure every window and bring samples — free of charge.", "contact"),
        ("Made-to-measure curtains", "Wave, pinch pleat, eyelet, American style, Roman, blackout, sheer and more.", "curtains"),
        ("Made-to-measure blinds", "Roller, zebra, Roman, vertical, wooden, aluminium, bamboo and printed blinds.", "blinds"),
        ("Motorized systems", "Motorized curtain tracks and blinds with remote, switch or smart control.", "motorized"),
        ("Commercial &amp; healthcare", "Offices, shops, clinics and hospital cubicle curtains with tracks.", "hospital"),
        ("Branded &amp; printed blinds", "Your logo, photo or artwork printed on sunscreen and roller blinds.", "logo"),
    ]
    cards = "".join(group([h(3, link(url(k), t)), p(d), p(link(url(k), "Learn more →"), "dce-more")], "dce-card dce-cat-card") for t, d, k in svc)
    out = [
        hero("Services", "Everything for the window, <em>from one team</em>",
             "Consultation, measuring, stitching and installation for curtains and blinds across Dubai and the UAE.",
             best(PROJECTS[:6], 1)[0] if any(ok(k) for k in PROJECTS[:6]) else None, "your services"),
        trust(),
        section([kicker("What we do"), h(2, "Our <em>services</em>"), group(cards, "dce-grid")], "dce-sec"),
        process(dark=True),
        section([kicker("Where"), h(2, "Service <em>areas</em>"),
                 p(" · ".join(link(url(a["key"]), a["name"]) for a in AREAS))], "dce-sec"),
        cta_form(None),
    ]
    return "\n\n".join(out)


def build_catalogue():
    groups_html = []
    for title, desc, items in CATALOGUES:
        cards = "".join(group([
            p("PDF catalogue", "dce-tag"), h(3, escape(name)), p(f"File size: {size}. Opens in Google Drive."),
            buttons(button("View catalogue", drive_view(fid), None, True),
                    button("Ask on WhatsApp", wa_link(wa_text(f"the {name} catalogue")), "dce-wa", True)),
        ], "dce-card dce-cat-card") for name, fid, size in items)
        groups_html.append(section([kicker(title), h(2, escape(title)), lead(desc), group(cards, "dce-grid")],
                                   "dce-sec" if len(groups_html) % 2 == 0 else "dce-sec dce-sec-alt"))
    out = [
        hero("Catalogues", "Fabric &amp; blind <em>catalogues</em>",
             f"Browse our curtain, outdoor and custom-print catalogues online. Found something you like? Send the name on WhatsApp and we will bring the samples to your home visit.",
             PINCH_HERO(), "your fabric catalogues"),
        trust(),
    ] + groups_html + [
        section([parent_band()], "dce-sec"),
        cta_form("fabric catalogues"),
    ]
    return "\n\n".join(out)


def PINCH_HERO():
    from data import PINCH
    k = best(PINCH[:5], 1)
    return k[0] if k else None


def build_projects():
    installs = [k for k in PROJECTS if ok(k)]
    wave = [k for k in WAVE_INSTALL if ok(k)]
    out = [
        hero("Projects", "Our <em>installations</em>",
             "Real curtains and blinds installed by our team in homes and businesses. Want a similar look? Send us a photo on WhatsApp.",
             installs[1] if len(installs) > 1 else None, "a project like these"),
        trust(),
        section([kicker("Curtains"), h(2, "Curtain <em>projects</em>"), gallery(installs, "Curtain installation by Dubai Curtain Experts", 3)], "dce-sec"),
        section([kicker("Wave curtains"), h(2, "Wave curtain <em>installations</em>"), gallery(wave, "Wave curtain installation in Dubai", 3)], "dce-sec dce-sec-alt"),
        section([kicker("Explore"), h(2, "Find your <em>style</em>"),
                 product_grid(["wave", "american", "kids", "hospital", "logo", "printed"])], "dce-sec"),
        cta_form("a project like the ones in your gallery"),
    ]
    return "\n\n".join(out)


def build_contact():
    out = [
        hero("Contact", "Book your <em>free home visit</em>",
             f"WhatsApp is the fastest way to reach us. You can also call {PHONE}, email {EMAIL} or visit the showroom in Deira.",
             WAVE_INSTALL[2] if ok(WAVE_INSTALL[2]) else None, None),
        section(columns([
            [kicker("Contact details"), h(2, "Talk to <em>us</em>"),
             p(f'<strong>WhatsApp:</strong> {link(wa_link(wa_text()), PHONE)}'),
             p(f'<strong>Phone:</strong> {link("tel:" + TEL, PHONE)}'),
             p(f'<strong>Email:</strong> {link("mailto:" + EMAIL, EMAIL)}'),
             p(f'<strong>Showroom:</strong> {link(MAP_URL, ADDRESS)}'),
             p(f"<strong>Hours:</strong> {HOURS}"),
             buttons(wa_button(None, "WhatsApp now"), call_button())],
            [kicker("Send a request"), h(2, "Request a <em>visit</em>"),
             p("The form opens WhatsApp with your details ready to send, and emails a copy to our team."),
             shortcode("[dce_lead_form]")],
        ]), "dce-sec dce-split", anchor="contact"),
        section([parent_band()], "dce-sec dce-sec-alt"),
    ]
    return "\n\n".join(out)


def build_areas_hub():
    dubai = [a for a in AREAS if a["dubai"]]
    uae = [a for a in AREAS if not a["dubai"]]

    def area_cards(lst):
        return group("".join(group([h(3, link(url(a["key"]), a["name"])), p(a["intro"]), p(link(url(a["key"]), "View area →"), "dce-more")],
                                   "dce-card dce-cat-card") for a in lst), "dce-grid")
    out = [
        hero("Service areas", "Curtains &amp; blinds <em>across the UAE</em>",
             "From our showroom in Deira we serve homes and businesses across the UAE — Dubai, Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah and Al Ain — with a free home visit and measurement.",
             PROJECTS[2] if ok(PROJECTS[2]) else None, "service in my area"),
        trust(),
        section([kicker("Dubai"), h(2, "Dubai <em>communities</em>"), area_cards(dubai)], "dce-sec"),
        section([kicker("UAE"), h(2, "Across the <em>Emirates</em>"), area_cards(uae)], "dce-sec dce-sec-alt"),
        cta_form(None),
    ]
    return "\n\n".join(out)


def build_privacy():
    out = [section([
        h(1, "Privacy policy"),
        p(f"This policy explains how {BRAND} ({PARENT}, {PARENT_LEGAL}) handles information you share through this website."),
        h(2, "What we collect"),
        p("When you use the request form we collect the details you enter: name, phone number, email address, area, the product you are interested in and your message. When you contact us on WhatsApp, phone or email, we receive the information you choose to send."),
        h(2, "How we use it"),
        p("We use your details only to respond to your enquiry, arrange home visits, prepare quotations and deliver your order. We do not sell your information."),
        h(2, "WhatsApp"),
        p("Our form and buttons open WhatsApp, a service operated by WhatsApp LLC / Meta. Messages you send there are also subject to WhatsApp's own privacy policy."),
        h(2, "Contact"),
        p(f'For questions or to ask us to delete your information, email {link("mailto:" + EMAIL, EMAIL)} or call {link("tel:" + TEL, PHONE)}.'),
    ], "dce-sec")]
    return "\n\n".join(out)


# ------------------------------------------------------------------ site map
def define_pages():
    page("home", "Curtains & Blinds in Dubai", "home", None,
         "Made-to-measure curtains and blinds in Dubai and across the UAE. Free home visit, professional installation. Part of Casa Vera Home. WhatsApp +971 50 859 9803.",
         PROJECTS[0], build_home, existing_id=493, menu="Home")
    page("about", "About Us", "about-us", None,
         "Dubai Curtain Experts is the curtains and blinds studio of Casa Vera Home (Mukhtar Curtain LLC), with a showroom on Naif Road, Deira.",
         INSTALL_PHOTO[0], build_about, existing_id=499, menu="About")
    page("services", "Our Services", "services", None,
         "Curtain and blind services in Dubai: free home visit and measuring, made-to-measure curtains and blinds, motorization, commercial and healthcare projects.",
         PROJECTS[3], build_services, existing_id=497, menu="Services")
    page("curtains", "Curtains in Dubai", "curtains", None,
         "Made-to-measure curtains in Dubai: wave, pinch pleat, eyelet, blackout, sheer, motorized and more. Free home visit and installation. WhatsApp +971 50 859 9803.",
         PROJECTS[0], lambda: build_hub("curtains"), menu="Curtains")
    for pr in CURTAINS:
        page(pr["key"], pr["title"], pr["slug"], "curtains", pr["desc"],
             (pr.get("existing") or pr["imgs"])[0], (lambda pr=pr: build_product(pr, "curtains")), menu=pr["label"])
    page("blinds", "Blinds in Dubai", "blinds", None,
         "Made-to-measure blinds in Dubai: roller, sunscreen, zebra, Roman, vertical, wooden, aluminium, bamboo, printed and logo blinds. WhatsApp +971 50 859 9803.",
         ZEBRA_FIRST(), lambda: build_hub("blinds"), menu="Blinds")
    for pr in BLINDS:
        page(pr["key"], pr["title"], pr["slug"], "blinds", pr["desc"], pr["imgs"][0],
             (lambda pr=pr: build_product(pr, "blinds")), menu=pr["label"])
    page("catalogue", "Fabric Catalogues", "catalogue", None,
         "Browse our curtain, D3, Sunbrella outdoor and custom-print fabric catalogues online, then ask on WhatsApp for samples at your free home visit.",
         PINCH_FIRST(), build_catalogue, menu="Catalogues")
    page("projects", "Our Projects", "projects", None,
         "See curtains and blinds installed by Dubai Curtain Experts in homes and businesses across Dubai.",
         PROJECTS[1], build_projects, existing_id=495, menu="Projects")
    page("areas", "Areas We Serve", "areas-we-serve", None,
         "Curtains and blinds across Dubai — Marina, Downtown, Palm Jumeirah, Jumeirah, Dubai Hills, JVC, Deira, Mirdif — and in Abu Dhabi, Sharjah, Ajman, RAK and Al Ain.",
         PROJECTS[2], build_areas_hub, menu="Areas")
    for i, ar in enumerate(AREAS):
        page(ar["key"], f"Curtains & Blinds in {ar['name']}", ar["slug"], "areas",
             f"Made-to-measure curtains and blinds in {ar['name']}. Free home visit, professional installation and fabric samples. WhatsApp +971 50 859 9803.",
             PROJECTS[(i * 3) % len(PROJECTS)], (lambda ar=ar, i=i: build_area(ar, i)), menu=ar["name"])
    page("contact", "Contact Us", "contact-us", None,
         "Contact Dubai Curtain Experts: WhatsApp or call +971 50 859 9803, email info@dubaicurtainexperts.ae, or visit Empire Plaza, Naif Road, Deira.",
         WAVE_INSTALL[2], build_contact, existing_id=501, menu="Contact")
    page("privacy", "Privacy Policy", "privacy-policy", None,
         "How Dubai Curtain Experts handles the information you share through our website, WhatsApp, phone and email.",
         None, build_privacy, existing_id=3, menu="Privacy Policy")


def ZEBRA_FIRST():
    from data import ZEBRA
    return ZEBRA[0]


def PINCH_FIRST():
    from data import PINCH
    return PINCH[0]


def main():
    load_image_map(sys.argv[1] if len(sys.argv) > 1 else None)
    define_pages()
    os.makedirs(os.path.join(OUT, "html"), exist_ok=True)
    result = []
    for pg in PAGES:
        content = pg["build"]()
        with open(os.path.join(OUT, "html", f"{pg['key']}.html"), "w") as f:
            f.write(content)
        feat = pg["featured"]
        result.append(dict(key=pg["key"], title=pg["title"], slug=pg["slug"], parent=pg["parent"], path=path_of(pg["key"]),
                           excerpt=pg["excerpt"], featured_media=B.IMG_MAP[feat]["id"] if feat and ok(feat) else 0,
                           existing_id=pg["existing_id"], menu=pg["menu"], content=content))
    with open(os.path.join(OUT, "pages.json"), "w") as f:
        json.dump(result, f, ensure_ascii=False, indent=1)
    with open(os.path.join(OUT, "import.json"), "w") as f:
        json.dump([{"drive_id": d, "name": n, "alt": a} for d, (n, a) in IMAGES.items()], f, indent=1)
    print(f"{len(result)} pages, {len(IMAGES)} images to import")
    for r in result:
        if len(r["excerpt"]) > 165:
            print("  long excerpt:", r["key"], len(r["excerpt"]))


if __name__ == "__main__":
    main()


# ------------------------------------------------------------------ blog
def build_post(post):
    out = [lead(post["intro"]),
           group([p("<strong>Key takeaways</strong>"), ul(post["takeaways"])], "dce-takeaways")]
    for heading, items in post["sections"]:
        out.append(h(2, heading))
        for it in items:
            if isinstance(it, str):
                out.append(p(it))
            elif it[0] in ("ul", "ol"):
                out.append(ul(it[1]) if it[0] == "ul" else B.ol(it[1]))
            elif it[0] == "img":
                if ok(it[1]):
                    out.append(image(it[1], it[2]))
            elif it[0] == "h3":
                out.append(h(3, it[1]))
    out.append(h(2, "Frequently asked questions"))
    out.append(group("".join(details(q, a) for q, a in post["faq"]), "dce-faq"))
    out.append(group([
        h(3, "Talk to a curtain specialist"),
        p(f"Free home visit and measurement across the UAE. Send your window photos on WhatsApp or call {PHONE}."),
        buttons(wa_button(post["topic"], "WhatsApp us"), call_button()),
    ], "dce-post-cta"))
    return "\n\n".join(out)


# ------------------------------------------------------------------ menus + footer
def site_structure():
    from data import PARENT_URL
    kids = lambda lst: [{"key": pr["key"], "title": pr["label"]} for pr in lst]
    primary = [
        {"key": "curtains", "title": "Curtains", "children": kids(CURTAINS)},
        {"key": "blinds", "title": "Blinds", "children": kids(BLINDS)},
        {"key": "services", "title": "Services"},
        {"key": "catalogue", "title": "Catalogues"},
        {"key": "projects", "title": "Projects"},
        {"key": "areas", "title": "Areas", "children": [{"key": a["key"], "title": a["name"]} for a in AREAS]},
        {"key": "blog", "title": "Blog"},
        {"key": "about", "title": "About"},
        {"key": "contact", "title": "Contact"},
    ]
    footer = [{"key": k, "title": t} for k, t in [("about", "About us"), ("services", "Services"), ("catalogue", "Catalogues"),
                                                   ("projects", "Projects"), ("areas", "Areas we serve"), ("blog", "Blog"), ("contact", "Contact"), ("privacy", "Privacy policy")]]
    footer.append({"url": PARENT_URL, "title": "Casa Vera Home", "new_tab": True})

    def plist(pairs):
        return B.ul([f'<a href="{{{{page:{k}}}}}">{t}</a>' for k, t in pairs])
    widgets = {
        "footer-1": [
            B.p(f"Made-to-measure curtains and blinds for homes and businesses across Dubai and the UAE. Part of <a href=\"{PARENT_URL}\">{PARENT}</a> ({PARENT_LEGAL})."),
            B.p(f"{HOURS}"),
        ],
        "footer-2": [B.h(3, "Curtains"), plist([(pr["key"], pr["label"]) for pr in CURTAINS])],
        "footer-3": [B.h(3, "Blinds"), plist([(pr["key"], pr["label"]) for pr in BLINDS]),
                     B.h(3, "Company"), plist([(k, t) for k, t in [("about", "About us"), ("catalogue", "Catalogues"), ("projects", "Projects"), ("areas", "Areas we serve"), ("blog", "Blog"), ("privacy", "Privacy policy")]])],
        "footer-4": [B.h(3, "Contact"), B.ul([
            f'<a href="{MAP_URL}">{ADDRESS}</a>',
            f'<a href="tel:{TEL}">{PHONE}</a>',
            f'<a href="{wa_link(wa_text())}">WhatsApp {PHONE}</a>',
            f'<a href="mailto:{EMAIL}">{EMAIL}</a>',
        ]), B.p(f'<a href="{{{{page:contact}}}}">Book a free home visit →</a>')],
    }
    return [{"name": "Main Navigation", "location": "primary", "items": primary},
            {"name": "Footer Links", "location": "footer", "items": footer}], widgets


def write_payload():
    import gzip, base64
    pages = json.load(open(os.path.join(OUT, "pages.json")))
    menus, widgets = site_structure()
    for w, blocks_ in widgets.items():
        with open(os.path.join(OUT, "html", f"widget-{w}.html"), "w") as f:
            f.write("\n\n".join(blocks_).replace("{{page:", "/x/{{"))
    by_id = {m["id"]: m.get("drive") for m in B.IMG_MAP.values()}
    for p in pages:
        p["content"] = tokenize(p["content"])
        if by_id.get(p["featured_media"]):
            p["featured_media"] = "drive:" + by_id[p["featured_media"]]
    from blog import POSTS
    posts = []
    os.makedirs(os.path.join(OUT, "html"), exist_ok=True)
    for post in POSTS:
        html = build_post(post)
        with open(os.path.join(OUT, "html", f"post-{post['key']}.html"), "w") as f:
            f.write(html)
        feat = post["featured"]
        fm = B.IMG_MAP[feat] if ok(feat) else None
        posts.append({"key": post["key"], "title": post["title"], "slug": post["slug"], "excerpt": post["excerpt"],
                      "category": post["cat"], "content": tokenize(html),
                      "featured_media": ("drive:" + fm["drive"]) if fm and "drive" in fm else (fm["id"] if fm else 0)})
    pages.append({"key": "blog", "title": "Curtain & Blind Guides", "slug": "blog", "parent": None, "path": "/blog/",
                  "excerpt": "Guides and ideas for curtains and blinds in Dubai and the UAE — measuring, blackout, sheers, motorized curtains, cleaning and more.",
                  "featured_media": 0, "existing_id": None, "content": ""})
    payload = {"pages": [{k: p[k] for k in ("key", "title", "slug", "parent", "path", "excerpt", "featured_media", "existing_id", "content")} for p in pages],
               "menus": menus, "widgets": widgets, "posts": posts, "posts_page": "blog"}
    raw = json.dumps(payload, ensure_ascii=False, separators=(",", ":")).encode()
    b64 = base64.b64encode(gzip.compress(raw, 9)).decode()
    with open(os.path.join(OUT, "install.json"), "w") as f:
        json.dump({"gz": b64}, f)
    print("payload raw", len(raw), "b64", len(b64))


if __name__ == "__main__" and "--payload" in sys.argv:
    write_payload()
