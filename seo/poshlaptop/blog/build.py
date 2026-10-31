"""Builds Posh Laptop blog posts (Gutenberg HTML + Rank Math meta). Original content; competitor blogs used only for topic ideas."""
import json, re, sys
sys.path.insert(0, "../phase2")

SITE = "https://poshlaptop.com"
WA = "https://wa.me/971522920920"
P = SITE + "/product/"
T = SITE + "/product-tag/"
C = SITE + "/product-category/"

def p(t): return f"<!-- wp:paragraph -->\n<p>{t}</p>\n<!-- /wp:paragraph -->\n"
def h2(t): return f'<!-- wp:heading -->\n<h2 class="wp-block-heading">{t}</h2>\n<!-- /wp:heading -->\n'
def h3(t): return f'<!-- wp:heading {{"level":3}} -->\n<h3 class="wp-block-heading">{t}</h3>\n<!-- /wp:heading -->\n'
def ul(items): return "<!-- wp:list -->\n<ul class=\"wp-block-list\">" + "".join(f"<!-- wp:list-item --><li>{i}</li><!-- /wp:list-item -->" for i in items) + "</ul>\n<!-- /wp:list -->\n"
def ol(items): return "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">" + "".join(f"<!-- wp:list-item --><li>{i}</li><!-- /wp:list-item -->" for i in items) + "</ol>\n<!-- /wp:list -->\n"
def a(href, text): return f'<a href="{href}">{text}</a>'
def table(head, rows):
    th = "".join(f"<th>{c}</th>" for c in head)
    tr = "".join("<tr>" + "".join(f"<td>{c}</td>" for c in r) + "</tr>" for r in rows)
    return f'<!-- wp:table -->\n<figure class="wp-block-table"><table><thead><tr>{th}</tr></thead><tbody>{tr}</tbody></table></figure>\n<!-- /wp:table -->\n'
def cta(text="Ask us on WhatsApp"):
    return f'<!-- wp:buttons -->\n<div class="wp-block-buttons"><!-- wp:button -->\n<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{WA}">{text}</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n'
def faq(qas):
    out = h2("Frequently asked questions")
    for q, ans in qas:
        out += h3(q) + p(ans)
    ld = {"@context": "https://schema.org", "@type": "FAQPage", "mainEntity": [
        {"@type": "Question", "name": q, "acceptedAnswer": {"@type": "Answer", "text": re.sub(r"<[^>]+>", "", ans)}} for q, ans in qas]}
    return out + "<!-- wp:html -->\n<script type=\"application/ld+json\">" + json.dumps(ld, ensure_ascii=False) + "</script>\n<!-- /wp:html -->\n"

SHOWROOM = "Shop 01, Warehouse 02, Madinat Al Salam Warehouses, Industrial Area 5 (behind ENOC 1127), Sharjah"
GUIDES = {
  "second": a(SITE+"/second-hand-laptop-dubai/", "second hand laptop buying guide"),
  "work": a(SITE+"/refurbished-workstation-laptop-dubai/", "refurbished workstation laptops"),
  "mac": a(SITE+"/used-macbook-dubai/", "used MacBook prices"),
  "chrome": a(SITE+"/chromebook-price-in-uae/", "Chromebook price list"),
  "hp": a(SITE+"/hp-elitebook-vs-probook/", "HP EliteBook vs ProBook comparison"),
  "2in1": a(SITE+"/2-in-1-detachable-laptop-uae/", "2-in-1 detachable laptops"),
}

posts = []

