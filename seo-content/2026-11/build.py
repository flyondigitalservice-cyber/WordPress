"""Build page payloads (JSON) for the new SEO pages."""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from blocks import *  # noqa
from data_areas import DUBAI_AREAS
from data_interiors import EMIRATES, SERVICES
from data_events_guides import EVENTS_HUB, EVENTS, GUIDES_HUB, TOP_GUIDES, PROVIDER_GUIDES

INTERIORS_HUB_SLUG = "interiors-uae"
pages = []


def page(key, slug, title, content, excerpt, seo_title, seo_desc, kw, type_, parent, img=None, place="", emirate="", order=0):
    pages.append(dict(key=key, slug=slug, title=title, content=content, excerpt=excerpt, parent=parent, img=img, order=order,
                      meta={"_cfm_type": type_, "_cfm_place": place, "_cfm_emirate": emirate,
                            "_cfm_seo_title": seo_title, "_cfm_seo_desc": seo_desc, "_cfm_seo_keyword": kw}))


# ---------- 1. Dubai area pages (floor mats) ----------
for i, a in enumerate(DUBAI_AREAS):
    c = intro("Floor mats · Dubai", f"Custom floor mats for {a['place']}", a["intro"], f"Free quote in {a['place']}")
    c += split("Coverage", f"Where we work in {a['place']}", a["coverage"], "Projects", f"Typical projects in {a['place']}", a["projects"])
    c += children("Products", f"Mats we supply in {a['place']}", "floor-mats", limit=6,
                  after=outline_button("See all 12 mat types", SITE + "/floor-mats/"))
    c += prose("Local know-how", f"Planning your project in {a['place']}", a["local"],
               ["Free logo mockup and samples (photos and sizes on WhatsApp)", "Mats made to your exact size, shape and colours",
                "Delivery and installation planned around your building's access rules", "Formal quotes and tax invoices for businesses"])
    c += sc('[cfm_gallery source="all" limit="8" title="Recent mats"]')
    c += faq(f"Floor mats in {a['place']} — FAQs", a["faqs"] + [
        (f"How much do custom floor mats cost in {a['place']}?",
         f"Price depends on mat type, size, construction and any logo or installation. Send your measurements, logo and a photo of the space on WhatsApp and we will reply with options and a clear quote for {a['place']}.")])
    c += areas_pills()
    c += cta(f"Custom mats in {a['place']} — get your quote")
    page(a["slug"], a["slug"], a["title"], c, a["excerpt"], a["seo_title"], a["seo_desc"], a["kw"], "location", "areas-we-serve",
         place=a["place"], emirate="Dubai", order=20 + i)

# ---------- 2. Interiors across the UAE: hub + emirate × service ----------
hub_c = intro("Casa Vera Home · UAE", "Curtains, blinds and carpets in every emirate",
              ["Our Casa Vera Home interiors team measures, makes and installs curtains, blinds and carpets far beyond Dubai — in Abu Dhabi, Al Ain, Sharjah, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain.",
               "Choose your emirate below to see the areas we cover, the products we install there and local tips for your home or business."],
              "Free measuring visit", second=("All interiors", SITE + "/interiors/"))
for e in EMIRATES:
    links = [(f"{SERVICES[s]['label']} in {e['name']}", f"/{INTERIORS_HUB_SLUG}/{s}-{e['key']}/") for s in ("curtains", "blinds", "carpets")]
    hub_c += links_section(e["name"], f"Curtains, blinds & carpets in {e['name']}", links)
hub_c += features("Why Casa Vera Home", "One team for every window and floor", [
    ("Free measuring", "We visit, measure every window and floor and bring samples."),
    ("Made to measure", "Curtains stitched and blinds made to your exact sizes."),
    ("Installed properly", "Tracks, motors, underlay and finishing done by our own fitters."),
    ("All seven emirates", "Planned visits across the UAE with confirmed dates.")])
hub_c += faq("Interiors across the UAE — FAQs", [
    ("Do you really install outside Dubai?", "Yes — we schedule measuring and installation trips to every emirate and confirm the dates in your quote."),
    ("Is there a charge for measuring outside Dubai?", "Tell us your location on WhatsApp; for most projects the measuring visit is free and we confirm before we travel."),
    ("Can you do curtains, blinds and carpets in one visit?", "Yes — one team can measure all three, so your home is finished with matching colours and one installation schedule.")])
hub_c += cta("Curtains, blinds or carpets anywhere in the UAE")
page(INTERIORS_HUB_SLUG, INTERIORS_HUB_SLUG, "Curtains, Blinds & Carpets Across the UAE", hub_c,
     "Made-to-measure curtains, blinds and carpets measured and installed in Abu Dhabi, Al Ain, Sharjah, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain.",
     "Curtains, Blinds & Carpets UAE | Abu Dhabi, Sharjah & More",
     "Curtains, blinds and carpets installed across the UAE by Casa Vera Home: Abu Dhabi, Al Ain, Sharjah, Ajman, Ras Al Khaimah, Fujairah and Umm Al Quwain.",
     "curtains blinds carpets uae", "hub", 0, img=318, order=50)

