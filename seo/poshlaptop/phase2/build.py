"""Builds the Posh Laptop phase 2 long-tail pages (Gutenberg HTML + Rank Math meta)."""
import json, html

SITE = "https://poshlaptop.com"
WA = "https://wa.me/971522920920"
P = SITE + "/product/"
T = SITE + "/product-tag/"

def p(t): return f"<!-- wp:paragraph -->\n<p>{t}</p>\n<!-- /wp:paragraph -->\n"
def h2(t): return f'<!-- wp:heading -->\n<h2 class="wp-block-heading">{t}</h2>\n<!-- /wp:heading -->\n'
def h3(t): return f'<!-- wp:heading {{"level":3}} -->\n<h3 class="wp-block-heading">{t}</h3>\n<!-- /wp:heading -->\n'
def ul(items): return "<!-- wp:list -->\n<ul class=\"wp-block-list\">" + "".join(f"<!-- wp:list-item --><li>{i}</li><!-- /wp:list-item -->" for i in items) + "</ul>\n<!-- /wp:list -->\n"
def a(href, text): return f'<a href="{href}">{text}</a>'
def table(head, rows):
    th = "".join(f"<th>{c}</th>" for c in head)
    tr = "".join("<tr>" + "".join(f"<td>{c}</td>" for c in r) + "</tr>" for r in rows)
    return f'<!-- wp:table -->\n<figure class="wp-block-table"><table><thead><tr>{th}</tr></thead><tbody>{tr}</tbody></table></figure>\n<!-- /wp:table -->\n'
def cta(text="Ask for today's stock and price on WhatsApp"):
    return f'<!-- wp:buttons -->\n<div class="wp-block-buttons"><!-- wp:button -->\n<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{WA}">{text}</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n'
def faq(qas):
    out = h2("Frequently asked questions")
    for q, ans in qas:
        out += h3(q) + p(ans)
    ld = {"@context": "https://schema.org", "@type": "FAQPage", "mainEntity": [
        {"@type": "Question", "name": q, "acceptedAnswer": {"@type": "Answer", "text": html.unescape(ans.replace("<a href", "<a  href"))}} for q, ans in qas]}
    # strip tags from answers for the JSON-LD
    import re
    for e in ld["mainEntity"]:
        e["acceptedAnswer"]["text"] = re.sub(r"<[^>]+>", "", e["acceptedAnswer"]["text"])
    out += "<!-- wp:html -->\n<script type=\"application/ld+json\">" + json.dumps(ld, ensure_ascii=False) + "</script>\n<!-- /wp:html -->\n"
    return out

TRUST = ul([
    "Every laptop is tested by our technicians before it is listed",
    "6-month warranty from the date of delivery",
    "15-day return window on online orders",
    "Delivery across Dubai, Abu Dhabi, Sharjah, Ajman and the rest of the UAE",
    "Repair, upgrade and AMC support from the same team after you buy",
])

pages = []