# 1 ─ Sharjah
posts.append(dict(
  slug="buy-laptop-in-sharjah",
  title="Where to Buy a Laptop in Sharjah: A Local Buyer's Guide",
  focus="buy laptop in sharjah",
  kw="buy laptop in sharjah,laptop shop sharjah,used laptop sharjah,laptop shop near me",
  seo_title="Where to Buy a Laptop in Sharjah (2026): Local Buyer's Guide",
  desc="Buying a laptop in Sharjah? How to compare shops, what to check before you pay, fair prices for renewed laptops, and how to visit our Industrial Area 5 showroom.",
  body=
    p("Sharjah is one of the best places in the UAE to buy a laptop on a budget. The industrial areas are home to many computer traders, and prices for used and renewed business laptops are often lower than in Dubai malls. The choice can also be overwhelming. This guide explains how to <strong>buy a laptop in Sharjah</strong> without getting a bad unit, and what you should expect to pay.")
    + h2("Your options in Sharjah")
    + table(["Where", "Good for", "Watch out for"], [
        ["Mall electronics stores", "Brand-new laptops, instalment plans", "Highest prices, few business models"],
        ["Industrial area computer traders", "Wide choice of used and refurbished laptops", "Testing and warranty vary shop to shop"],
        ["Online marketplaces", "Lowest asking prices", "No testing, no warranty, meet-up risk"],
        ["Renewed laptop specialists (like Posh Laptop)", "Tested business laptops with warranty, online or in person", "Stock moves quickly, so check before visiting"],
      ])
    + h2("What to check before you pay")
    + ol([
        "<strong>Ask for a battery report.</strong> On Windows, the shop can run <code>powercfg /batteryreport</code>; on a Mac, check the cycle count in System Information.",
        "<strong>Look at the screen on white and black backgrounds</strong> for dead pixels, marks and uneven backlight.",
        "<strong>Test every port</strong>: USB, USB-C, HDMI, headphone jack and the charger port.",
        "<strong>Type a full sentence</strong> and use the trackpad, including right-click and scrolling.",
        "<strong>Check the processor generation.</strong> For Windows 11, choose Intel 8th Gen or newer.",
        "<strong>Confirm the Windows licence is activated</strong> and that no previous owner's account or company management is left on it.",
        "<strong>Get the warranty and return terms in writing</strong>, together with an invoice.",
      ])
    + p("Our " + GUIDES["second"] + " goes deeper on each of these checks.")
    + h2("What a renewed laptop costs in Sharjah")
    + p("Prices depend on the generation, RAM and condition. As a guide, these are examples from our current stock (AED):")
    + table(["Budget", "Example", "Price"], [
        ["Entry", a(P+"renewed-lenovo-thinkpad-t480-laptop-intel-core-i5-8th-gen-8gb-ram-256gb-ssd-14%e2%80%b3-display-integrated-graphics-business-laptop/", "Lenovo ThinkPad T480, i5 8th Gen, 8GB, 256GB"), "680"],
        ["Mid-range", a(P+"renewed-lenovo-thinkpad-t14-gen-2-laptop-14%e2%80%b3-fhd-intel-core-i5-11th-gen-16gb-ddr4-ram-256gb-ssd-windows-10-pro-business-laptop/", "Lenovo ThinkPad T14 Gen 2, i5 11th Gen, 16GB"), "899"],
        ["Professional", a(P+"renewed-dell-latitude-5550-laptop-15-6-fhd-intel-core-i7-16gb-ram-256gb-ssd-windows-11-pro/", "Dell Latitude 5550, Core i7, 16GB"), "1,299"],
        ["Premium", a(P+"renewed-lenovo-thinkpad-x1-carbon-gen-10-14%e2%80%b3-display-intel-core-i7-1260p-12th-gen-deca-core-16gb-ram-512gb-ssd-intel-iris-xe-premium-business-ultrabook/", "ThinkPad X1 Carbon Gen 10, i7 12th Gen, 16GB, 512GB"), "2,199"],
      ])
    + p("If a price is far below these, ask why: an old battery, a replaced screen or a locked BIOS are common reasons.")
    + h2("Visit the Posh Laptop showroom in Sharjah")
    + p(f"Our showroom is at <strong>{SHOWROOM}</strong>. You can see and test laptops in person, ask our technicians for advice, and arrange RAM or SSD upgrades. Stock changes daily, so message us on WhatsApp before you come and we will keep the models you want ready.")
    + cta("Message us before you visit")
    + p("Can't make it to Sharjah? We deliver across the UAE, usually in 1 to 2 days, and to Saudi Arabia and Qatar. Browse the " + a(SITE+"/shop/", "full shop") + " or see our " + GUIDES["mac"] + " and " + GUIDES["chrome"] + ".")
    + faq([
        ("Where can I buy a used laptop in Sharjah?", f"Sharjah's industrial areas have many computer traders. Posh Laptop's showroom is at {SHOWROOM}, where every renewed laptop is tested and sold with a warranty."),
        ("Are laptops cheaper in Sharjah than in Dubai?", "Used and renewed business laptops are often cheaper from Sharjah traders than from Dubai malls, but compare the testing, warranty and return terms as well as the price."),
        ("Do I need to visit the showroom to buy?", "No. You can order online and we deliver across the UAE, usually in 1 to 2 days. You are also welcome to visit and test laptops in person."),
        ("Can I upgrade RAM or SSD when I buy?", "Yes. Many business laptops can be upgraded, and our technicians can fit more RAM or a larger SSD before you collect or before delivery."),
      ]),
))