order = 0
for e in EMIRATES:
    for skey in ("curtains", "blinds", "carpets"):
        s = SERVICES[skey]
        n = e["name"]
        fmt = lambda t: t.format(name=n)
        title = f"{s['label']} in {n}"
        c = intro(f"{s['label']} · {n}", f"{s['label']} in {n}, measured and installed",
                  [s["lead"], f"In {n} we work in {e['homes']}, as well as {e['biz']}. {s['offer']}"],
                  f"Free {s['label'].lower()} quote in {n}", second=(f"All {s['label'].lower()}", SITE + f"/{s['hub']}/"))
        c += features("What we install", f"Popular {s['label'].lower()} in {n}", s["products"])
        c += split("Coverage", f"Areas we cover in {n}", e["areas"], "Options", "Fabrics, finishes & extras", s["options"])
        c += prose("Local know-how", f"Planning {s['label'].lower()} in {n}", [e["note"], e["logistics"]],
                   ["Free measuring visit with samples", "Written quote per room or area", "Installation by our own team", "After-sales support and alterations"])
        c += links_section("Explore", f"{s['label']} services", s["links"])
        c += faq(f"{title} — FAQs", [(fmt(q), fmt(a_)) for q, a_ in s["faqs"]])
        c += areas_pills("Curtains, blinds & carpets in other emirates", INTERIORS_HUB_SLUG, "Across the UAE")
        c += sc("[cfm_parent_company]")
        c += cta(f"{title} — book a free measuring visit")
        page(f"{skey}-{e['key']}", f"{skey}-{e['key']}", title, c, fmt(s["excerpt"]), fmt(s["seo_title"]), fmt(s["seo_desc"]), fmt(s["kw"]),
             "interior", INTERIORS_HUB_SLUG, img=s["img"], place=n, emirate=e["emirate"], order=order)
        order += 1

# ---------- 3. Events & Exhibitions ----------
H = EVENTS_HUB
c = intro("Events & Exhibitions", "Flooring that is ready when the doors open",
          ["Exhibitions, launches, weddings and gala dinners all run to fixed dates. We supply and install exhibition carpet, stand flooring, red carpets, branded mats and floor graphics — on time and in your colours.",
           "We work with organisers, stand builders, planners and venues across Dubai World Trade Centre, Expo City Dubai, ADNEC, hotel ballrooms and outdoor venues."],
          "Event flooring quote", second=("Exhibition carpet", SITE + "/carpets/exhibition-carpet/"))
c += children("What we supply", "Event & exhibition flooring", "events-exhibitions")
c += features("Why us", "Built around your event schedule", [
    ("Deadline-driven", "Installation inside build-up windows, removal at breakdown."),
    ("Brand colours", "Carpets, mats and graphics matched to your theme."),
    ("One supplier", "Carpet, stand floors, branded mats and graphics together."),
    ("UAE-wide", "Dubai, Abu Dhabi and venues across the emirates.")])
c += split("Venues", "Where we install", ["Dubai World Trade Centre", "Expo City Dubai", "ADNEC, Abu Dhabi", "Hotel ballrooms and conference centres", "Outdoor venues, tents and beaches", "Malls and retail activations"],
           "Events", "Events we supply", ["Trade shows and exhibitions", "Product launches and openings", "Conferences and summits", "Weddings and gala dinners", "Sports and community events", "Brand activations and pop-ups"])
c += faq("Event & exhibition flooring — FAQs", [
    ("How quickly can you supply event carpet?", "Stocked colours can often be arranged at short notice. Send your event date, venue and size on WhatsApp and we will confirm."),
    ("Do you remove flooring after the event?", "Yes — removal at breakdown can be included in the quote."),
    ("Can you match our brand colours?", "Yes — we offer a wide colour range and will suggest the closest match, with printed graphics for exact logos.")])
c += links_section("Guides", "Planning an event?", [("Best event & exhibition flooring in Dubai", "/buying-guides/best-event-exhibition-flooring-dubai/"),
                                                    ("How to choose an exhibition flooring contractor", "/buying-guides/exhibition-flooring-contractor-dubai/")])
c += cta("Event or exhibition coming up? Get your flooring quote")
page(H["slug"], H["slug"], H["title"], c, H["excerpt"], H["seo_title"], H["seo_desc"], H["kw"], "hub", 0, img=H["img"], order=45)