# 1 ─ Chromebook price in UAE
pages.append(dict(
  slug="chromebook-price-in-uae",
  title="Chromebook Price in UAE: Renewed Chromebooks from AED 199",
  focus="chromebook price in uae",
  seo_title="Chromebook Price in UAE (2026): Renewed from AED 199 | Posh Laptop",
  desc="Compare renewed Chromebook prices in the UAE: HP, Dell, Lenovo and Acer from AED 199, tested with a 6-month warranty. Delivery across Dubai and the UAE.",
  body=
    p("Looking at <strong>Chromebook prices in the UAE</strong> for a student or a second home laptop? A renewed Chromebook is the cheapest way to get a fast, low-maintenance laptop with a full keyboard. At Posh Laptop, renewed Chromebooks start at <strong>AED 199</strong>, and every unit is tested and covered by a 6-month warranty.")
    + h2("Renewed Chromebook price list (UAE)")
    + p("Prices below are in AED and come from our current stock. Stock moves quickly, so check the product page or message us to confirm.")
    + table(["Model", "Key specs", "Price (AED)"], [
        [a(P+"renewed-lenovo-chromebook-n22-11-6-hd-display-intel-celeron-n3050-4gb-ram-16gb-emmc-chrome-os-student-laptop/", "Lenovo Chromebook N22"), "11.6\" HD, Celeron N3050, 4GB, 16GB eMMC", "199"],
        [a(P+"renewed-hp-chromebook-11-g6-ee-11-6-hd-display-intel-celeron-4gb-ram-32gb-emmc-chrome-os-student-laptop/", "HP Chromebook 11 G6 EE"), "11.6\" HD, Celeron, 4GB, 32GB eMMC", "249"],
        [a(P+"refurbished-hp-chromebook-11-g5-11-6-hd-display-intel-celeron-4gb-ram-32gb-emmc-chrome-os-student-business-laptop/", "HP Chromebook 11 G5"), "11.6\" HD, Celeron, 4GB, 32GB eMMC", "249"],
        [a(P+"renewed-acer-chromebook-c740-zhn-11-6-hd-display-intel-celeron-4gb-ram-16gb-emmc-chrome-os-student-laptop/", "Acer Chromebook C740"), "11.6\" HD, Celeron, 4GB, 16GB eMMC", "249"],
        [a(P+"renewed-hp-chromebook-11-g7-ee-11-6-hd-display-intel-celeron-n4000-4gb-ram-32gb-emmc-chrome-os-student-laptop/", "HP Chromebook 11 G7 EE"), "11.6\" HD, Celeron N4000, 4GB, 32GB eMMC", "279"],
        [a(P+"renewed-hp-chromebook-11-g8-11-6-hd-display-intel-celeron-n4020-4gb-ram-32gb-emmc-chrome-os-student-laptop/", "HP Chromebook 11 G8"), "11.6\" HD, Celeron N4020, 4GB, 32GB eMMC", "299"],
        [a(P+"renewed-lenovo-chromebook-n23-2017-11-6-hd-display-intel-celeron-4gb-ram-16gb-emmc-chrome-os-student-laptop/", "Lenovo Chromebook N23"), "11.6\" HD, Celeron, 4GB, 16GB eMMC", "299"],
        [a(P+"renewed-acer-chromebook-r11-2-in-1-laptop-11-6-touchscreen-intel-celeron-4gb-ram-16gb-ssd-chrome-os-convertible-laptop/", "Acer Chromebook R11 2-in-1"), "11.6\" touchscreen, Celeron, 4GB, 16GB", "299"],
        [a(P+"renewed-dell-chromebook-3120-11-6-hd-display-intel-celeron-2gb-ram-16gb-ssd-chrome-os-budget-laptop/", "Dell Chromebook 3120"), "11.6\" HD, Celeron, 2GB, 16GB", "299"],
        [a(P+"refurbished-dell-chromebook-11-3180-11-6-hd-display-intel-celeron-n3060-2gb-ram-16gb-emmc-chrome-os-budget-student-laptop/", "Dell Chromebook 11 3180"), "11.6\" HD, Celeron N3060, 2GB, 16GB eMMC", "310"],
        [a(P+"renewed-dell-chromebook-3100-11-6-hd-display-intel-celeron-n4020-4gb-ram-32gb-emmc-chrome-os-student-laptop/", "Dell Chromebook 3100"), "11.6\" HD, Celeron N4020, 4GB, 32GB eMMC", "320"],
        [a(P+"renewed-dell-chromebook-11-3189-2-in-1-11-6-ips-touch-intel-celeron-n3060-4gb-ram-32gb-emmc-chrome-os-convertible-laptop/", "Dell Chromebook 11 3189 2-in-1"), "11.6\" IPS touch, Celeron N3060, 4GB, 32GB eMMC", "320"],
      ])
    + cta()
    + h2("Which Chromebook should you buy?")
    + h3("For school and homework")
    + p("Pick a model with <strong>4GB RAM and 32GB storage</strong>, such as the HP Chromebook 11 G7 EE, HP 11 G8 or Dell Chromebook 3100. They run Google Classroom, Docs, Meet and the browser comfortably, and the education (EE) versions are built to survive a school bag. Browse all " + a(T+"chromebook-for-school-uae/", "Chromebooks for school in the UAE") + ".")
    + h3("For the lowest budget")
    + p("The Lenovo Chromebook N22 at AED 199 and the 2GB Dell models are fine for browsing, video calls and online documents. If the laptop will also be used for many open tabs, step up to 4GB RAM.")
    + h3("For touch and drawing")
    + p("The Acer Chromebook R11 and Dell Chromebook 11 3189 fold into tablet mode, which younger students find easier for reading and drawing apps.")
    + h2("Chromebook or Windows laptop?")
    + p("A Chromebook boots in seconds, updates itself and is hard to slow down with viruses, which makes it ideal for children and light users. If you need Microsoft Office desktop apps, school software that only runs on Windows, or more storage, look at our " + a(T+"student-laptop-uae/", "student laptops") + " or the wider range of " + a(T+"affordable-laptop-uae/", "affordable laptops in the UAE") + ". Before buying any Chromebook, check its auto-update date on Google's support site so you know how long it will keep getting Chrome OS updates.")
    + h2("Why buy a renewed Chromebook from Posh Laptop")
    + TRUST
    + faq([
        ("What is the price of a Chromebook in the UAE?", "Renewed Chromebooks at Posh Laptop cost between AED 199 and AED 320, depending on RAM, storage and whether the model has a touchscreen. Brand-new Chromebooks in UAE retail stores usually cost considerably more."),
        ("Do renewed Chromebooks come with a warranty?", "Yes. Every renewed Chromebook we sell is tested before listing and comes with a 6-month warranty from the date of delivery, plus a 15-day return window on online orders."),
        ("Can a Chromebook run Microsoft Office?", "Chromebooks run the web versions of Word, Excel and PowerPoint, and Google Docs, Sheets and Slides. They cannot install the Windows desktop versions of Office."),
        ("Do you deliver Chromebooks outside Dubai?", "Yes. We deliver across the UAE, including Abu Dhabi, Sharjah and Ajman. Message us on WhatsApp on +971 52 292 0920 for delivery times to your area."),
      ]),
))