# 2 ─ Warranty
posts.append(dict(
  slug="refurbished-laptop-warranty-uae",
  title="Refurbished Laptop Warranty in the UAE: What's Covered and What to Ask",
  focus="refurbished laptop warranty uae",
  kw="refurbished laptop warranty uae,renewed laptop warranty,used laptop warranty dubai",
  seo_title="Refurbished Laptop Warranty in the UAE: What's Covered?",
  desc="How refurbished laptop warranties work in the UAE: what is usually covered, what isn't, how long they last, and the questions to ask before you buy.",
  body=
    p("A warranty is the main thing that separates a renewed laptop from a risky second hand one. But warranties on refurbished laptops in the UAE range from nothing at all to several months, and the details matter. Here is what a <strong>refurbished laptop warranty</strong> normally covers, and how Posh Laptop's works.")
    + h2("Typical warranty lengths in the UAE")
    + table(["Seller type", "Typical cover"], [
        ["Private seller or marketplace listing", "None, sold as-is"],
        ["Market trader", "Anything from a few days' checking period to a few weeks"],
        ["Wholesale suppliers", "Often 30 to 90 days, mainly for business buyers"],
        ["Posh Laptop", "6 months from delivery, plus a 15-day return window on online orders"],
      ])
    + p("Always read the period stated for the specific product, because some items such as accessories may have different terms.")
    + h2("What a warranty usually covers")
    + ul([
        "Hardware faults that appear during normal use: motherboard, RAM, SSD, screen, keyboard, ports",
        "Faults present at the time of sale that were not disclosed",
        "Repair, replacement with an equivalent unit, or refund, depending on the seller's terms",
      ])
    + h2("What is usually not covered")
    + p("Our own terms, like most sellers', exclude:")
    + ul([
        "Normal wear and tear",
        "Accidental damage, drops and liquid spills",
        "Misuse or abnormal working and storage conditions",
        "Repairs or modifications by someone other than the seller",
      ])
    + p("Batteries are consumable parts, so ask specifically how the battery is covered and what its health was at the time of sale.")
    + h2("7 questions to ask before you buy")
    + ol([
        "How long is the warranty, and does it start from purchase or delivery?",
        "Is it written on the invoice?",
        "Is it repair only, or replacement and refund too?",
        "Who pays for collection or delivery during a claim?",
        "Is the battery covered, and what is its current health?",
        "Is there a return window if I simply change my mind?",
        "Can I upgrade RAM or SSD without voiding the warranty?",
      ])
    + h2("How to make a claim with Posh Laptop")
    + ol([
        "Message us on WhatsApp with your order number and a short video or photo of the problem, as our terms ask for this evidence.",
        "Our team reviews the issue and tells you the next step.",
        "Eligible faults are repaired or the laptop is replaced under our warranty terms.",
      ])
    + p("Read the full details in our " + a(SITE+"/terms-conditions/", "terms and conditions") + ". After the warranty ends, our " + a(SITE+"/services/", "repair and AMC services") + " are still available.")
    + cta("Ask about warranty on a specific model")
    + faq([
        ("How long is the warranty on a refurbished laptop in the UAE?", "It depends on the seller: from none for private sales up to several months. Posh Laptop gives 6 months from delivery on renewed laptops, plus a 15-day return window on online orders."),
        ("Does the warranty cover the battery?", "Batteries are consumable, so cover varies. Ask the seller for the battery health at purchase and how battery faults are handled under the warranty."),
        ("Does a warranty cover accidental damage?", "Usually not. Accidental damage, drops, liquid spills and repairs by third parties are excluded from most refurbished laptop warranties, including ours."),
        ("Can I return a refurbished laptop if I change my mind?", "Posh Laptop accepts returns within 15 days of receipt on online orders, in original condition with accessories and packaging."),
      ]),
))