for i, ev in enumerate(EVENTS):
    c = intro("Events & Exhibitions", f"{ev['title']} in Dubai & the UAE", ev["intro"], f"Get a price for {ev['title'].lower()}",
              second=("All event flooring", SITE + "/events-exhibitions/"))
    c += features("Benefits", f"Why choose our {ev['title'].lower()}", ev["features"])
    c += split("Options", "Options & finishes", ev["options"], "Where it’s used", "Perfect for", ev["uses"])
    c += features("How it works", "From brief to event day", [
        ("Send your brief", "Event date, venue, size and your logo or colours on WhatsApp."),
        ("Mockup & quote", "We send options, a mockup where relevant and a written quote."),
        ("Production", "Made or prepared to your exact sizes and colours."),
        ("Install & remove", "Laid before doors open and removed after if required.")], extra_cls="cfm-steps")
    c += faq(f"{ev['title']} — common questions", ev["faqs"])
    c += areas_pills("More event & exhibition flooring", "events-exhibitions", "Keep exploring")
    c += sc("[cfm_parent_company]")
    c += cta(f"Get a quote for {ev['title'].lower()}")
    page(ev["slug"], ev["slug"], ev["title"], c, ev["excerpt"], ev["seo_title"], ev["seo_desc"], ev["kw"], "interior", "events-exhibitions",
         img=ev["img"], order=i)

# ---------- 4. Buying guides ----------
G = GUIDES_HUB
c = intro("Buying guides", "Choose the right mat, curtain, blind or carpet",
          ["Not sure what to buy? These guides compare the top choices for UAE homes, offices, hotels and events — what each option is best for and what to watch out for.",
           "We also explain how to choose a reliable supplier or installer, with the questions to ask before you pay."],
          "Ask us for advice")
c += children("Top choices", "Guides & comparisons", "buying-guides")
c += cta("Still deciding? Ask us on WhatsApp")
page(G["slug"], G["slug"], G["title"], c, G["excerpt"], G["seo_title"], G["seo_desc"], G["kw"], "hub", 0, img=G["img"], order=60)

for i, g in enumerate(TOP_GUIDES):
    c = intro("Buying guide · 2026", "The short answer", [g["lead"], "Every pick below links to the full product page with sizes, options and photos."], "Get advice & a quote")
    picks = p("Top choices", "cfm-eyebrow") + h("Our top choices at a glance")
    for n_, (name, best, why, link) in enumerate(g["picks"], 1):
        picks += h(f"{n_}. {name}", 3) + p(f"<strong>Best for:</strong> {best}. {why} <a href=\"{SITE}{link}\">See {name.lower()} →</a>")
    c += group("cfm-section cfm-local", picks)
    th, rows = g["table"]
    c += group("cfm-section cfm-local", p("Quick comparison", "cfm-eyebrow") + h("Which one should you choose?") + table(th, rows))
    c += prose("Buying tips", "How to choose well", ["A few simple rules make a big difference to how long your purchase lasts and how good it looks."], g["tips"])
    c += faq(f"{g['title'].split(':')[0]} — FAQs", g["faqs"])
    c += areas_pills("More buying guides", "buying-guides", "Keep reading")
    c += cta("Want a recommendation for your space?")
    page(g["slug"], g["slug"], g["title"], c, g["excerpt"], g["seo_title"], g["seo_desc"], g["kw"], "guide", "buying-guides", img=g["img"], order=i)

for i, g in enumerate(PROVIDER_GUIDES):
    c = intro("Supplier guide", "Choosing with confidence", [g["lead"], "Use this checklist to compare suppliers — and see how we measure up."], "Compare our quote")
    c += split("What the best offer", "What top providers offer", g["offers"], "Before you order", "Questions to ask", g["questions"])
    c += split("Watch out", "Red flags to avoid", g["red_flags"], "Our promise", "Why clients choose us", g["why"])
    c += faq(f"{g['title'].split(':')[0]} — FAQs", g["faqs"])
    c += areas_pills("More buying guides", "buying-guides", "Keep reading")
    c += sc("[cfm_parent_company]")
    c += cta("Get a quote to compare")
    page(g["slug"], g["slug"], g["title"], c, g["excerpt"], g["seo_title"], g["seo_desc"], g["kw"], "guide", "buying-guides", img=g["img"], order=10 + i)

out = os.path.join(os.path.dirname(os.path.abspath(__file__)), "pages.json")
with open(out, "w") as f:
    json.dump(pages, f, ensure_ascii=False)
print(len(pages), "pages;", "max content", max(len(x["content"]) for x in pages), "chars")
for x in pages:
    if len(x["meta"]["_cfm_seo_title"]) > 65 or len(x["meta"]["_cfm_seo_desc"]) > 165:
        print("LONG:", x["slug"], len(x["meta"]["_cfm_seo_title"]), len(x["meta"]["_cfm_seo_desc"]))