# 2 ─ Second hand laptop Dubai
pages.append(dict(
  slug="second-hand-laptop-dubai",
  title="Second Hand Laptop in Dubai: Buy Tested, Renewed Laptops with Warranty",
  focus="second hand laptop dubai",
  seo_title="Second Hand Laptop in Dubai: Tested, With Warranty | Posh Laptop",
  desc="Buying a second hand laptop in Dubai? Get a tested, renewed Dell, HP, Lenovo or MacBook with a 6-month warranty and 15-day returns, not a marketplace gamble.",
  body=
    p("A <strong>second hand laptop in Dubai</strong> can save you 40–70% compared with buying new, but buying from an online marketplace or a stranger is a gamble on the battery, the screen and the history of the machine. Posh Laptop sells <strong>renewed</strong> second hand laptops: each one is inspected, cleaned, tested and sold with a 6-month warranty from our UAE showroom.")
    + h2("Second hand, refurbished or renewed: what is the difference?")
    + table(["Type", "What it usually means", "Warranty"], [
        ["Second hand / used", "Sold as-is by the previous owner, often untested", "Usually none"],
        ["Refurbished", "Checked and repaired where needed by a seller or technician", "Varies by seller"],
        ["Renewed (Posh Laptop)", "Fully tested, cleaned, faulty parts replaced, OS reinstalled", "6 months + 15-day returns"],
      ])
    + h2("Popular second hand laptops by budget")
    + h3("Under AED 700")
    + p("Business laptops such as the " + a(P+"renewed-dell-vostro-3590-laptop-15-6-fhd-intel-core-i5-10th-gen-8gb-ram-256gb-ssd-business-laptop-windows-10-pro/", "Dell Vostro 3590 (Core i5 10th Gen, AED 699)") + " and the " + a(P+"renewed-dell-latitude-5290-2-in-1-laptop-12-3-touch-intel-core-i5-8th-gen-8gb-ram-256gb-ssd/", "Dell Latitude 5290 2-in-1 (AED 699)") + " handle office work, online classes and browsing. For the lowest budget, our " + a(SITE+"/chromebook-price-in-uae/", "renewed Chromebooks start at AED 199") + ".")
    + h3("AED 900 – 1,500")
    + p("This is the sweet spot for professionals: the " + a(P+"renewed-dell-xps-13-9300-laptop-13-4-fhd-intel-core-i7-10th-gen-8gb-ram-256gb-ssd-windows-11-ultra-slim/", "Dell XPS 13 9300 (Core i7, AED 999)") + ", " + a(P+"renewed-hp-zbook-14-g7-laptop-14-fhd-intel-core-i7-10th-gen-16gb-ram-256gb-ssd-windows-11-business-workstation/", "HP ZBook 14 G7 (AED 1,199)") + " and " + a(P+"renewed-dell-latitude-5550-laptop-15-6-fhd-intel-core-i7-16gb-ram-256gb-ssd-windows-11-pro/", "Dell Latitude 5550 (AED 1,299)") + ". See all " + a(T+"business-laptop-refurbished/", "refurbished business laptops") + ".")
    + h3("AED 1,500 and above")
    + p("Workstations and premium machines for design, engineering and video: Dell Precision, HP ZBook, Dell XPS 15 and MacBook Pro. Compare them on our " + a(SITE+"/refurbished-workstation-laptop-dubai/", "refurbished workstation laptops") + " and " + a(SITE+"/used-macbook-dubai/", "used MacBook in Dubai") + " pages.")
    + cta("Tell us your budget on WhatsApp")
    + h2("Checklist before you buy any second hand laptop")
    + ul([
        "<strong>Battery health:</strong> ask for the battery report or cycle count, not just \"it charges\"",
        "<strong>Screen:</strong> look for dead pixels, pressure marks and backlight bleed on a white and a black background",
        "<strong>Keyboard and ports:</strong> test every key, both USB ports, HDMI and the charger port",
        "<strong>Storage:</strong> insist on an SSD; a hard drive makes even a fast processor feel slow",
        "<strong>Processor generation:</strong> for Windows 11, Intel 8th Gen or newer is the safe minimum",
        "<strong>Legal Windows licence and a clean install</strong>, with no previous owner's accounts",
        "<strong>Warranty and returns in writing</strong>",
      ])
    + p("Every laptop we sell has already been through this checklist, and you can also bring it back to us later for " + a(SITE+"/services/", "repairs, upgrades or an AMC") + ".")
    + h2("Why Dubai buyers choose Posh Laptop")
    + TRUST
    + faq([
        ("Is it safe to buy a second hand laptop in Dubai?", "It is safe when you buy from a seller who tests the laptop and gives a written warranty. Posh Laptop sells renewed laptops with a 6-month warranty and a 15-day return window on online orders."),
        ("How much does a second hand laptop cost in Dubai?", "At Posh Laptop, renewed Chromebooks start at AED 199, Core i5 business laptops from about AED 699, and Core i7 business laptops from about AED 999. Workstations and MacBooks cost more."),
        ("Can I upgrade the RAM or SSD on a used laptop?", "Many business laptops such as Dell Latitude and HP EliteBook models can be upgraded. Our technicians can upgrade RAM and SSD at the time of purchase or later."),
        ("Do you buy or exchange old laptops?", "Message us on WhatsApp on +971 52 292 0920 with your laptop model and condition and we will let you know what options are available."),
      ]),
))