# 3 ─ Grades
posts.append(dict(
  slug="refurbished-laptop-grades-explained",
  title="Refurbished Laptop Grades Explained: Grade A vs B vs C",
  focus="refurbished laptop grades",
  kw="refurbished laptop grades,grade a refurbished laptop,grade a vs grade b laptop",
  seo_title="Refurbished Laptop Grades Explained: Grade A vs B vs C",
  desc="What Grade A, B and C mean on refurbished laptops, how grading affects price, and which grade is worth buying for work, study or gifting in the UAE.",
  body=
    p("When you shop for a refurbished laptop you will see labels like Grade A, A+, B or C. These <strong>refurbished laptop grades</strong> describe how the laptop looks, not how it performs. There is no single official standard, so each seller defines them a little differently. This guide explains the common meanings so you can compare like with like.")
    + h2("The common grading scale")
    + table(["Grade", "What it usually means", "Who it suits"], [
        ["A+ / Excellent", "Looks almost new; at most tiny marks visible only up close", "Gifts, client-facing roles"],
        ["A / Very good", "Light signs of use on the lid or base; clean screen and keyboard", "Most buyers"],
        ["B / Good", "Visible scratches or scuffs, possibly worn key letters; screen still clear", "Students and budget buyers who want the lowest price"],
        ["C / Fair", "Heavy cosmetic wear, small dents or screen marks", "Spare or lab machines; usually best avoided"],
      ])
    + h2("Grade is about looks, not performance")
    + p("A properly refurbished Grade B laptop should work exactly like a Grade A one. The processor, RAM, SSD, ports and battery are what decide performance, so always check those specs alongside the grade. A Grade A laptop with a worn-out battery can be a worse buy than a Grade B unit with a healthy one.")
    + h2("What to ask when a seller lists a grade")
    + ul([
        "Can I see real photos of this exact unit, not stock photos?",
        "Are there any screen marks or dead pixels?",
        "What is the battery health or cycle count?",
        "Has anything been replaced, such as the keyboard, screen or battery?",
        "Does the warranty differ between grades?",
      ])
    + h2("How Posh Laptop describes condition")
    + p("We call our laptops <strong>renewed</strong>: every unit is tested, cleaned and set up with a fresh operating system before it is listed, and it comes with the same warranty whatever its cosmetic condition. If you care about looks, for example for a gift, just ask and we will send photos of the exact laptop or keep the cleanest units for you.")
    + cta("Ask for photos of a specific laptop")
    + p("Ready to choose a model? Start with our " + GUIDES["second"] + ", compare business lines in the " + GUIDES["hp"] + ", or browse " + a(T+"refurbished-thinkpad/", "refurbished ThinkPads") + ".")
    + faq([
        ("What does Grade A refurbished mean?", "Grade A usually means the laptop shows only light signs of use, with a clean screen and keyboard. Definitions vary by seller, so ask for photos of the exact unit."),
        ("Is a Grade B refurbished laptop worth buying?", "Yes, if you don't mind visible scratches. A properly refurbished Grade B laptop performs the same as Grade A and usually costs less."),
        ("Does the grade affect performance?", "No. Grades describe cosmetic condition. Performance depends on the processor, RAM, storage and battery health."),
        ("Is there an official refurbished grading standard?", "There is no single official standard used by all sellers, so compare descriptions and photos rather than relying on the grade label alone."),
      ]),
))

