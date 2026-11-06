import json
# page_id: (slug, label, featured_code_or_None, [gallery codes])
P = {
 # curtains
 50: ("blackout-curtains", "Blackout curtains", 233, [219, 462, 463]),
 51: ("sheer-curtains", "Sheer curtains", 443, [442, 451, 188]),
 52: ("curtain-rods-tracks", "Curtain track and rod", 213, [207, 210, 232]),
 53: ("motorized-curtains", "Motorised curtains", 225, [209, 253, 241]),
 54: ("office-curtains", "Office and clinic curtains", 399, [401, 397]),
 55: ("villa-curtain-installation", "Villa curtain installation", 437, [441, 449, 446, 453, 444, 448]),
 56: ("curtain-alteration-stitching", "Made-to-measure curtain stitching", 248, [197, 192, 229]),
 # blinds
 73: ("roller-blinds", "Roller blinds", 840, [831, 395]),
 74: ("venetian-blinds", "Venetian blinds", 162, [169, 166, 167]),
 75: ("vertical-blinds", "Vertical blinds", 837, [845, 853]),
 76: ("blackout-blinds", "Blackout blinds", 828, [833]),
 77: ("roman-blinds", "Roman blinds", 854, [855, 847, 849]),
 78: ("motorized-blinds", "Motorised day-and-night blinds", 408, [410, 829]),
 79: ("office-blinds", "Office blinds", 403, [391, 396]),
 # carpets
 58: ("wall-to-wall-carpet", "Wall-to-wall carpet", 266, [293, 298]),
 60: ("exhibition-carpet", "Exhibition carpet", 135, [133, 144, 138]),
 63: ("custom-carpet-runners", "Stair carpet runner", 343, [276, 284, 348]),
 # wallpaper
 65: ("3d-wallpaper", "Tropical feature wallpaper", 22, [10, 11]),
 66: ("textured-wallpaper", "Textured damask wallpaper", 21, [19, 46]),
 69: ("custom-wall-murals", "Custom wall mural", 20, [24, 23, 44]),
 70: ("wallpaper-removal", "Wall preparation for wallpaper", 37, [38]),
 71: ("commercial-wallpaper-installation", "Wallpaper installation", 2, [9, 39, 40]),
 # flooring
 88: ("wooden-flooring", "Wooden flooring", 64, [62, 54, 57]),
 89: ("vinyl-flooring", "Vinyl flooring", 146, [151, 264, 150]),
 91: ("epoxy-flooring", "Epoxy flooring", 328, [326, 331, 325]),
 92: ("sports-flooring", "Sports flooring and turf", 94, [88, 822, 382]),
 93: ("rubber-flooring", "Poured EPDM rubber flooring", 120, [116, 858]),
 94: ("gym-flooring", "Gym flooring", 859, [825, 807, 814]),
 # mats (keep current featured, add real gallery photos)
 18: ("custom-logo-mats", "Custom logo mat", None, [289]),
 20: ("outdoor-scraper-mats", "Rubber scraper mat", None, [123, 126, 125]),
 23: ("anti-fatigue-mats", "Anti-fatigue mat", None, [170, 177, 180]),
 24: ("rubber-gym-mats", "Rubber gym mats", None, [818, 815, 860]),
 25: ("anti-slip-wet-area-mats", "Anti-slip wet-area mat", None, [171, 426, 432, 423]),
 29: ("kids-play-mats", "Kids play area flooring", None, [122, 863, 117]),
 # events
 465: ("events-exhibitions", "Event carpet", 134, []),
 466: ("exhibition-stand-flooring", "Exhibition stand flooring", 132, [141, 140]),
 467: ("red-carpet-event-carpet", "Red carpet", 137, [136, 139]),
 468: ("branded-event-mats", "Branded promotional mat", 321, []),
 470: ("wedding-gala-carpet", "Wedding aisle carpet", 143, [142]),
}
EM = ["abu-dhabi", "sharjah", "ajman", "ras-al-khaimah", "fujairah", "umm-al-quwain", "al-ain"]
EMN = ["Abu Dhabi", "Sharjah", "Ajman", "Ras Al Khaimah", "Fujairah", "Umm Al Quwain", "Al Ain"]
pick = {"curtains": [224, 227, 237, 206, 251, 189, 202], "blinds": [849, 413, 393, 831, 838, 164, 838 and 834], "carpets": [315, 283, 303, 339, 176, 175, 292]}
pick["blinds"] = [846, 413, 393, 159, 838, 161, 841]
pid = 444
for k, n in zip(EM, EMN):
    for j, s in enumerate(["curtains", "blinds", "carpets"]):
        P[pid] = (f"{s}-{k}", f"{s.capitalize()} in {n}", pick[s][EM.index(k)], [])
        pid += 1
sel = {i["code"]: i for i in json.load(open("drv/sel.json"))}
codes = []
for pg, (slug, label, f, g) in P.items():
    for c in ([f] if f is not None else []) + g:
        codes.append(c)
dup = [c for c in set(codes) if codes.count(c) > 1]
print("pages", len(P), "uploads", len(codes), "dups", dup)
for c in codes:
    if sel[c]["mime"] not in ("image/jpeg", "image/png", "image/webp"):
        print("MIME", c, sel[c]["mime"], sel[c]["title"])
json.dump({str(k): v for k, v in P.items()}, open("plan.json", "w"))