# 3 ─ Workstation
pages.append(dict(
  slug="refurbished-workstation-laptop-dubai",
  title="Refurbished Workstation Laptops in Dubai: Dell Precision and HP ZBook",
  focus="refurbished workstation laptop dubai",
  seo_title="Refurbished Workstation Laptop Dubai: Dell Precision & HP ZBook",
  desc="Refurbished Dell Precision and HP ZBook workstation laptops in Dubai from AED 1,099, with NVIDIA Quadro and RTX graphics. Tested, 6-month warranty.",
  body=
    p("Mobile workstations are built for AutoCAD, Revit, SolidWorks, Adobe and video editing, and new ones often cost well over AED 8,000. A <strong>refurbished workstation laptop in Dubai</strong> gives you the same certified hardware for a fraction of that. Posh Laptop stocks renewed <strong>Dell Precision</strong> and <strong>HP ZBook</strong> workstations, tested under load and covered by a 6-month warranty.")
    + h2("Workstation laptop price list (AED)")
    + table(["Model", "CPU / GPU", "RAM / SSD", "Price"], [
        [a(P+"renewed-dell-precision-3550-laptop-15-6-fhd-intel-core-i7-10th-gen-8gb-ram-256gb-ssd-windows-10-pro/", "Dell Precision 3550"), "Core i7 10th Gen", "8GB / 256GB", "1,099"],
        [a(P+"renewed-hp-zbook-14-g7-laptop-14-fhd-intel-core-i7-10th-gen-16gb-ram-256gb-ssd-windows-11-business-workstation/", "HP ZBook 14 G7"), "Core i7 10th Gen", "16GB / 256GB", "1,199"],
        [a(P+"renewed-dell-precision-7540-workstation-intel-core-i7-9th-gen-8gb-ram-256gb-ssd-15-6-fhd-windows-10-pro/", "Dell Precision 7540"), "Core i7 9th Gen", "8GB / 256GB", "1,299"],
        [a(P+"renewed-dell-precision-7530-workstation-15-6-fhd-intel-core-i7-8750h-8gb-ram-256gb-ssd-nvidia-quadro-p2000-windows-10-pro/", "Dell Precision 7530"), "i7-8750H / Quadro P2000", "8GB / 256GB", "1,599"],
        [a(P+"renewed-dell-precision-7550-workstation-laptop-15-6-fhd-intel-core-i7-10850h-8gb-ram-256gb-nvme-ssd-nvidia-quadro-t1000-4gb-windows-11/", "Dell Precision 7550"), "i7-10850H / Quadro T1000 4GB", "8GB / 256GB", "1,599"],
        [a(P+"renewed-dell-precision-3560-mobile-workstation-15-6-fhd-intel-core-i7-1165g7-11th-gen-16gb-ram-256gb-ssd-windows-10-pro/", "Dell Precision 3560"), "i7-1165G7 11th Gen", "16GB / 256GB", "1,599"],
        [a(P+"renewed-hp-zbook-firefly-14-g8-laptop-14-fhd-intel-core-i7-1165g7-16gb-ram-256gb-ssd-iris-xe-windows-10-pro-business-laptop/", "HP ZBook Firefly 14 G8"), "i7-1165G7 / Iris Xe", "16GB / 256GB", "1,599"],
        [a(P+"renewed-dell-precision-7560-workstation-laptop-15-6-fhd-intel-core-i7-16gb-ram-512gb-ssd-windows-11-pro/", "Dell Precision 7560"), "Core i7 11th Gen", "16GB / 512GB", "1,899"],
        [a(P+"renewed-hp-zbook-fury-15-g8-workstation-15-6-fhd-intel-core-i7-11th-gen-h-16gb-ram-256gb-ssd-nvidia-quadro-t1200-4gb-windows-11-pro/", "HP ZBook Fury 15 G8"), "i7 11th Gen H / Quadro T1200 4GB", "16GB / 256GB", "2,099"],
        [a(P+"renewed-hp-zbook-studio-16-g9-mobile-workstation-intel-core-i9-12900h-32gb-ddr5-ram-1tb-ssd-12gb-nvidia-gpu-16-display-windows-11-pro/", "HP ZBook Studio 16 G9"), "i9-12900H / 12GB NVIDIA", "32GB DDR5 / 1TB", "4,500"],
      ])
    + cta()
    + h2("Which workstation fits your work?")
    + h3("2D CAD, Office and BIM viewing")
    + p("The Dell Precision 3550, 3560 and HP ZBook 14 G7 / Firefly G8 are thin, light and fast enough for AutoCAD 2D, Revit viewing, Excel models and site visits.")
    + h3("3D modelling, Revit and SolidWorks")
    + p("Choose a model with a dedicated <strong>NVIDIA Quadro</strong> card: Dell Precision 7530 (P2000), 7550 (T1000) or HP ZBook Fury 15 G8 (T1200). Certified drivers mean fewer crashes in professional software. We recommend 16GB RAM or more; we can upgrade RAM and SSD before delivery.")
    + h3("Rendering, video and heavy simulation")
    + p("The HP ZBook Studio 16 G9 with Core i9, 32GB DDR5 and a 12GB NVIDIA GPU handles 4K editing, rendering and large assemblies. The " + a(P+"renewed-dell-xps-15-9510-laptop-15-6-oled-3-5k-intel-core-i7-16gb-ram-512gb-ssd-nvidia-rtx-3050-ti-4gb-windows-10/", "Dell XPS 15 9510 with RTX 3050 Ti") + " is a lighter creative alternative.")
    + h2("What we test on every workstation")
    + ul(["CPU and GPU stress test for thermals and throttling", "Fans cleaned and thermal paste refreshed where needed", "Battery health check", "Display uniformity and colour check", "All ports, keyboard, trackpad and webcam", "Clean Windows install with current drivers"])
    + p("Browse more " + a(T+"workstation-laptop-uae/", "workstation laptops in the UAE") + " or the full " + a(T+"refurbished-dell-workstation/", "refurbished Dell workstation") + " range.")
    + h2("Buy with confidence")
    + TRUST
    + faq([
        ("What is the best refurbished workstation laptop for AutoCAD?", "For 2D AutoCAD, a Dell Precision 3550 or 3560 with 16GB RAM is enough. For 3D work, choose a model with an NVIDIA Quadro card such as the Dell Precision 7550 or HP ZBook Fury 15 G8."),
        ("How much does a refurbished workstation laptop cost in Dubai?", "At Posh Laptop, renewed workstation laptops start from AED 1,099 for a Dell Precision 3550 and go up to AED 4,500 for an HP ZBook Studio 16 G9 with Core i9 and 32GB DDR5."),
        ("Can you upgrade RAM and SSD before delivery?", "Yes. Most Dell Precision and HP ZBook models can be upgraded. Tell us your software and we will recommend and fit the right RAM and SSD."),
        ("Do refurbished workstations come with a warranty?", "Yes, every workstation comes with a 6-month warranty from delivery and a 15-day return window on online orders."),
      ]),
))