# 4 ─ ThinkPad vs EliteBook vs Latitude
posts.append(dict(
  slug="thinkpad-vs-elitebook-vs-latitude",
  title="ThinkPad vs EliteBook vs Latitude: Which Refurbished Business Laptop Is Best?",
  focus="thinkpad vs elitebook vs latitude",
  kw="thinkpad vs elitebook vs latitude,refurbished thinkpad uae,dell latitude vs thinkpad",
  seo_title="ThinkPad vs EliteBook vs Latitude: Best Refurbished Business Laptop",
  desc="Lenovo ThinkPad, HP EliteBook and Dell Latitude compared for refurbished buyers in the UAE: keyboards, build, ports, upgrades and real prices from AED 680.",
  body=
    p("Lenovo ThinkPad, HP EliteBook and Dell Latitude are the three business laptop families you will see most often on the refurbished market in the UAE. All three were built for corporate fleets, which is why they last so well second time around. So which should you buy? Here is an honest <strong>ThinkPad vs EliteBook vs Latitude</strong> comparison.")
    + h2("At a glance")
    + table(["", "Lenovo ThinkPad", "HP EliteBook", "Dell Latitude"], [
        ["Known for", "Best-in-class keyboard, TrackPoint, durability", "Premium aluminium build, security features", "Wide range of sizes, easy to service"],
        ["Popular lines", "T series, X1 Carbon, X13, E series", "830, 840, 850, x360", "5000 and 7000 series, 2-in-1 detachables"],
        ["Upgrades", "Many T and E models take extra RAM and a second SSD", "Varies; many 840 models are upgradeable", "Most 5000-series models are easy to upgrade"],
        ["Best for", "Writers, developers, heavy typists", "Executives and travellers", "Office fleets, field teams"],
      ])
    + h2("Lenovo ThinkPad")
    + p("ThinkPads have the keyboard most people prefer for long typing sessions, and many models include the red TrackPoint. The <strong>T series</strong> is the all-rounder, the <strong>X1 Carbon</strong> is the light premium option, and the <strong>E series</strong> is the budget line.")
    + table(["Model (our stock)", "Specs", "Price (AED)"], [
        [a(P+"renewed-lenovo-thinkpad-t480-laptop-intel-core-i5-8th-gen-8gb-ram-256gb-ssd-14%e2%80%b3-display-integrated-graphics-business-laptop/", "ThinkPad T480"), "i5 8th Gen, 8GB, 256GB", "680"],
        [a(P+"renewed-lenovo-thinkpad-t490-laptop-14%e2%80%b3-full-hd-intel-core-i5-8365u-8th-gen-8gb-ram-256gb-ssd-intel-uhd-graphics-windows-10-pro/", "ThinkPad T490"), "i5-8365U, 8GB, 256GB", "690"],
        [a(P+"renewed-lenovo-thinkpad-t14-gen-2-laptop-14%e2%80%b3-fhd-intel-core-i5-11th-gen-16gb-ddr4-ram-256gb-ssd-windows-10-pro-business-laptop/", "ThinkPad T14 Gen 2"), "i5 11th Gen, 16GB, 256GB", "899"],
        [a(P+"renewed-lenovo-thinkpad-t14s-gen-3-14%e2%80%b3-fhd-intel-core-i7-1255u-12th-gen-16gb-ram-256-gb-ssd/", "ThinkPad T14s Gen 3"), "i7-1255U, 16GB, 256GB", "1,199"],
        [a(P+"renewed-lenovo-thinkpad-x1-carbon-gen-9-14%e2%80%b3-full-hd-intel-core-i7-11th-gen-16gb-ram-256-gb-ssd-windows-10-pro-premium-business-ultrabook/", "ThinkPad X1 Carbon Gen 9"), "i7 11th Gen, 16GB, 256GB", "1,649"],
        [a(P+"renewed-lenovo-thinkpad-x1-carbon-gen-10-14%e2%80%b3-display-intel-core-i7-1260p-12th-gen-deca-core-16gb-ram-512gb-ssd-intel-iris-xe-premium-business-ultrabook/", "ThinkPad X1 Carbon Gen 10"), "i7-1260P, 16GB, 512GB", "2,199"],
      ])
    + p("Browse all " + a(T+"refurbished-thinkpad/", "refurbished ThinkPads") + ".")
    + h2("HP EliteBook")
    + p("EliteBooks feel the most premium of the three, with metal bodies, bright screens on newer models, and HP's security tools such as Sure Start and, on some models, the Sure View privacy screen. The 14-inch <strong>840</strong> is the classic choice and the 13.3-inch <strong>830</strong> is the travel option. If you are deciding between HP's own lines, read our " + GUIDES["hp"] + ", or browse " + a(T+"renewed-hp-laptop/", "renewed HP laptops") + ".")
    + h2("Dell Latitude")
    + p("Latitudes are everywhere in UAE offices, which means parts and chargers are easy to find. The <strong>5000 series</strong> is the practical workhorse and the <strong>7000 series</strong> is lighter and more premium. Dell also makes some of the best business 2-in-1s, such as the Latitude 7320 Detachable; see our " + GUIDES["2in1"] + ". Browse " + a(T+"renewed-dell-laptop-uae/", "renewed Dell laptops in the UAE") + ".")
    + h2("Which one should you choose?")
    + ul([
        "<strong>You type all day:</strong> ThinkPad T series or X1 Carbon.",
        "<strong>You travel or meet clients:</strong> EliteBook 830/840 or ThinkPad X1 Carbon.",
        "<strong>You are equipping an office:</strong> Dell Latitude 5000 series or ThinkPad T series, for easy servicing.",
        "<strong>Lowest price that still runs Windows 11:</strong> an 8th Gen ThinkPad T480 or T490.",
        "<strong>Engineering or design software:</strong> step up to a workstation; see our " + GUIDES["work"] + ".",
      ])
    + p("In practice, the generation, RAM and battery matter more than the brand. A newer EliteBook beats an older ThinkPad, and the other way round.")
    + cta("Tell us your budget and we'll suggest a model")
    + faq([
        ("Is ThinkPad better than Dell Latitude?", "ThinkPads are usually preferred for their keyboard and durability, while Latitudes are easy to service and widely used in offices. For the same generation and specs, both are excellent refurbished choices."),
        ("Which is better, HP EliteBook or Lenovo ThinkPad?", "EliteBooks have a more premium metal build and security features; ThinkPads have the better keyboard. Pick based on whether you value build and looks or typing comfort."),
        ("What is the cheapest refurbished ThinkPad that runs Windows 11?", "8th Gen Intel ThinkPads such as the T480 and T490 meet Windows 11's processor requirement and start from about AED 680 at Posh Laptop."),
        ("Are refurbished business laptops reliable?", "Yes. Business lines like ThinkPad, EliteBook and Latitude are built for years of corporate use, and a tested renewed unit with a warranty is a reliable choice."),
      ]),
))

