import json, sys, os
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from blocks import links_section
ANCHOR = '<!-- wp:group {"className":"cfm-section cfm-areas"} -->'
EM = {"abu-dhabi": ("Abu Dhabi", 41), "sharjah": ("Sharjah", 42), "ajman": ("Ajman", 43), "ras-al-khaimah": ("Ras Al Khaimah", 44),
      "fujairah": ("Fujairah", 45), "umm-al-quwain": ("Umm Al Quwain", 46), "al-ain": ("Al Ain", 47)}
INTERIOR_IDS = {}
i = 444
for k in ["abu-dhabi", "sharjah", "ajman", "ras-al-khaimah", "fujairah", "umm-al-quwain", "al-ain"]:
    for s in ["curtains", "blinds", "carpets"]:
        INTERIOR_IDS[(s, k)] = i; i += 1
edits = []
# 1. emirate floor-mat pages -> interiors in same emirate
for k, (name, pid) in EM.items():
    sec = links_section("Casa Vera Home", f"Curtains, blinds &amp; carpets in {name}", [
        (f"Curtains in {name}", f"/interiors-uae/curtains-{k}/"), (f"Blinds in {name}", f"/interiors-uae/blinds-{k}/"),
        (f"Carpets in {name}", f"/interiors-uae/carpets-{k}/"), ("Event &amp; exhibition flooring", "/events-exhibitions/"),
        ("Best floor mats: buying guide", "/buying-guides/best-floor-mats-dubai/")])
    edits.append((pid, sec))
# 2. interior emirate pages -> floor mats in that emirate + sibling services
for (s, k), pid in INTERIOR_IDS.items():
    name = EM[k][0]
    others = [x for x in ["curtains", "blinds", "carpets"] if x != s]
    sec = links_section("Also in " + name, f"More for your property in {name}", [
        (f"Floor mats in {name}", f"/areas-we-serve/floor-mats-{k}/"),
        (f"{others[0].capitalize()} in {name}", f"/interiors-uae/{others[0]}-{k}/"),
        (f"{others[1].capitalize()} in {name}", f"/interiors-uae/{others[1]}-{k}/"),
        ("Event &amp; exhibition flooring", "/events-exhibitions/")])
    edits.append((pid, sec))
# 3. Dubai area pages (old 33-40 + new 431-442) -> Dubai interiors, events, guides
dubai = [(33, "Dubai")] + [(i_, None) for i_ in range(34, 41)] + [(i_, None) for i_ in range(431, 443)]
for pid, _ in dubai:
    sec = links_section("Casa Vera Home · Dubai", "Curtains, blinds, carpets &amp; events in Dubai", [
        ("Curtains in Dubai", "/curtains/"), ("Blinds in Dubai", "/blinds/"), ("Carpets in Dubai", "/carpets/"),
        ("Event &amp; exhibition flooring", "/events-exhibitions/"), ("Best floor mats in Dubai: buying guide", "/buying-guides/best-floor-mats-dubai/"),
        ("How to choose a floor mat supplier", "/buying-guides/floor-mat-supplier-dubai/")])
    edits.append((pid, sec))
reqs = [{"method": "POST", "path": "/wpvibe/v1/content/edit", "body": {"target_type": "post", "post_id": pid, "field": "post_content",
         "old_content": ANCHOR, "new_content": sec + ANCHOR}} for pid, sec in edits]
json.dump(reqs, open("xlinks.json", "w"), ensure_ascii=False)
print(len(reqs)); print(json.dumps(reqs[0]["body"], ensure_ascii=False))