# 4 ─ Used MacBook
pages.append(dict(
  slug="used-macbook-dubai",
  title="Used MacBook in Dubai: Renewed MacBook Air and MacBook Pro Prices",
  focus="used macbook dubai",
  seo_title="Used MacBook Dubai: Renewed MacBook Air M2 & Pro Prices | Posh Laptop",
  desc="Renewed used MacBooks in Dubai: MacBook Air M2, MacBook Pro M1 and M1 Pro from AED 1,099. Battery-checked, tested, 6-month warranty, UAE delivery.",
  body=
    p("A <strong>used MacBook in Dubai</strong> holds its value well, and that is exactly why it pays to buy carefully. Posh Laptop sells renewed MacBook Air and MacBook Pro models that are battery-checked, iCloud-free, reset to a clean macOS install and covered by a 6-month warranty.")
    + h2("Renewed MacBook price list (AED)")
    + table(["Model", "Chip", "RAM / SSD", "Price"], [
        [a(P+"renewed-apple-macbook-pro-13-2016-13-3-retina-display-intel-core-i5-8gb-ram-256gb-ssd-macos/", "MacBook Pro 13\" (2016)"), "Intel Core i5", "8GB / 256GB", "1,099"],
        [a(P+"renewed-apple-macbook-pro-13-2017-13-3-retina-display-intel-core-i5-dual-core-8gb-ram-256gb-ssd-macos/", "MacBook Pro 13\" (2017)"), "Intel Core i5", "8GB / 256GB", "1,299"],
        [a(P+"renewed-apple-macbook-pro-13-2020-m1-13-3-retina-display-apple-m1-chip-8gb-ram-256gb-ssd-macos/", "MacBook Pro 13\" (2020, M1)"), "Apple M1", "8GB / 256GB", "2,999"],
        [a(P+"renewed-apple-macbook-air-m2-mc7x4-13-6-liquid-retina-display-8-core-cpu-8-core-gpu-16gb-ram-256gb-ssd-macos/", "MacBook Air M2 (MC7X4)"), "Apple M2, 8-core GPU", "16GB / 256GB", "3,599"],
        [a(P+"renewed-apple-macbook-pro-14-2021-m1-pro-14-liquid-retina-xdr-m1-pro-octa-core-16gb-ram-256gb-ssd-macos/", "MacBook Pro 14\" (2021, M1 Pro)"), "Apple M1 Pro", "16GB / 256GB", "3,699"],
        [a(P+"renewed-apple-macbook-air-m2-mc7w4-13-6-liquid-retina-display-8-core-cpu-8-core-gpu-16gb-ram-256gb-ssd-macos/", "MacBook Air M2 (MC7W4)"), "Apple M2, 8-core GPU", "16GB / 256GB", "3,999"],
      ])
    + cta()
    + h2("Which used MacBook should you choose?")
    + h3("Apple silicon (M1, M2) vs Intel MacBooks")
    + p("M1 and M2 MacBooks are much faster, run cooler and get far better battery life than the Intel models, and they will keep receiving macOS updates for longer. Intel MacBook Pros from 2016–2017 are a budget option for writing, browsing and study, but check that the macOS version you need is still supported.")
    + h3("MacBook Air M2 for students and everyday work")
    + p("Silent (no fan), light and all-day battery. Our M2 units come with <strong>16GB RAM</strong>, which keeps them quick with many browser tabs, Office and light photo editing for years.")
    + h3("MacBook Pro 14\" M1 Pro for creators")
    + p("The Liquid Retina XDR screen, extra ports (HDMI, SD card, MagSafe) and M1 Pro chip make it a serious tool for video editing, music production and development.")
    + h2("What we check on every used MacBook")
    + ul(["Battery cycle count and health", "Activation Lock and iCloud removed, MDM-free", "Clean macOS installation", "Display, keyboard, trackpad, speakers, camera and all ports", "Genuine charger compatibility"])
    + p("Need accessories? See our " + a(P+"wireless-touch-mouse-for-macbook-air-pro-multi-touch-gesture-mouse-bluetooth-rechargeable-slim-design/", "wireless touch mouse for MacBook") + " and the full " + a(T+"apple-laptop-macos/", "Apple laptop range") + ". Prefer Windows? Compare our " + a(SITE+"/second-hand-laptop-dubai/", "second hand laptops in Dubai") + ".")
    + h2("Buy with confidence")
    + TRUST
    + faq([
        ("How much is a used MacBook in Dubai?", "At Posh Laptop, renewed MacBooks start at AED 1,099 for a 2016 Intel MacBook Pro. Apple silicon models cost from AED 2,999 for a MacBook Pro M1, and from AED 3,599 for a MacBook Air M2 with 16GB RAM."),
        ("Is a used MacBook Air M2 worth it?", "Yes, for most users. The M2 is fast, silent and efficient, and a 16GB RAM model will stay responsive for several years of study and office work."),
        ("Are your MacBooks iCloud-locked?", "No. Every MacBook is checked for Activation Lock and MDM, and is reset to a clean macOS install before sale."),
        ("Do used MacBooks come with a warranty?", "Yes. Every renewed MacBook comes with a 6-month warranty from delivery and a 15-day return window on online orders."),
      ]),
))