# 5 ─ Windows 11
posts.append(dict(
  slug="windows-11-compatible-refurbished-laptops",
  title="Which Refurbished Laptops Can Run Windows 11? A Simple Checklist",
  focus="windows 11 compatible refurbished laptop",
  kw="windows 11 compatible refurbished laptop,refurbished laptop windows 11 uae,windows 11 laptop 8th gen",
  seo_title="Windows 11 Compatible Refurbished Laptops: Simple Checklist",
  desc="Will a refurbished laptop run Windows 11? Check the processor generation, TPM 2.0 and Secure Boot, and see which renewed models from AED 680 qualify.",
  body=
    p("Windows 10 support has ended, so if you are buying a refurbished laptop today it makes sense to choose one that can run Windows 11 and keep getting security updates. The good news is that most business laptops from 2018 onwards qualify. Here is how to tell whether a <strong>refurbished laptop is Windows 11 compatible</strong>.")
    + h2("Microsoft's main requirements")
    + table(["Requirement", "What to look for"], [
        ["Processor", "Intel Core 8th Gen or newer, or AMD Ryzen 2000 series or newer (on Microsoft's supported list)"],
        ["TPM", "TPM 2.0 enabled in the BIOS"],
        ["Secure Boot", "UEFI with Secure Boot support"],
        ["RAM", "At least 4GB; we recommend 8GB minimum and 16GB for multitasking"],
        ["Storage", "At least 64GB; in practice choose a 256GB SSD or larger"],
      ])
    + p("Check Microsoft's PC Health Check app or its list of supported processors if you are unsure about a specific model.")
    + h2("How to read a laptop listing")
    + p("Look at the processor model number. In Intel names, the first digit or two after the dash show the generation:")
    + ul([
        "<strong>i5-6200U</strong> = 6th Gen: <strong>not</strong> officially supported",
        "<strong>i5-7300U</strong> = 7th Gen: <strong>not</strong> officially supported (with very few exceptions)",
        "<strong>i5-8365U</strong> = 8th Gen: supported",
        "<strong>i7-1165G7</strong> = 11th Gen: supported",
        "<strong>i7-1255U</strong> = 12th Gen: supported",
      ])
    + h2("Windows 11 ready laptops from our stock")
    + table(["Model", "Processor", "Price (AED)"], [
        [a(P+"renewed-lenovo-thinkpad-t480-laptop-intel-core-i5-8th-gen-8gb-ram-256gb-ssd-14%e2%80%b3-display-integrated-graphics-business-laptop/", "Lenovo ThinkPad T480"), "Core i5 8th Gen", "680"],
        [a(P+"renewed-dell-latitude-5290-2-in-1-laptop-12-3-touch-intel-core-i5-8th-gen-8gb-ram-256gb-ssd/", "Dell Latitude 5290 2-in-1"), "Core i5 8th Gen", "699"],
        [a(P+"renewed-lenovo-thinkpad-t14-gen-2-laptop-14%e2%80%b3-fhd-intel-core-i5-11th-gen-16gb-ddr4-ram-256gb-ssd-windows-10-pro-business-laptop/", "Lenovo ThinkPad T14 Gen 2"), "Core i5 11th Gen", "899"],
        [a(P+"renewed-dell-xps-13-9300-laptop-13-4-fhd-intel-core-i7-10th-gen-8gb-ram-256gb-ssd-windows-11-ultra-slim/", "Dell XPS 13 9300"), "Core i7 10th Gen", "999"],
        [a(P+"renewed-lenovo-thinkpad-t14s-gen-3-14%e2%80%b3-fhd-intel-core-i7-1255u-12th-gen-16gb-ram-256-gb-ssd/", "Lenovo ThinkPad T14s Gen 3"), "Core i7 12th Gen", "1,199"],
        [a(P+"renewed-dell-latitude-7320-detachable-2-in-1-laptop-13-fhd-touch-intel-core-i5-1140g7-11th-gen-16gb-ram-256gb-ssd-windows-11/", "Dell Latitude 7320 Detachable"), "Core i5 11th Gen", "1,299"],
      ])
    + p("Some listings still say Windows 10 because that is how the laptop was originally supplied. If the processor is 8th Gen or newer, ask us and we can install Windows 11 before delivery.")
    + h2("What about older laptops?")
    + p("Laptops with 6th or 7th Gen processors can still be good value for basic tasks, but they are not officially supported by Windows 11. For a low-cost machine that keeps getting updates, a renewed Chromebook is another option; see our " + GUIDES["chrome"] + ".")
    + cta("Ask us to install Windows 11 before delivery")
    + faq([
        ("Can a refurbished laptop run Windows 11?", "Yes, if it has a supported processor (generally Intel 8th Gen or newer, or AMD Ryzen 2000 series or newer), TPM 2.0 and Secure Boot. Most business laptops from 2018 onwards qualify."),
        ("Does an Intel 7th Gen laptop support Windows 11?", "Generally no. Apart from a very small number of exceptions, Intel 7th Gen processors are not on Microsoft's supported list."),
        ("How do I check if my laptop is Windows 11 compatible?", "Run Microsoft's PC Health Check app, or check the processor model against Microsoft's list of supported processors."),
        ("Will you install Windows 11 on a laptop I buy?", "Yes. If the laptop meets the requirements, we can install Windows 11 before delivery. Just ask when you order."),
      ]),
))