# 5 ─ EliteBook vs ProBook
pages.append(dict(
  slug="hp-elitebook-vs-probook",
  title="HP EliteBook vs ProBook: Which Renewed HP Laptop Should You Buy?",
  focus="hp elitebook vs probook",
  seo_title="HP EliteBook vs ProBook: Which to Buy? (430 G8, 840, 830 G9)",
  desc="HP EliteBook vs ProBook compared: build, display, security, battery and price. Including the ProBook 430 G8 and EliteBook 830/840. Renewed in the UAE.",
  body=
    p("<strong>HP EliteBook vs ProBook</strong> is the question we hear most from buyers in the UAE. Both are HP business laptops and both are reliable, but they are built for different budgets. This guide compares them so you can pick the right renewed HP laptop.")
    + h2("Quick comparison")
    + table(["", "HP ProBook", "HP EliteBook"], [
        ["Positioning", "Mid-range business", "Premium business"],
        ["Build", "Aluminium lid, plastic or aluminium base", "Mostly aluminium/magnesium, lighter and thinner"],
        ["Display", "FHD on most configs", "FHD, often brighter; touch and privacy screens available"],
        ["Security", "TPM, fingerprint on many models", "HP Sure Start, Sure View privacy screen, IR camera on many models"],
        ["Keyboard", "Good, spill-resistant", "Backlit, quieter, more premium feel"],
        ["Best for", "Students, SMEs, office staff", "Executives, travellers, client-facing roles"],
        ["Renewed price", "Lower", "Higher for the same generation"],
      ])
    + h2("Popular models we stock")
    + h3("HP ProBook 430 G8, 440 G10, 450 G8 / G9")
    + p("The " + a(T+"hp-probook-430-g8/", "HP ProBook 430 G8") + " is a compact 13.3\" laptop with Intel 11th Gen processors, ideal for students and staff who carry their laptop daily. The " + a(T+"hp-probook-440-g10/", "ProBook 440 G10") + " moves to 13th Gen Intel on a 14\" screen, and the " + a(T+"hp-probook-450-g9/", "ProBook 450 G9") + " and " + a(T+"hp-probook-450-g8/", "450 G8") + " add a 15.6\" display and number pad for spreadsheet work.")
    + h3("HP EliteBook 830 and 840 series")
    + p("The " + a(T+"hp-elitebook-830-g9/", "EliteBook 830 G9") + " (12th Gen Intel) is a premium 13.3\" ultrabook. The 840 series, from the " + a(T+"hp-elitebook-840-g5/", "840 G5") + " to the " + a(T+"hp-elitebook-840-g8/", "840 G8") + " and " + a(T+"hp-elitebook-840-g9/", "840 G9") + ", is the 14\" all-rounder found in many corporate offices. For a convertible, look at the " + a(T+"hp-elitebook-x360-830-g6/", "EliteBook x360 830 G6") + ".")
    + cta("Ask for today's HP stock on WhatsApp")
    + h2("Which should you buy?")
    + ul([
        "<strong>Choose a ProBook</strong> if price matters most and the laptop will mostly stay on a desk or in a bag between home and office.",
        "<strong>Choose an EliteBook</strong> if you travel, present to clients, or want a lighter, better-built laptop with a nicer screen and keyboard.",
        "<strong>Generation matters more than the badge:</strong> an EliteBook 840 G8 (11th Gen) will feel faster than an older ProBook 440 G5. Aim for Intel 8th Gen or newer for Windows 11.",
        "<strong>16GB RAM</strong> is worth it if you use many browser tabs, Teams and Excel together.",
      ])
    + p("See every " + a(T+"renewed-hp-laptop/", "renewed HP laptop") + " in stock, or compare HP with other brands in our guide to " + a(SITE+"/second-hand-laptop-dubai/", "second hand laptops in Dubai") + ".")
    + h2("Why buy your HP from Posh Laptop")
    + TRUST
    + faq([
        ("Is HP EliteBook better than ProBook?", "EliteBook is HP's premium business line with better build quality, displays and security features. ProBook is the more affordable line. For the same processor generation, an EliteBook is the better laptop but costs more."),
        ("Is the HP ProBook 430 G8 good?", "Yes. The ProBook 430 G8 is a compact 13.3-inch business laptop with Intel 11th Gen processors and Windows 11 support, a good choice for students and office staff on a budget."),
        ("What is the difference between EliteBook 830 and 840?", "The EliteBook 830 has a 13.3-inch screen and is smaller and lighter. The 840 has a 14-inch screen with similar internals. Choose the 830 for travel and the 840 for a slightly larger workspace."),
        ("Do renewed HP laptops support Windows 11?", "HP ProBook and EliteBook models with Intel 8th Gen processors or newer generally support Windows 11. Ask us to confirm for a specific model."),
      ]),
))

# 6 ─ 2-in-1 detachable
pages.append(dict(
  slug="2-in-1-detachable-laptop-uae",
  title="2-in-1 Detachable Laptops in the UAE: Renewed Dell Latitude and Surface",
  focus="2 in 1 detachable laptop uae",
  seo_title="2-in-1 Detachable Laptop UAE: Dell Latitude & Surface from AED 699",
  desc="Renewed 2-in-1 detachable and convertible laptops in the UAE: Dell Latitude 7320, 7210, 5285, HP Elite x2 and Surface Book from AED 699. 6-month warranty.",
  body=
    p("A <strong>2-in-1 detachable laptop</strong> is a tablet when you are reading or signing documents and a full Windows laptop when you clip on the keyboard. Posh Laptop sells renewed business-grade 2-in-1s from Dell, HP and Microsoft across the UAE, all tested and covered by a 6-month warranty.")
    + h2("Renewed 2-in-1 laptop price list (AED)")
    + table(["Model", "Screen", "CPU / RAM / SSD", "Price"], [
        [a(P+"renewed-dell-latitude-5290-2-in-1-laptop-12-3-touch-intel-core-i5-8th-gen-8gb-ram-256gb-ssd/", "Dell Latitude 5290 2-in-1"), "12.3\" touch", "i5 8th Gen / 8GB / 256GB", "699"],
        [a(P+"renewed-microsoft-surface-book-1-13-5-pixelsense-touch-display-intel-core-i5-6th-gen-8gb-ram-256gb-ssd-detachable-2-in-1-laptop/", "Microsoft Surface Book 1"), "13.5\" PixelSense", "i5 6th Gen / 8GB / 256GB", "749"],
        [a(P+"renewed-hp-elite-x2-1012-g1-2-in-1-laptop-12-3-touch-intel-core-m5-6th-gen-8gb-ram-256gb-ssd-windows-10-pro/", "HP Elite x2 1012 G1"), "12.3\" touch", "Core m5 / 8GB / 256GB", "799"],
        [a(P+"renewed-dell-latitude-7200-2-in-1-laptop-12-3-touch-intel-core-i7-8th-gen-16gb-ram-512gb-ssd-windows-10-pro/", "Dell Latitude 7200 2-in-1"), "12.3\" touch", "i7 8th Gen / 16GB / 512GB", "899"],
        [a(P+"renewed-dell-latitude-7210-2-in-1-laptop-12-3-touch-intel-core-i7-10610u-10th-gen-16gb-ram-512gb-ssd-windows-10-pro/", "Dell Latitude 7210 2-in-1"), "12.3\" touch", "i7-10610U / 16GB / 512GB", "1,049"],
        [a(P+"renewed-dell-latitude-5285-12-3-fhd-touchscreen-intel-core-i5-7th-gen-8gb-ram-256gb-ssd-detachable-2-in-1-business-laptop/", "Dell Latitude 5285 (i5)"), "12.3\" FHD touch", "i5 7th Gen / 8GB / 256GB", "1,159"],
        [a(P+"renewed-dell-latitude-7320-detachable-2-in-1-laptop-13-fhd-touch-intel-core-i5-1140g7-11th-gen-16gb-ram-256gb-ssd-windows-11/", "Dell Latitude 7320 Detachable (i5)"), "13\" FHD touch", "i5-1140G7 / 16GB / 256GB", "1,299"],
        [a(P+"renewed-dell-latitude-7320-detachable-2-in-1-laptop-13-fhd-touch-intel-core-i7-11th-gen-16gb-ram-256gb-ssd-windows-11-2/", "Dell Latitude 7320 Detachable (i7)"), "13\" FHD touch", "i7 11th Gen / 16GB / 256GB", "1,499"],
        [a(P+"renewed-microsoft-surface-book-2-15-pixelsense-touchscreen-intel-core-i7-8th-gen-16gb-ram-256gb-ssd-nvidia-geforce-gtx-1060-6gb-silver/", "Microsoft Surface Book 2 15\""), "15\" PixelSense", "i7 8th Gen / 16GB / GTX 1060", "2,199"],
        [a(P+"renewed-microsoft-surface-book-3-15-pixelsense-detachable-touchscreen-intel-core-i7-10th-gen-quad-core-16gb-ram-512gb-ssd-nvidia-rtx-a3000-6gb/", "Microsoft Surface Book 3 15\""), "15\" PixelSense", "i7 10th Gen / 16GB / 512GB", "2,899"],
      ])
    + cta()
    + h2("Detachable vs convertible: which type do you need?")
    + p("<strong>Detachables</strong> (Dell Latitude 7320, 7210, 5285, HP Elite x2, Surface Book) let you pull the screen off the keyboard and use it as a light tablet. <strong>Convertibles</strong> (such as the HP EliteBook x360 and Lenovo ThinkPad Yoga) keep the keyboard attached and fold back 360°. Detachables are lighter as tablets; convertibles feel sturdier on your lap. Browse all " + a(T+"2-in-1-convertible-laptop/", "2-in-1 convertible laptops") + " and " + a(T+"detachable-laptop-uae/", "detachable laptops in the UAE") + ".")
    + h2("Who are 2-in-1 laptops best for?")
    + ul([
        "<strong>Sales and field teams</strong> who show presentations and collect signatures on the screen",
        "<strong>Students</strong> who take handwritten notes with a stylus",
        "<strong>Executives</strong> who read documents on flights and type on the ground",
        "<strong>Designers</strong> who sketch: the Surface Book 2 and 3 add a dedicated NVIDIA GPU",
      ])
    + p("Need a budget touch option for children? Our " + a(SITE+"/chromebook-price-in-uae/", "2-in-1 Chromebooks start at AED 299") + ".")
    + h2("Buy with confidence")
    + TRUST
    + faq([
        ("What is the best 2-in-1 detachable laptop for business?", "The Dell Latitude 7320 Detachable with an Intel 11th Gen processor and 16GB RAM is a strong business choice. It runs Windows 11 and has a 13-inch FHD touchscreen."),
        ("How much does a renewed 2-in-1 laptop cost in the UAE?", "Renewed 2-in-1 laptops at Posh Laptop start at AED 699 for a Dell Latitude 5290 and go up to AED 2,899 for a Microsoft Surface Book 3 15-inch."),
        ("Do detachable laptops come with the keyboard?", "Yes, the listed price includes the keyboard unless the product page says otherwise. Message us to confirm the included accessories and stylus availability."),
        ("Is there a warranty on renewed 2-in-1 laptops?", "Yes. Every renewed 2-in-1 laptop comes with a 6-month warranty from delivery and a 15-day return window on online orders."),
      ]),
))

for pg in pages:
    with open(f"{pg['slug']}.html", "w") as f:
        f.write(pg["body"])
manifest = [{k: v for k, v in pg.items() if k != "body"} for pg in pages]
json.dump(manifest, open("pages.json", "w"), indent=2, ensure_ascii=False)
for pg in pages:
    import re
    words = len(re.sub(r"<[^>]+>", " ", pg["body"]).split())
    print(pg["slug"], words, "words", len(pg["seo_title"]), "title chars", len(pg["desc"]), "desc chars")