# 6 ─ Business / bulk
posts.append(dict(
  slug="bulk-refurbished-laptops-for-business-uae",
  title="Buying Refurbished Laptops in Bulk for Your Business in the UAE",
  focus="bulk refurbished laptops uae",
  kw="bulk refurbished laptops uae,laptops for business uae,corporate laptops dubai,laptop amc uae",
  seo_title="Bulk Refurbished Laptops for Business in the UAE: Buyer's Guide",
  desc="Equipping an office, school or call centre in the UAE? How to buy refurbished laptops in bulk: choosing models, budgeting, setup, warranty, rental and AMC.",
  body=
    p("For startups, schools, call centres and growing SMEs, renewed business laptops can cut the cost of equipping a team by half or more compared with buying new. This guide covers how to buy <strong>refurbished laptops in bulk</strong> in the UAE without ending up with a mixed fleet that is hard to support.")
    + h2("Step 1: Standardise on one or two models")
    + p("A fleet of identical laptops is far easier to set up, repair and supply chargers for. Business lines such as the Lenovo ThinkPad T series, Dell Latitude 5000 series and HP EliteBook 840 are ideal because they were made for corporate fleets. Our " + a(SITE+"/thinkpad-vs-elitebook-vs-latitude/", "ThinkPad vs EliteBook vs Latitude guide") + " compares them.")
    + h2("Step 2: Match specs to roles")
    + table(["Role", "Suggested spec", "Example budget per unit (AED)"], [
        ["Call centre, reception, data entry", "Core i5 8th Gen+, 8GB RAM, 256GB SSD", "680 – 900"],
        ["Office staff, sales, managers", "Core i5/i7 11th Gen+, 16GB RAM, 256–512GB SSD", "900 – 1,300"],
        ["Engineers, designers", "Workstation with NVIDIA GPU, 16–32GB RAM", "1,500+"],
        ["Students and classrooms", "Chromebook or Core i5 8th Gen+ laptop", "199 – 700"],
      ])
    + p("Budgets are based on examples from our current stock and vary with availability. See also our " + GUIDES["work"] + " and " + GUIDES["chrome"] + ".")
    + h2("Step 3: Plan setup and delivery")
    + ul([
        "Windows edition and licence (Windows 11 Pro for most businesses)",
        "Standard software image, company accounts and antivirus",
        "Arabic/English keyboards if your team needs them",
        "Asset tags and serial number list for your records",
        "Delivery to one or several sites across the UAE",
      ])
    + h2("Step 4: Agree warranty and support in writing")
    + p("Ask how faults will be handled across the whole fleet: turnaround time, spare units and on-site support. Posh Laptop's renewed laptops come with a 6-month warranty, and our " + a(SITE+"/services/", "Annual Maintenance Contracts (AMC)") + " add regular maintenance, health checks and priority repairs after that. Read more about " + a(SITE+"/refurbished-laptop-warranty-uae/", "how refurbished laptop warranties work") + ".")
    + h2("Buy, rent or both?")
    + p("For short projects, events, exhibitions or temporary staff, renting can make more sense than buying. We offer short and long-term laptop rental alongside sales, so you can buy laptops for permanent staff and rent extra units when you need them.")
    + cta("Request a bulk quote on WhatsApp")
    + p(f"You are welcome to visit our showroom at {SHOWROOM} to see sample units before you order, or read our guide to " + a(SITE+"/buy-laptop-in-sharjah/", "buying a laptop in Sharjah") + ".")
    + faq([
        ("Can I buy refurbished laptops in bulk in the UAE?", "Yes. Posh Laptop supplies renewed business laptops in quantity for offices, schools and call centres, with setup, delivery across the UAE and warranty included."),
        ("Are refurbished laptops good for businesses?", "Yes. Business lines like ThinkPad, Latitude and EliteBook were built for corporate use, and tested renewed units cost much less than new while offering similar reliability."),
        ("Do you offer maintenance contracts for company laptops?", "Yes. Our Annual Maintenance Contracts cover regular maintenance, software updates, health checks and priority repair support."),
        ("Can we rent laptops instead of buying?", "Yes. We offer short and long-term laptop rental for projects, events and temporary staff, and you can combine rental with purchases."),
      ]),
))

for pg in posts:
    open(f"{pg['slug']}.html", "w").write(pg["body"])
    body = {"title": pg["title"], "slug": pg["slug"], "status": "draft", "content": pg["body"]}
    open(f"{pg['slug']}.json", "w").write(json.dumps(body, ensure_ascii=False))
json.dump([{k: v for k, v in pg.items() if k != "body"} for pg in posts], open("posts.json", "w"), indent=2, ensure_ascii=False)
for pg in posts:
    print(pg["slug"], len(re.sub(r"<[^>]+>", " ", pg["body"]).split()), "words |", len(pg["seo_title"]), "|", len(pg["desc"]))
