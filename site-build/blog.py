"""SEO blog posts, planned from Google Trends (Dubai, 30 Jun – 30 Sep 2026).

Each post targets a query from the top / rising lists, links to the matching
product and area pages, uses photos already in the Media Library and ends
with a WhatsApp call to action. Guidance is general industry practice
("typically", "usually") — no prices, delivery times or warranty claims.

Section item formats:
    "html paragraph"          -> paragraph
    ("ul", [items])           -> bullet list
    ("ol", [items])           -> numbered list
    ("img", key, alt)         -> image from the Media Library
    ("h3", "text")            -> sub-heading
"""
from data import (WAVE, WAVE_INSTALL, INSTALL_PHOTO, PINCH, EYELET, AMERICAN, ROMAN_C, KIDS, HOSPITAL,
                  LOGO_BLINDS, PRINTED, ZEBRA, VERTICAL, WOODEN, ALU, BAMBOO, ROMAN_B, SUNSCREEN,
                  BLACKOUT_ROLLER, PROJECTS)

P = "/curtains/"
B = "/blinds/"


def a(href, text):
    return f'<a href="{href}">{text}</a>'


POSTS = [
    # 1 ------------------------------------------------------------------
    dict(
        key="how-to-measure", slug="how-to-measure-for-curtains",
        title="How to Measure for Curtains: A Step-by-Step Guide for Dubai Homes",
        cat="Buying Guides", query="how to measure for curtains",
        excerpt="Learn how to measure for curtains and blinds correctly — width, drop, fullness and track position — with tips for Dubai's floor-to-ceiling windows.",
        featured=INSTALL_PHOTO[0],
        intro="“How to measure for curtains” is one of the fastest-growing curtain searches in Dubai — and for good reason. A few centimetres decide whether your curtains stack neatly, block light properly and reach the floor. This guide walks you through the method professionals use.",
        sections=[
            ("What you need before you start", [
                ("ul", ["A steel measuring tape (fabric tapes stretch)", "A notepad or your phone to record each window separately",
                        "A step ladder for ceiling-height tracks", "A decision on style: curtains on a track or rod, or blinds inside the window recess"]),
                "Always measure in centimetres or millimetres and label every window (for example “Living room – left”). In villas and apartments, windows that look identical are often a few millimetres different.",
            ]),
            ("Step 1: Decide where the track or rod will go", [
                "For most Dubai apartments with full-height glass, a <strong>ceiling-mounted track</strong> gives the cleanest look and the best light control. For standard windows, a wall-mounted track or rod usually sits 10–15 cm above the window frame.",
                "Mount it higher than the window and the room looks taller; mount it wider and the curtains can stack off the glass so you keep the full view when they are open.",
                ("img", INSTALL_PHOTO[0], "Curtain track installed on the ceiling in a Dubai apartment"),
            ]),
            ("Step 2: Measure the width", [
                ("ol", ["Measure the full width of the window or glass area.",
                        "Add an extension on each side — typically 15–25 cm — so the curtains can stack beside the window instead of covering it.",
                        "This total is your <strong>track or rod width</strong>."]),
                "The fabric itself is wider than the track because curtains need fullness to fold. As a rule of thumb, fabric width is usually about <strong>2 to 2.5 times</strong> the track width for wave and pinch pleat curtains, and around <strong>1.5 to 2 times</strong> for eyelet curtains.",
            ]),
            ("Step 3: Measure the drop (length)", [
                "Measure from the top of the track (or the bottom of the rod rings) down to where you want the curtain to finish. Take this measurement in three places — left, centre and right — and use the shortest, because floors and ceilings are rarely perfectly level.",
                ("ul", ["<strong>Floor-skimming:</strong> subtract about 1 cm so the curtain just clears the floor — the most popular finish.",
                        "<strong>Puddled:</strong> add 5–15 cm for a relaxed, luxurious look in formal rooms.",
                        "<strong>Sill length:</strong> finish about 1 cm above the window sill, common for kitchens and kids rooms."]),
            ]),
            ("How to measure for blinds", [
                "Blinds are measured differently. For an <strong>inside-recess</strong> fit, measure the width at the top, middle and bottom of the recess and use the smallest; then measure the height on the left, centre and right. For an <strong>outside-recess</strong> fit, add roughly 5–10 cm to each side so the blind overlaps the frame and blocks more light.",
                f"Not sure which blind suits the window? See our guides to {a(B + 'blackout-roller-blinds/', 'blackout roller blinds')}, {a(B + 'zebra-blinds/', 'zebra blinds')} and {a(B + 'roman-blinds/', 'Roman blinds')}.",
            ]),
            ("Common measuring mistakes", [
                ("ul", ["Measuring only the glass and forgetting the stacking space",
                        "Using one height for every window in the room",
                        "Forgetting skirting boards, AC grilles or handles that the curtain must clear",
                        "Ordering ready-made sizes that are close, but not right, for tall Dubai windows"]),
            ]),
            ("Let us measure for you", [
                f"If you would rather not climb the ladder, our team offers a <strong>free home visit and measurement across the UAE</strong>. We measure every window, bring fabric samples and recommend the right track, fullness and finish. {a(P + 'wave-curtains/', 'Wave curtains')} and {a(P + 'blackout-curtains/', 'blackout curtains')} are the most requested for Dubai apartments.",
            ]),
        ],
        faq=[("How much wider than the window should curtains be?", "The track is usually 15–25 cm wider on each side of the window, and the fabric itself is about 2–2.5 times the track width for wave and pinch pleat curtains."),
             ("Should curtains touch the floor?", "Most people choose a floor-skimming finish about 1 cm above the floor. It looks tailored and keeps the hem clean."),
             ("Can you measure my windows for free?", "Yes — we offer a free home visit and measurement across the UAE. Message us on WhatsApp to book a time.")],
        topic="measuring my windows for curtains",
    ),
    # 2 ------------------------------------------------------------------
    dict(
        key="blackout-guide", slug="blackout-curtains-dubai-guide",
        title="Blackout Curtains in Dubai: The Complete Buying Guide",
        cat="Buying Guides", query="blackout curtains dubai",
        excerpt="Everything to know about blackout curtains in Dubai — fabrics vs linings, how to stop light gaps, heat control, colours and pairing with sheers.",
        featured="blackout-bedroom",
        intro="Blackout curtains are the most searched curtain type in Dubai after “curtains” itself. With strong sun for most of the year, they do more than darken a bedroom — they help keep rooms cooler and screens glare-free. Here is how to choose them well.",
        sections=[
            ("What makes a curtain “blackout”?", [
                "Blackout curtains stop light passing through the fabric. They are usually made in one of three ways:",
                ("ul", ["<strong>Triple-weave fabrics</strong> — a dense weave with a dark inner yarn that blocks light while keeping a soft hand.",
                        "<strong>Coated fabrics</strong> — a light-blocking coating on the back of the cloth.",
                        "<strong>Blackout lining</strong> — a separate lining sewn behind any decorative fabric, so you can choose almost any colour or texture."]),
                ("img", "blackout-bedroom", "Blackout curtains in a Dubai bedroom"),
            ]),
            ("How to stop light leaking around the edges", [
                "Even a perfect blackout fabric lets light in if the curtain is the wrong size. To get close to full darkness:",
                ("ul", ["Use a <strong>ceiling-mounted track</strong> so light cannot escape over the top.",
                        "Extend the track 15–25 cm beyond each side of the window.",
                        "Choose an overlap where the two curtains meet in the centre.",
                        "Finish the curtain just above the floor rather than at the sill."]),
            ]),
            ("Blackout curtains and heat", [
                "By blocking direct sun on the glass, blackout curtains reduce the heat that builds up in west- and south-facing rooms during Dubai afternoons. A light-coloured or coated back reflects more sunlight than a dark one, which is why many blackout fabrics are pale on the window side.",
            ]),
            ("Colours and styles", [
                f"Blackout does not have to mean dark. Neutral greys, beiges and whites are the most popular in Dubai homes, and any heading works: {a(P + 'wave-curtains/', 'wave')}, {a(P + 'pinch-pleat-curtains/', 'pinch pleat')}, {a(P + 'eyelet-curtains/', 'eyelet')} or {a(P + 'american-style-curtains/', 'American style')}.",
                ("img", "blackout-office", "Blackout curtains in a corporate meeting room"),
            ]),
            ("Pair blackout with sheer curtains", [
                f"The most practical set-up for bedrooms is a <strong>double track</strong>: a {a(P + 'sheer-curtains/', 'sheer curtain')} for soft daylight and privacy, plus a blackout curtain for sleep. Each runs independently — and both can be {a(P + 'motorized-curtains/', 'motorized')}.",
            ]),
            ("Blackout blinds as an alternative", [
                f"Where space is tight, {a(B + 'blackout-roller-blinds/', 'blackout roller blinds')} or blackout-lined {a(B + 'roman-blinds/', 'Roman blinds')} give darkness in a slim footprint — ideal for kids rooms, studies and media rooms. Many clients combine a blackout blind with decorative curtains.",
            ]),
        ],
        faq=[("Do blackout curtains keep a room cooler?", "They reduce direct sunlight and the heat it brings through the glass, which helps keep sun-facing rooms more comfortable."),
             ("Are blackout curtains 100% dark?", "The fabric blocks light, but gaps at the top and sides can let light in. A ceiling track, wider coverage and a centre overlap give the best result."),
             ("Can I have blackout curtains in a light colour?", "Yes. Blackout linings and triple-weave fabrics come in light neutrals as well as dark shades.")],
        topic="blackout curtains",
    ),
    # 3 ------------------------------------------------------------------
    dict(
        key="readymade-vs-custom", slug="ready-made-vs-made-to-measure-blackout-curtains",
        title="Ready-Made vs Made-to-Measure Blackout Curtains in Dubai",
        cat="Buying Guides", query="ikea blackout curtains",
        excerpt="Store-bought or tailored? Compare ready-made and made-to-measure blackout curtains for Dubai windows — fit, light gaps, fullness, lining and value.",
        featured=WAVE[0],
        intro="Searches for ready-made blackout curtains from big furniture stores jumped this summer. They can be a good quick fix — but for many Dubai windows, a made-to-measure curtain performs very differently. Here is an honest comparison.",
        sections=[
            ("When ready-made curtains work well", [
                ("ul", ["Standard-size windows that match the pack sizes",
                        "Short-term rentals where you may move soon",
                        "Secondary rooms where a perfect finish matters less"]),
            ]),
            ("Where made-to-measure makes the difference", [
                "Many Dubai apartments and villas have floor-to-ceiling glass, extra-wide sliding doors, corner windows or double-height living rooms. Ready-made curtains come in fixed widths and drops, so on these windows they are often too short, too narrow or need several panels joined together.",
                ("ul", ["<strong>Fit:</strong> made-to-measure curtains are cut to your exact drop, so they skim the floor instead of hovering above it.",
                        "<strong>Light gaps:</strong> the width is planned with the track, so curtains overlap and stack beyond the window.",
                        "<strong>Fullness:</strong> tailored curtains use the right amount of fabric for the heading — wave curtains typically need around twice the track width.",
                        "<strong>Lining and fabric choice:</strong> choose any fabric from a catalogue and add a blackout lining behind it.",
                        "<strong>Hardware:</strong> the track or rod is sized and installed for the curtain weight."]),
                ("img", WAVE[0], "Made-to-measure wave curtains in a Dubai living room"),
            ]),
            ("Comparing value", [
                "Ready-made curtains cost less upfront. Made-to-measure curtains cost more, but include measurement, stitching to size and professional installation — and they usually last longer because the fabric and hardware are chosen for the window. Ask for a quotation that lists fabric, lining, stitching, track and installation separately so you can compare fairly.",
            ]),
            ("A practical middle ground", [
                f"If budget matters, consider made-to-measure for the rooms you use most — bedrooms and the living room — and simpler options elsewhere. {a(B + 'blackout-roller-blinds/', 'Blackout roller blinds')} are also a cost-effective tailored option for smaller windows.",
                ("img", PROJECTS[3], "Tailored curtains installed in a Dubai home"),
            ]),
        ],
        faq=[("Why do ready-made blackout curtains let light in?", "They often do not match the window width or drop, leaving gaps at the sides, top or bottom where light escapes."),
             ("Is made-to-measure worth it for floor-to-ceiling windows?", "Usually yes — tall and wide windows rarely match ready-made sizes, and tailored curtains are cut to the exact drop and width."),
             ("Do you offer a free measurement?", "Yes, we offer a free home visit and measurement across the UAE.")],
        topic="made-to-measure blackout curtains",
    ),
    # 4 ------------------------------------------------------------------
    dict(
        key="sheer-guide", slug="sheer-curtains-dubai-voile-chiffon-linen",
        title="Sheer Curtains in Dubai: Voile, Chiffon and Linen-Look Explained",
        cat="Buying Guides", query="sheer curtains dubai",
        excerpt="A guide to sheer curtains in Dubai — voile, chiffon and linen-look sheers, daytime privacy, glare control, colours and how to pair them with blackout.",
        featured="sheer-living",
        intro="“Sheer curtains Dubai”, “voile curtains” and “chiffon curtains” all broke out in local searches this season. Sheers soften Dubai's bright light without making rooms dark. Here is how the main types differ.",
        sections=[
            ("Voile curtains", [
                "Voile is a light, finely woven fabric with a smooth drape. It diffuses sunlight evenly and is the classic choice for layering behind heavier curtains.",
            ]),
            ("Chiffon curtains", [
                "Chiffon is very light and slightly more transparent, with a floaty, delicate look. It suits bedrooms and dressing areas where a soft, airy feel matters more than privacy.",
            ]),
            ("Linen-look and textured sheers", [
                "Linen-look sheers have a visible slub or texture that gives a relaxed, natural character. They are popular in modern Dubai apartments because they hide the view of neighbouring towers better than plain voile while keeping the room bright.",
                ("img", "sheer-living", "Linen-look sheer curtains in a minimalist living room"),
            ]),
            ("Privacy: day vs night", [
                "During the day, sheers make it harder to see inside while you can still see out. At night, with lights on inside, sheers become see-through — so bedrooms and ground-floor rooms usually need a second layer.",
                f"The answer is a double track: a sheer in front of the glass and a {a(P + 'blackout-curtains/', 'blackout curtain')} or dim-out curtain behind.",
            ]),
            ("Choosing colour and heading", [
                ("ul", ["<strong>White and ivory</strong> are the most requested — they keep rooms bright and neutral.",
                        "<strong>Warm beige or sand</strong> softens strong light and suits warmer interiors.",
                        f"<strong>{a(P + 'wave-curtains/', 'Wave headings')}</strong> give sheers even, flowing folds and are the most popular choice."]),
                ("img", "sheer-bedroom", "Light sheer curtains in a bedroom"),
            ]),
        ],
        faq=[("Do sheer curtains block heat?", "They reduce glare and some solar heat, but blackout or dim-out curtains block much more. Many homes use both on a double track."),
             ("What is the difference between voile and chiffon?", "Voile is a fine, smooth weave with even diffusion; chiffon is lighter and more transparent with a floaty drape."),
             ("Can sheer curtains be motorized?", "Yes — sheers on a wave track can be fitted with a motor, separately from a blackout layer.")],
        topic="sheer curtains",
    ),
    # 5 ------------------------------------------------------------------
    dict(
        key="cleaning", slug="how-to-clean-curtains-and-blinds-dubai",
        title="How to Clean Curtains and Blinds in Dubai: A Care Guide",
        cat="Care & Maintenance", query="curtains cleaning dubai",
        excerpt="Keep curtains and blinds fresh despite Dubai's dust — routine care, washing vs dry cleaning, steaming, and how to clean roller, wooden and aluminium blinds.",
        featured=PINCH[1],
        intro="“Curtains cleaning” became a breakout search in Dubai this season. Fine dust and sand settle quickly on fabric, so a simple routine keeps curtains and blinds looking new for longer. Always check the care label first — the fabric maker's instructions come before any general advice.",
        sections=[
            ("Weekly or fortnightly: remove the dust", [
                ("ul", ["Vacuum curtains gently with a soft brush attachment on low suction, working from top to bottom.",
                        "Give curtains a light shake when you open them to release surface dust.",
                        "Keep windows closed on dusty days — it makes a real difference in Dubai."]),
            ]),
            ("Washing vs dry cleaning", [
                ("ul", ["<strong>Unlined sheers and voiles:</strong> many can be washed on a gentle, cool cycle — check the label, remove hooks first, and wash in a laundry bag.",
                        "<strong>Lined, blackout, velvet and heavy curtains:</strong> professional dry cleaning is usually safer, because linings and fabrics can shrink at different rates.",
                        "<strong>Never tumble dry on high heat</strong> — rehang curtains slightly damp and let the weight pull out creases."]),
                ("img", PINCH[1], "Pinch pleat curtains after cleaning"),
            ]),
            ("Removing creases", [
                "A handheld steamer works well on hanging curtains. Keep the steamer moving and a short distance from the fabric, and test on a hidden area first.",
            ]),
            ("Cleaning blinds by type", [
                ("ul", [f"<strong>{a(B + 'blackout-roller-blinds/', 'Roller')} and {a(B + 'sunscreen-roller-blinds/', 'sunscreen')} blinds:</strong> dust with a soft brush; spot-clean with a damp cloth and mild soap.",
                        f"<strong>{a(B + 'wooden-blinds/', 'Wooden blinds')}:</strong> dust with a dry cloth or duster; avoid soaking the wood.",
                        f"<strong>{a(B + 'aluminium-venetian-blinds/', 'Aluminium blinds')}:</strong> wipe slats with a damp cloth — they handle moisture well.",
                        f"<strong>{a(B + 'vertical-blinds/', 'Vertical blinds')}:</strong> vacuum fabric slats gently; spot-clean marks.",
                        f"<strong>{a(B + 'zebra-blinds/', 'Zebra blinds')}:</strong> vacuum lightly and spot-clean; avoid soaking the fabric."]),
                ("img", WOODEN[0], "Wooden venetian blinds"),
            ]),
            ("Motorized curtains and blinds", [
                f"For {a(P + 'motorized-curtains/', 'motorized curtains')}, never wet the motor or track. Unhook the fabric from the carriers before washing, and keep water away from wiring and remotes.",
            ]),
        ],
        faq=[("How often should curtains be cleaned in Dubai?", "Light dusting every week or two, with a full wash or dry clean when they look dull — often once or twice a year depending on dust and fabric."),
             ("Can blackout curtains go in the washing machine?", "It depends on the fabric and lining. Many lined or coated blackout curtains are safer dry cleaned — follow the care label."),
             ("Do you offer curtain cleaning?", "We make and install new curtains and blinds. If your curtains are worn out, message us for a free home visit and quotation for replacements.")],
        topic="new curtains to replace my old ones",
    ),
    # 6 ------------------------------------------------------------------
    dict(
        key="curtains-vs-blinds", slug="curtains-vs-blinds-which-is-best",
        title="Curtains vs Blinds: Which Is Best for Your Dubai Home?",
        cat="Buying Guides", query="blinds and curtains dubai",
        excerpt="Curtains or blinds? Compare looks, light control, space, cleaning and cost room by room — plus when to combine both in Dubai homes.",
        featured=ZEBRA[0],
        intro="“Curtains and blinds” and “blinds and curtains” are two of the most searched phrases in Dubai. Both control light and privacy, but they suit different rooms. Here is a room-by-room way to decide.",
        sections=[
            ("Curtains: softness and full coverage", [
                ("ul", ["Soften hard surfaces and add warmth and texture",
                        "Cover full-height windows and sliding doors beautifully",
                        "Layer easily — sheer by day, blackout at night",
                        "Need space beside the window to stack when open"]),
                ("img", AMERICAN[0], "Full-length curtains in a Dubai living room"),
            ]),
            ("Blinds: precision and a slim footprint", [
                ("ul", ["Sit inside or just outside the window frame",
                        "Give precise light control — tilt slats or offset zebra bands",
                        "Suit kitchens, bathrooms, offices and small windows",
                        "Easy to wipe clean in dusty or humid spaces"]),
                ("img", ZEBRA[0], "Zebra blinds in a Dubai apartment"),
            ]),
            ("Room by room", [
                ("ul", [f"<strong>Living room:</strong> {a(P + 'wave-curtains/', 'wave curtains')} with sheers; add {a(P + 'motorized-curtains/', 'motorization')} for wide glass.",
                        f"<strong>Bedroom:</strong> {a(P + 'blackout-curtains/', 'blackout curtains')}, or a {a(B + 'blackout-roller-blinds/', 'blackout roller blind')} behind decorative curtains.",
                        f"<strong>Kitchen:</strong> {a(B + 'aluminium-venetian-blinds/', 'aluminium')} or {a(B + 'roman-blinds/', 'Roman blinds')} away from the hob.",
                        f"<strong>Office:</strong> {a(B + 'sunscreen-roller-blinds/', 'sunscreen roller blinds')} or {a(B + 'vertical-blinds/', 'vertical blinds')}.",
                        f"<strong>Kids room:</strong> {a(P + 'kids-curtains/', 'kids curtains')} with blackout lining, or {a(B + 'printed-blinds/', 'printed blinds')}."]),
            ]),
            ("Why not both?", [
                "Many Dubai homes combine a blind for daily light control with curtains for softness and style. It is also the best way to get full darkness in bedrooms.",
            ]),
        ],
        faq=[("Are blinds cheaper than curtains?", "It depends on size and materials. Blinds often use less fabric, but motorized or wooden blinds can cost more than simple curtains."),
             ("Which is better for heat in Dubai?", "Blackout curtains and blackout or sunscreen blinds all reduce solar heat. The best choice depends on the window size and how you use the room."),
             ("Can you install both?", "Yes — we regularly install blinds and curtains together on the same window.")],
        topic="curtains and blinds",
    ),
    # 7 ------------------------------------------------------------------
    dict(
        key="motorized-guide", slug="motorized-electric-curtains-guide",
        title="Motorized & Electric Curtains in Dubai: How They Work and What to Plan",
        cat="Buying Guides", query="electric curtains",
        excerpt="Motorized and electric curtains explained — tracks, motors, remote and smart control, power points, which headings work and planning tips for Dubai homes.",
        featured="motor-hall",
        intro="Searches for “electric curtains” rose sharply in Dubai this season, and “motorized curtains” broke out. Here is how motorized curtains work and what to plan before you install them.",
        sections=[
            ("How a motorized curtain works", [
                "A motorized curtain hangs on a special track with a motor at one end. The motor pulls a belt inside the track that moves the curtain carriers open or closed. From the room, it looks like a normal curtain track.",
                ("img", "motor-hall", "Motorized curtains in a modern living hall"),
            ]),
            ("Ways to control them", [
                ("ul", ["<strong>Remote control</strong> — the simplest option.",
                        "<strong>Wall switch</strong> — handy beside the bed or door.",
                        "<strong>App and smart-home control</strong> — many motors work with home-automation systems; compatibility depends on the motor, so share your system during the visit.",
                        "<strong>Schedules</strong> — close curtains automatically during strong afternoon sun."]),
            ]),
            ("Plan the power point early", [
                "Most curtain track motors need a power point near one end of the track. If you are renovating, plan it before painting or ceiling work. For finished homes we check the options during the home visit.",
            ]),
            ("Which curtains can be motorized?", [
                f"{a(P + 'wave-curtains/', 'Wave curtains')} and {a(P + 'pinch-pleat-curtains/', 'pinch pleat curtains')} work best on motorized tracks. {a(P + 'sheer-curtains/', 'Sheer')} and {a(P + 'blackout-curtains/', 'blackout')} layers can each have their own motor. Many blinds — roller, zebra and Roman — can be motorized too.",
                ("img", "motor-bedroom", "Motorized smart curtains in a bedroom"),
            ]),
            ("Where motorization makes most sense", [
                ("ul", ["Very wide or heavy curtains that are hard to pull by hand",
                        "Double-height windows you cannot reach",
                        f"{a(P + 'cinema-curtains/', 'Home cinemas')} and media rooms",
                        "Bedrooms, for opening curtains without getting up"]),
            ]),
        ],
        faq=[("Are motorized curtains noisy?", "Modern curtain motors are designed to run quietly; the sound level depends on the motor model."),
             ("Can I motorize my existing curtains?", "Sometimes. It depends on the heading and fabric weight. Send photos on WhatsApp and we will advise."),
             ("Do motorized curtains need wiring?", "Most curtain track motors need a nearby power point; we check this during the free home visit.")],
        topic="motorized curtains",
    ),
    # 8 ------------------------------------------------------------------
    dict(
        key="pinch-vs-wave", slug="pinch-pleat-vs-wave-curtains",
        title="Pinch Pleat vs Wave Curtains: Which Heading Should You Choose?",
        cat="Design Ideas", query="pinch pleat curtains",
        excerpt="Pinch pleat or wave? Compare the look, fullness, stacking, tracks and best rooms for each curtain heading to choose the right style for your home.",
        featured=PINCH[0],
        intro="“Pinch pleat curtains” was one of the fastest-rising curtain searches in Dubai this season. The heading — how the top of the curtain is made — changes the whole look. Here is how pinch pleat and wave compare.",
        sections=[
            ("The look", [
                f"<strong>{a(P + 'pinch-pleat-curtains/', 'Pinch pleat curtains')}</strong> gather fabric into tailored clusters at the top, giving a structured, formal drape. <strong>{a(P + 'wave-curtains/', 'Wave curtains')}</strong> form continuous, soft S-shaped folds from end to end for a clean, modern look.",
                ("img", PINCH[0], "Pinch pleat curtains in a formal room"),
                ("img", WAVE[1], "Wave curtains with soft S-shaped folds"),
            ]),
            ("Fullness and fabric", [
                "Both headings typically use around two to two-and-a-half times the track width in fabric. Pinch pleats handle heavier fabrics such as velvets and lined cloths very well; wave headings suit sheers and mid-weight fabrics beautifully.",
            ]),
            ("Stacking and space", [
                "Wave curtains stack neatly and compactly to the side, which suits wide glass in apartments. Pinch pleats stack a little wider but give a fuller, more traditional frame around the window.",
            ]),
            ("Tracks and hardware", [
                ("ul", ["<strong>Wave:</strong> a dedicated wave track with evenly spaced carriers keeps the folds uniform.",
                        "<strong>Pinch pleat:</strong> hangs from hooks on a track or rings on a decorative rod.",
                        f"Both can be {a(P + 'motorized-curtains/', 'motorized')}."]),
            ]),
            ("Which rooms suit which heading?", [
                ("ul", ["<strong>Choose wave</strong> for modern living rooms, bedrooms with floor-to-ceiling glass and sheer + blackout layering.",
                        "<strong>Choose pinch pleat</strong> for majlis, formal living and dining rooms, and classic villas."]),
            ]),
        ],
        faq=[("Which is more modern, pinch pleat or wave?", "Wave curtains have the more modern, minimal look; pinch pleats feel classic and tailored."),
             ("Can I use a rod with wave curtains?", "Wave curtains perform best on a wave track, which controls the spacing of each fold."),
             ("Which uses more fabric?", "Both typically use around two to two-and-a-half times the track width, depending on the fabric and look you want.")],
        topic="pinch pleat or wave curtains",
    ),
    # 9 ------------------------------------------------------------------
    dict(
        key="rods-tracks-hooks", slug="curtain-rods-tracks-and-hooks-guide",
        title="Curtain Rods, Tracks and Hooks: A Simple Guide",
        cat="Buying Guides", query="curtains rod",
        excerpt="Rod or track? Ceiling or wall? A clear guide to curtain rods, tracks, hooks, rings and brackets — and which hardware suits each curtain heading.",
        featured=INSTALL_PHOTO[0],
        intro="Searches for “curtains rod” and “curtains hooks” grew in Dubai this season. The hardware behind a curtain decides how it hangs, glides and lasts. Here is what you need to know.",
        sections=[
            ("Curtain rods (poles)", [
                f"A rod is a visible, decorative pole. It suits {a(P + 'eyelet-curtains/', 'eyelet curtains')}, ring-top pinch pleats and classic interiors. Finials at each end finish the look.",
            ]),
            ("Curtain tracks", [
                "A track is a slim rail — often hidden by the curtain — with gliders or carriers that the curtain hangs from. Tracks can be ceiling- or wall-mounted, bent around bay windows and fitted with motors.",
                ("img", INSTALL_PHOTO[0], "Ceiling curtain track installation"),
            ]),
            ("Ceiling vs wall mounting", [
                ("ul", ["<strong>Ceiling mounting</strong> gives floor-to-ceiling curtains, blocks light over the top and makes rooms feel taller — ideal for Dubai apartments.",
                        "<strong>Wall mounting</strong> suits standard windows with space above the frame; brackets typically sit 10–15 cm above the window."]),
            ]),
            ("Hooks, rings and carriers", [
                ("ul", ["<strong>Pin hooks</strong> — pushed into pinch pleat headings.",
                        "<strong>Glider hooks</strong> — hook into curtain tape and run in a track.",
                        "<strong>Wave carriers</strong> — snap into wave tape at fixed spacing for even folds.",
                        "<strong>Rings</strong> — slide on a rod; the curtain hangs from clips or hooks.",
                        "<strong>Eyelets</strong> — metal rings built into the curtain that thread straight onto the rod."]),
            ]),
            ("Tips for a long-lasting installation", [
                ("ul", ["Match bracket spacing to the curtain weight — heavy blackout curtains need more support.",
                        "Use proper wall or ceiling fixings for the surface (concrete, gypsum or wood).",
                        "Allow enough track beyond the window for the curtains to stack."]),
            ]),
        ],
        faq=[("Is a track or a rod better?", "Tracks are better for smooth gliding, ceiling mounting, bay windows and motorization; rods are a decorative choice for eyelet and ring-top curtains."),
             ("Can you install on a gypsum ceiling?", "Yes, with the right fixings or supports. We check the ceiling during the home visit."),
             ("Do you supply the hardware?", "Yes — we supply and install tracks, rods and brackets with your curtains.")],
        topic="curtain tracks and rods",
    ),
    # 10 -----------------------------------------------------------------
    dict(
        key="bedroom-ideas", slug="bedroom-curtain-ideas",
        title="Bedroom Curtain Ideas: Sleep Better and Style Your Room",
        cat="Design Ideas", query="bedroom curtains",
        excerpt="Bedroom curtain ideas for Dubai homes — blackout + sheer layering, colours, lengths, ceiling tracks, kids rooms and motorized bedside control.",
        featured=PROJECTS[1],
        intro="Bedroom curtains have two jobs: help you sleep and make the room feel calm. With Dubai's early sunrise and bright afternoons, getting both right matters. Here are the ideas that work best.",
        sections=[
            ("Layer blackout and sheer", [
                f"A double track with a {a(P + 'sheer-curtains/', 'sheer')} in front and a {a(P + 'blackout-curtains/', 'blackout curtain')} behind gives soft daylight, privacy and real darkness — all in one window.",
                ("img", PROJECTS[1], "Layered bedroom curtains installed in Dubai"),
            ]),
            ("Mount high and go full length", [
                "Ceiling-mounted tracks and floor-length curtains make ceilings look higher and block light escaping over the top. A floor-skimming finish looks tailored and is easy to keep clean.",
            ]),
            ("Calm colours", [
                ("ul", ["Soft greys, warm beiges and off-whites create a restful mood.",
                        "Deeper tones like navy, olive or charcoal feel cosy and hotel-like.",
                        "Blackout lining works behind any of them."]),
            ]),
            ("Kids rooms and nurseries", [
                f"{a(P + 'kids-curtains/', 'Kids curtains')} in playful prints can have a blackout lining for daytime naps. Cordless headings such as {a(P + 'eyelet-curtains/', 'eyelet')} and {a(P + 'wave-curtains/', 'wave')} keep things simple.",
                ("img", KIDS[1], "Kids room curtains"),
            ]),
            ("Control from the bed", [
                f"{a(P + 'motorized-curtains/', 'Motorized curtains')} let you open and close the curtains from a remote or bedside switch — and can be scheduled to open with your alarm.",
            ]),
        ],
        faq=[("What are the best curtains for sleeping?", "Blackout curtains on a ceiling track, extended beyond the window and finished just above the floor, give the darkest result."),
             ("Should bedroom curtains touch the floor?", "Floor-length curtains block more light and look more elegant than sill-length ones in most bedrooms."),
             ("Can I combine curtains with a blind?", "Yes — a blackout roller blind behind decorative curtains is a popular bedroom set-up.")],
        topic="bedroom curtains",
    ),
    # 11 -----------------------------------------------------------------
    dict(
        key="living-room-ideas", slug="living-room-curtain-ideas",
        title="Living Room Curtain Ideas for Dubai Apartments and Villas",
        cat="Design Ideas", query="curtains for living room",
        excerpt="Living room curtain ideas — white sheers, floor-to-ceiling wave curtains, colour matching, patterns and formal majlis styles for Dubai homes.",
        featured=WAVE[7],
        intro="The living room is where curtains make the biggest visual impact. In Dubai, it is also where the glass is widest and the sun strongest. These ideas balance light, privacy and style.",
        sections=[
            ("White and ivory sheers", [
                f"White curtains are among the most searched colours in Dubai. {a(P + 'sheer-curtains/', 'Sheer curtains')} in white or ivory keep the room bright, soften glare and make the space feel larger.",
                ("img", WAVE[7], "Wave curtains in a living room"),
            ]),
            ("Floor-to-ceiling wave curtains", [
                f"For apartments with full-height glass, {a(P + 'wave-curtains/', 'wave curtains')} on a ceiling track create a seamless, gallery-like wall of fabric. Add {a(P + 'motorized-curtains/', 'motorization')} for very wide windows.",
            ]),
            ("Match curtains to the room", [
                ("ul", ["Pick up a tone from the sofa, rug or cushions for a coordinated look.",
                        "Choose a shade slightly lighter or darker than the walls for subtle contrast.",
                        "Use texture — linen-look, velvet or bouclé-style fabrics — to add depth to plain colours."]),
            ]),
            ("Formal living rooms and majlis", [
                f"{a(P + 'pinch-pleat-curtains/', 'Pinch pleat')} and {a(P + 'american-style-curtains/', 'American style curtains')} in rich fabrics, with tie-backs and a sheer layer, suit formal living rooms and majlis.",
                ("img", AMERICAN[1], "American style curtains in a formal living room"),
            ]),
            ("Browse fabrics first", [
                f"Open our {a('/catalogue/', 'fabric catalogues')} to shortlist colours and textures, then we bring the samples to your free home visit.",
            ]),
        ],
        faq=[("What colour curtains are best for a living room?", "Whites, ivories and warm neutrals are the most popular because they keep rooms bright; deeper colours add drama in larger rooms."),
             ("Should living room curtains be sheer or blackout?", "Most living rooms use sheers for daytime, with a dim-out or blackout layer if the room gets strong afternoon sun or is used for TV."),
             ("How long should living room curtains be?", "Floor-length curtains, just skimming the floor, suit almost every living room.")],
        topic="living room curtains",
    ),
    # 12 -----------------------------------------------------------------
    dict(
        key="floral-patterned", slug="floral-and-patterned-curtains-ideas",
        title="Floral and Patterned Curtains: How to Use Them Well",
        cat="Design Ideas", query="floral curtains",
        excerpt="Floral curtains broke out in Dubai searches. How to choose pattern scale, colours and rooms for floral and patterned curtains, blinds and printed designs.",
        featured=ROMAN_C[0],
        intro="“Floral curtains” became a breakout search in Dubai this season. Pattern brings personality to a room — used well, it can become the focal point. Here is how to get it right.",
        sections=[
            ("Choose the right scale", [
                ("ul", ["<strong>Large florals</strong> suit big windows and rooms with plain walls and furniture.",
                        "<strong>Small, delicate prints</strong> work in bedrooms, kids rooms and smaller windows.",
                        "Keep other patterns in the room quieter so the curtains stand out."]),
                ("img", ROMAN_C[0], "Roman curtains in a patterned fabric"),
            ]),
            ("Pick colours from the room", [
                "Choose a floral that shares one or two colours with your cushions, rug or artwork. It ties the room together and makes the pattern feel intentional.",
            ]),
            ("Where patterns work best", [
                ("ul", [f"<strong>Bedrooms:</strong> soft florals with a blackout lining.",
                        f"<strong>Kids rooms:</strong> playful prints in {a(P + 'kids-curtains/', 'kids curtains')}.",
                        f"<strong>Kitchens and studies:</strong> patterned {a(B + 'roman-blinds/', 'Roman blinds')} or {a(P + 'roman-curtains/', 'Roman curtains')}."]),
            ]),
            ("Printed blinds: any pattern you like", [
                f"With {a(B + 'printed-blinds/', 'printed blinds')} you can put almost any design on a roller blind — florals, landscapes or your own artwork.",
                ("img", PRINTED[0], "Custom printed blind"),
            ]),
            ("Balance with sheers", [
                "Pair patterned curtains with a plain sheer on a double track. The sheer softens daylight while the pattern frames the window.",
            ]),
        ],
        faq=[("Are floral curtains still in style?", "Yes — floral and botanical patterns are popular again, especially in softer, muted colours."),
             ("Can patterned curtains have a blackout lining?", "Yes, a blackout lining can be added behind almost any patterned fabric."),
             ("Can I see patterned fabrics before buying?", "Yes — browse our catalogues online, then we bring samples to your free home visit.")],
        topic="floral and patterned curtains",
    ),
    # 13 -----------------------------------------------------------------
    dict(
        key="kitchen", slug="kitchen-curtains-and-blinds-ideas",
        title="Kitchen Curtains and Blinds: Practical Ideas That Last",
        cat="Design Ideas", query="kitchen curtains",
        excerpt="The best window treatments for kitchens — easy-clean blinds, Roman blinds away from the hob, sill-length curtains and tips for humidity and heat.",
        featured=ALU[2],
        intro="Kitchens need window treatments that handle steam, heat and daily cleaning. Here are practical kitchen curtain and blind ideas for Dubai homes.",
        sections=[
            ("Blinds are usually the best choice", [
                ("ul", [f"<strong>{a(B + 'aluminium-venetian-blinds/', 'Aluminium venetian blinds')}</strong> handle moisture and wipe clean easily.",
                        f"<strong>{a(B + 'blackout-roller-blinds/', 'Roller blinds')}</strong> sit flat and neat with washable fabric options.",
                        f"<strong>{a(B + 'zebra-blinds/', 'Zebra blinds')}</strong> give adjustable light in dining areas."]),
                ("img", ALU[2], "Aluminium venetian blinds"),
            ]),
            ("Roman blinds for warmth", [
                f"{a(B + 'roman-blinds/', 'Roman blinds')} bring fabric softness to kitchens — ideal for windows away from the hob and sink.",
                ("img", ROMAN_B[1], "Roman blinds in a kitchen"),
            ]),
            ("If you prefer curtains", [
                "Choose sill-length curtains in easy-care fabrics, keep them well clear of cooking areas, and pick a heading that is easy to take down for washing.",
            ]),
            ("Safety first", [
                "Keep all fabrics away from gas hobs and cooking appliances. For windows directly behind a hob, a wipe-clean blind is the safest choice.",
            ]),
        ],
        faq=[("What is the best blind for a kitchen?", "Aluminium venetian and roller blinds are popular because they tolerate humidity and are easy to clean."),
             ("Can I use curtains in a kitchen?", "Yes, on windows away from the hob and sink — sill-length curtains in washable fabric work best."),
             ("Do you install kitchen blinds?", "Yes — we measure, supply and install blinds for kitchens across the UAE.")],
        topic="kitchen blinds",
    ),
    # 14 -----------------------------------------------------------------
    dict(
        key="office", slug="office-curtains-and-blinds-dubai",
        title="Office Curtains and Blinds in Dubai: Glare, Privacy and Branding",
        cat="Commercial", query="office curtains",
        excerpt="Office curtains and blinds in Dubai — cut screen glare, add meeting room privacy and put your logo on the window with sunscreen and printed blinds.",
        featured=LOGO_BLINDS[0],
        intro="Dubai offices often have large glazed façades — great for views, hard on screens. The right curtains and blinds reduce glare and heat, add privacy and can even carry your brand.",
        sections=[
            ("Cut glare without losing the view", [
                f"{a(B + 'sunscreen-roller-blinds/', 'Sunscreen roller blinds')} filter sunlight through an open weave, reducing glare and heat while keeping the outside view.",
                ("img", SUNSCREEN[0], "Sunscreen roller blinds in an office"),
            ]),
            ("Privacy for meeting rooms", [
                f"For meeting and board rooms, {a(B + 'blackout-roller-blinds/', 'blackout roller blinds')} or {a(P + 'blackout-curtains/', 'blackout curtains')} darken the room for presentations. {a(B + 'vertical-blinds/', 'Vertical blinds')} are practical for wide windows and glass partitions.",
            ]),
            ("Put your brand on the glass", [
                f"{a(B + 'logo-sunscreen-blinds/', 'Logo sunscreen blinds')} print your logo onto the blind fabric — shade for your team and branding for the street.",
                ("img", LOGO_BLINDS[0], "Customised logo sunscreen blinds"),
            ]),
            ("Clinics and healthcare", [
                f"For clinics and medical centres, {a(P + 'hospital-curtains/', 'hospital cubicle curtains')} on ceiling tracks create private bays for patients.",
            ]),
            ("Planning an office project", [
                ("ul", ["Send floor plans or window sizes for an initial quotation.",
                        "Choose consistent colours and systems across floors or branches.",
                        "Plan installation outside working hours where possible."]),
            ]),
        ],
        faq=[("What blinds are best for offices?", "Sunscreen roller blinds are the most popular because they cut glare and heat while keeping the view."),
             ("Can you print our logo on blinds?", "Yes — we produce logo sunscreen and roller blinds. A vector logo file gives the best result."),
             ("Do you handle multiple floors or branches?", "Yes — send drawings or sizes and we will prepare a project quotation.")],
        topic="office blinds",
    ),
    # 15 -----------------------------------------------------------------
    dict(
        key="choose-shop", slug="how-to-choose-a-curtain-shop-in-dubai",
        title="How to Choose a Curtain Shop in Dubai: A Buyer's Checklist",
        cat="Buying Guides", query="curtains shop",
        excerpt="Looking for a curtain shop in Dubai or Abu Dhabi? Use this checklist — home visit, samples, itemised quotes, installation and after-sales questions to ask.",
        featured=PROJECTS[5],
        intro="“Curtains shop”, “curtains near me” and “custom curtains” are searched every day in Dubai. Whichever company you choose, these questions help you compare fairly and avoid surprises.",
        sections=[
            ("1. Do they measure at your home?", [
                "Accurate measurement is the foundation of good curtains. A home visit also lets the specialist see your light, ceiling type and power points for motorized systems.",
            ]),
            ("2. Can you see and touch the fabrics?", [
                f"Photos cannot show texture or how a fabric reacts to light. Ask to see samples in your room — and browse {a('/catalogue/', 'catalogues')} beforehand to shortlist.",
                ("img", PROJECTS[5], "Curtains installed in a Dubai home"),
            ]),
            ("3. Is the quotation itemised?", [
                ("ul", ["Fabric and lining (with fullness stated)",
                        "Stitching and heading type",
                        "Track, rod or motor",
                        "Installation"]),
                "An itemised quote makes it easy to compare companies like for like.",
            ]),
            ("4. Is installation included?", [
                "Check whether the same team supplies and installs the hardware and curtains, and whether ceilings or walls need special fixings.",
            ]),
            ("5. What happens after installation?", [
                "Ask how adjustments are handled if something needs fine-tuning after the curtains are hung.",
            ]),
            ("Where we are", [
                f"Our showroom is at Empire Plaza, Naif Road, Deira, and we offer a free home visit and measurement across the UAE — from {a('/areas-we-serve/curtains-blinds-dubai-marina/', 'Dubai Marina')} to {a('/areas-we-serve/curtains-blinds-abu-dhabi/', 'Abu Dhabi')}. We are part of {a('https://casaverahome.ae/', 'Casa Vera Home')}.",
            ]),
        ],
        faq=[("How long does it take to get custom curtains?", "It depends on the fabric, quantity and hardware. Ask for the timeline in writing with your quotation."),
             ("Should I choose a shop near me?", "A nearby showroom is convenient for seeing fabrics, but a company that visits your home and installs is what matters most."),
             ("Do you serve Abu Dhabi?", "Yes — we offer a free home visit and measurement across the UAE, including Abu Dhabi.")],
        topic="curtains for my home",
    ),
]


# ---------------------------------------------------------------------------
# Expansion: key takeaways, extra sections and extra FAQs per post, plus
# SEO titles trimmed to ≤ 60 characters.
# ---------------------------------------------------------------------------
TITLES = {
    "how-to-measure": "How to Measure for Curtains: Step-by-Step Guide",
    "sheer-guide": "Sheer Curtains in Dubai: Voile, Chiffon & Linen Guide",
    "motorized-guide": "Motorized & Electric Curtains in Dubai: Full Guide",
    "pinch-vs-wave": "Pinch Pleat vs Wave Curtains: Which Should You Choose?",
    "office": "Office Curtains & Blinds in Dubai: Glare and Privacy",
}

TAKEAWAYS = {
    "how-to-measure": ["Add 15–25 cm of track on each side of the window for stacking space.",
                       "Fabric is usually 2–2.5× the track width for wave and pinch pleat curtains.",
                       "Measure the drop in three places and use the shortest.",
                       "Floor-skimming curtains finish about 1 cm above the floor."],
    "blackout-guide": ["Blackout comes from triple-weave fabric, a coating, or a separate lining.",
                       "A ceiling track and wider coverage stop most light gaps.",
                       "Pale-backed blackout curtains reflect more sun in hot rooms.",
                       "A sheer + blackout double track is the most flexible bedroom set-up."],
    "readymade-vs-custom": ["Ready-made curtains suit standard windows and short-term rentals.",
                            "Tall and wide Dubai windows rarely match pack sizes.",
                            "Made-to-measure includes fit, fullness, lining choice and installation.",
                            "Compare itemised quotes, not just the headline price."],
    "sheer-guide": ["Voile is smooth and even; chiffon is lighter and floatier; linen-look adds texture.",
                    "Sheers give daytime privacy but not night-time privacy.",
                    "Wave headings suit sheers best.",
                    "Pair sheers with blackout on a double track for bedrooms."],
    "cleaning": ["Vacuum curtains every week or two to fight Dubai dust.",
                 "Follow the care label: many sheers wash; lined and blackout curtains usually dry clean.",
                 "Never tumble dry on high heat.",
                 "Keep water away from motors and tracks."],
    "curtains-vs-blinds": ["Curtains add softness and suit full-height glass.",
                           "Blinds suit kitchens, offices, bathrooms and small windows.",
                           "Bedrooms get the darkest result by combining both.",
                           "Choose by room, light, cleaning and space — not just looks."],
    "motorized-guide": ["A motor at one end of the track moves the curtain carriers.",
                        "Control by remote, wall switch, app or schedule.",
                        "Plan a power point near the track before renovation work.",
                        "Wave and pinch pleat curtains motorize best."],
    "pinch-vs-wave": ["Wave = modern, soft continuous folds; pinch pleat = tailored and formal.",
                      "Both typically use 2–2.5× the track width in fabric.",
                      "Wave curtains stack more compactly beside wide glass.",
                      "Both can be motorized."],
    "rods-tracks-hooks": ["Tracks glide better and suit ceilings, bays and motors.",
                          "Rods are decorative and suit eyelet and ring-top curtains.",
                          "Match hooks and carriers to the curtain heading.",
                          "Heavier blackout curtains need closer bracket spacing."],
    "bedroom-ideas": ["Layer sheer and blackout for light, privacy and sleep.",
                      "Ceiling tracks and floor-length curtains block the most light.",
                      "Calm neutrals and deep tones both work with blackout lining.",
                      "Motorized curtains add bedside convenience."],
    "living-room-ideas": ["White and ivory sheers keep living rooms bright.",
                          "Floor-to-ceiling wave curtains suit full-height glass.",
                          "Pick curtain colours from the sofa, rug or cushions.",
                          "Formal rooms and majlis suit pinch pleat or American style."],
    "floral-patterned": ["Match pattern scale to window and room size.",
                         "Choose a floral that shares colours with the room.",
                         "Keep other patterns quieter so the curtains lead.",
                         "Printed blinds can carry any design you like."],
    "kitchen": ["Wipe-clean blinds are the most practical kitchen choice.",
                "Roman blinds add softness away from the hob and sink.",
                "Keep all fabrics clear of cooking appliances.",
                "Choose easy-care fabrics that come down for washing."],
    "office": ["Sunscreen blinds cut glare but keep the view.",
               "Blackout blinds or curtains suit meeting rooms.",
               "Logo blinds brand shop fronts and receptions.",
               "Plan consistent systems across floors and branches."],
    "choose-shop": ["Insist on a home measurement visit.",
                    "See fabric samples in your own light.",
                    "Ask for an itemised quotation.",
                    "Confirm installation and after-sales in writing."],
}

EXTRA = {
    "how-to-measure": [
        ("Measuring floor-to-ceiling windows and sliding doors", [
            "Full-height glass is standard in many Dubai apartments. Here the curtain usually runs from a ceiling track to just above the floor, so the key measurement is <strong>ceiling to floor</strong>. Measure it at both ends and the centre, because ceilings and floors in new buildings can vary by several millimetres.",
            "For sliding doors, check which side the door opens and where you want the curtains to stack. Many people stack both curtains to the fixed-glass side so the door can open freely.",
            ("img", PROJECTS[11], "Floor-to-ceiling curtains on a sliding door"),
        ]),
        ("Bay, corner and arched windows", [
            ("ul", ["<strong>Bay windows:</strong> measure each section of the bay separately, plus the total run. A bendable track can follow the shape of the bay.",
                    "<strong>Corner windows:</strong> measure both walls to the corner and decide whether the curtains meet in the corner or stack at the ends.",
                    "<strong>Arched windows:</strong> measure the widest point and the height to the top of the arch — they are usually best dressed with a track mounted above the arch."]),
        ]),
        ("Before you order: a quick checklist", [
            ("ol", ["Window or glass width recorded",
                    "Extra width for stacking on each side added",
                    "Drop measured in three places — shortest used",
                    "Finish chosen: floor-skimming, puddled or sill length",
                    "Mount chosen: ceiling, wall or inside recess",
                    "Obstacles noted: AC units, sockets, handles, skirting"]),
            "If any of these feel uncertain, a professional measurement avoids costly re-makes — especially for motorized curtains, where the track length is fixed once installed.",
        ]),
    ],
    "blackout-guide": [
        ("Blackout curtains vs blackout blinds", [
            ("ul", ["<strong>Curtains</strong> cover the whole window wall, soften the room and can overlap to seal light at the edges.",
                    "<strong>Blinds</strong> sit close to the glass and suit smaller windows, kids rooms and tight spaces.",
                    "<strong>Both together</strong> give the darkest result — a blind inside the recess and curtains over it."]),
        ]),
        ("Which rooms need blackout?", [
            ("ul", ["<strong>Bedrooms</strong> — especially east-facing rooms that catch the early sunrise.",
                    "<strong>Kids rooms and nurseries</strong> — for daytime naps.",
                    "<strong>Media rooms and home cinemas</strong> — for screen contrast.",
                    "<strong>Home offices and meeting rooms</strong> — to stop glare on screens and presentations.",
                    "<strong>West-facing living rooms</strong> — for the strongest afternoon sun."]),
        ]),
        ("Looking after blackout curtains", [
            "Dust blackout curtains regularly with a soft brush attachment. Many lined or coated blackout curtains are best dry cleaned rather than machine washed, because the lining and face fabric can react differently to water and heat — always follow the care label.",
        ]),
    ],
    "readymade-vs-custom": [
        ("Questions to ask before you buy either", [
            ("ol", ["What is the exact width and drop of my window, including stacking space?",
                    "Will the curtains overlap in the centre and extend beyond the frame?",
                    "Is the blackout from the fabric, a coating or a lining?",
                    "Is the track or rod strong enough for the curtain weight?",
                    "Who installs it — and does the price include installation?"]),
        ]),
        ("Why fullness matters", [
            "Ready-made curtain panels are often sold at a fixed width that gives modest fullness once hung. Made-to-measure curtains are made with a planned fullness — usually around twice the track width for wave and pinch pleat headings — so they fold evenly and look full even when closed. That extra fabric also helps block light at the folds.",
        ]),
        ("Sizing for common Dubai windows", [
            "Apartment windows in Dubai are frequently taller than the standard drops sold in stores, especially in newer towers with full-height glazing. If a ready-made curtain ends several centimetres above the floor, it tends to look unfinished and lets light in underneath — the most common reason people switch to made-to-measure.",
            f"For those windows, {a(P + 'wave-curtains/', 'wave curtains')} on a ceiling track or {a(P + 'blackout-curtains/', 'tailored blackout curtains')} are the usual solution.",
        ]),
    ],
    "sheer-guide": [
        ("Sheers for different rooms", [
            ("ul", ["<strong>Living rooms:</strong> full-height white or ivory sheers on a wave track for a bright, airy feel.",
                    "<strong>Bedrooms:</strong> sheers in front of a blackout layer for daytime privacy and night-time darkness.",
                    "<strong>Balcony doors:</strong> sheers soften the view and glare while letting you see out.",
                    "<strong>Home offices:</strong> sheers reduce glare on screens without blocking daylight."]),
        ]),
        ("How much fabric do sheers need?", [
            "Sheers look best with generous fullness. On a wave track, around two to two-and-a-half times the track width gives even, flowing folds. Too little fabric makes sheers look flat and see-through when closed.",
        ]),
        ("Caring for sheer curtains", [
            "Many unlined voiles and sheers can be washed on a gentle, cool cycle in a laundry bag — always check the care label first. Rehang them slightly damp so the weight pulls out the creases, and dust them regularly because fine Dubai dust shows quickly on light fabrics.",
            f"See our full {a('/blog/how-to-clean-curtains-and-blinds-dubai/', 'curtain cleaning guide')} for more care tips.",
        ]),
    ],
    "cleaning": [
        ("A simple care calendar", [
            ("ul", ["<strong>Weekly:</strong> open curtains fully and shake gently; dust blinds.",
                    "<strong>Every two to four weeks:</strong> vacuum curtains with a soft brush attachment.",
                    "<strong>Every few months:</strong> spot-clean marks with a damp cloth and mild soap (test first).",
                    "<strong>Once or twice a year:</strong> wash or dry clean according to the care label."]),
        ]),
        ("Spot-cleaning marks and stains", [
            "Blot rather than rub, work from the outside of the mark inwards, and test any cleaner on a hidden corner first. For coated blackout fabrics, avoid harsh solvents that can damage the coating.",
        ]),
        ("Taking curtains down safely", [
            ("ol", ["Close the curtains so they are fully spread along the track.",
                    "Remove hooks or unclip carriers one by one, supporting the fabric's weight.",
                    "Label each curtain with its window if you have several.",
                    "Check the track while the curtains are down — wipe it and make sure gliders run smoothly."]),
        ]),
        ("When cleaning is not enough", [
            f"Sun-faded, frayed or shrunken curtains are usually due for replacement. When that time comes, {a(P + 'blackout-curtains/', 'blackout')} or lined curtains resist fading better in sunny Dubai rooms, and {a(B + 'aluminium-venetian-blinds/', 'aluminium blinds')} are the easiest to keep clean in kitchens and bathrooms.",
        ]),
    ],
    "curtains-vs-blinds": [
        ("Light control compared", [
            ("ul", ["<strong>Curtains:</strong> open or closed, with sheers for in-between.",
                    "<strong>Venetian blinds:</strong> tilt slats to direct light up, down or block it.",
                    "<strong>Zebra blinds:</strong> offset bands to dial privacy and light.",
                    "<strong>Sunscreen blinds:</strong> cut glare while keeping the view.",
                    "<strong>Blackout blinds and curtains:</strong> for full darkness."]),
        ]),
        ("Cleaning and maintenance", [
            "Blinds are generally quicker to clean — a wipe or a dust — which is why they are popular in kitchens, bathrooms and offices. Curtains need occasional washing or dry cleaning but can be taken down and refreshed completely.",
        ]),
        ("Space and furniture", [
            "Curtains need space beside the window to stack and hang in front of the wall, so check for sofas, beds or wardrobes close to the window. Blinds sit within or just outside the frame and leave the wall space free.",
        ]),
        ("Style and atmosphere", [
            "Curtains bring softness, colour and movement — they can make a room feel warm, formal or luxurious. Blinds give a cleaner, more architectural look. Many designers use blinds for function and curtains for atmosphere in the same room.",
        ]),
    ],
    "motorized-guide": [
        ("Motorized blinds", [
            f"Roller, {a(B + 'zebra-blinds/', 'zebra')} and {a(B + 'roman-blinds/', 'Roman blinds')} can also be motorized. Some blind motors are mains-powered and others use rechargeable batteries, which can be useful where wiring is difficult — we advise on the options during the visit.",
        ]),
        ("Why motorization suits Dubai homes", [
            ("ul", ["Close curtains automatically before the strongest afternoon sun to keep rooms cooler.",
                    "Open curtains in the morning on a schedule.",
                    "Operate heavy blackout curtains on very wide glass with one button.",
                    "Reduce wear on fabric from pulling by hand."]),
        ]),
        ("What happens at the home visit", [
            ("ol", ["We measure the window and check the ceiling or wall for track fixing.",
                    "We locate the nearest power point, or advise where one should be added.",
                    "We discuss control: remote, wall switch or smart-home integration.",
                    "You choose fabrics for each layer — sheer, blackout or both."]),
        ]),
    ],
    "pinch-vs-wave": [
        ("Fabrics that suit each heading", [
            ("ul", ["<strong>Wave:</strong> sheers, voiles, linen-look and mid-weight fabrics — they fall into soft, even curves.",
                    "<strong>Pinch pleat:</strong> velvets, jacquards, lined and interlined fabrics — the pleats support heavier cloth."]),
        ]),
        ("Pinch pleat styles", [
            "Pinch pleats come in double and triple pleat variations. Triple pleats create a fuller, more formal heading; double pleats are a little lighter and more relaxed. Both can be made with blackout linings.",
            ("img", PINCH[2], "Triple pinch pleat curtain heading"),
        ]),
        ("Cost and value", [
            "Because both headings typically use similar amounts of fabric, the biggest price differences usually come from the fabric choice, lining and hardware rather than the heading itself. A wave track and a decorative rod, for example, are priced differently — ask for these to be itemised in your quotation.",
        ]),
    ],
    "rods-tracks-hooks": [
        ("Choosing hardware by curtain type", [
            ("ul", [f"<strong>{a(P + 'wave-curtains/', 'Wave curtains')}:</strong> wave track with carriers.",
                    f"<strong>{a(P + 'pinch-pleat-curtains/', 'Pinch pleat')}:</strong> track with glider hooks, or rod with rings.",
                    f"<strong>{a(P + 'eyelet-curtains/', 'Eyelet')}:</strong> rod only.",
                    f"<strong>{a(P + 'motorized-curtains/', 'Motorized')}:</strong> motorized track.",
                    f"<strong>{a(P + 'hospital-curtains/', 'Hospital cubicles')}:</strong> ceiling-mounted cubicle track."]),
        ]),
        ("Double tracks and double rods", [
            "To layer a sheer and a blackout curtain, use a double track (two tracks side by side) or a double rod bracket. The sheer usually hangs closest to the glass so it can stay closed during the day while the outer curtain opens.",
        ]),
        ("Signs your track needs replacing", [
            ("ul", ["Curtains drag, jump or stick when opened",
                    "Brackets are loose or the track sags in the middle",
                    "Gliders are broken or missing",
                    "You want to add motorization"]),
        ]),
    ],
    "bedroom-ideas": [
        ("Best curtain styles for bedrooms", [
            ("ul", [f"<strong>{a(P + 'wave-curtains/', 'Wave curtains')}</strong> — calm, modern folds on a ceiling track.",
                    f"<strong>{a(P + 'pinch-pleat-curtains/', 'Pinch pleat')}</strong> — tailored and hotel-like in master bedrooms.",
                    f"<strong>{a(P + 'roman-curtains/', 'Roman curtains')}</strong> — neat for small windows beside the bed."]),
        ]),
        ("Guest rooms and small bedrooms", [
            "In smaller rooms, curtains in the same colour as the walls make the space feel larger. A blackout roller blind with a light sheer curtain keeps the look airy while guaranteeing darkness for guests.",
        ]),
        ("Mistakes to avoid", [
            ("ul", ["Curtains that stop above the window, letting light over the top",
                    "Too little fullness, which leaves gaps when closed",
                    "Sill-length curtains on tall windows",
                    "Heavy dark curtains in a small room without a lighter layer"]),
        ]),
    ],
    "living-room-ideas": [
        ("Solving glare on the TV", [
            "If the TV faces a window, a sheer takes the edge off daylight, while a dim-out or blackout layer on a second track handles the brightest afternoon hours. Motorized tracks make it easy to switch between them.",
        ]),
        ("Open-plan living and dining", [
            "In open-plan spaces, use the same fabric and heading across every window so the room reads as one. Where kitchen windows share the space, a matching fabric on Roman blinds keeps the look consistent.",
            ("img", PROJECTS[9], "Living room curtains installed in Dubai"),
        ]),
        ("Budget-friendly upgrades", [
            ("ul", ["Keep existing blinds and add sheer curtains for softness.",
                    "Change the track to a ceiling-mounted one to make the room feel taller.",
                    "Replace only the main window's curtains and use blinds elsewhere."]),
        ]),
    ],
    "floral-patterned": [
        ("Geometric and striped alternatives", [
            "If florals feel too busy, geometric prints and stripes add pattern with a more tailored look. Vertical stripes can make ceilings feel higher; horizontal stripes on Roman blinds can make narrow windows feel wider.",
        ]),
        ("Pattern and light", [
            "Sunlight shining through an unlined patterned curtain will show the pattern from both sides and can fade bright colours over time. A lining protects the fabric and keeps the pattern crisp — especially on sun-facing windows.",
        ]),
        ("Mixing patterns confidently", [
            ("ul", ["Keep one large pattern and pair it with smaller, simpler ones.",
                    "Repeat one colour across all the patterns in the room.",
                    "Use plain sheers or blinds to give the eye a rest."]),
        ]),
    ],
    "kitchen": [
        ("Light and privacy in kitchens", [
            "Kitchens often need daylight for cooking but privacy from neighbours. Light-filtering roller fabrics or tilted venetian slats let light in while blocking direct views.",
        ]),
        ("Dining areas next to the kitchen", [
            f"Where the dining area shares the kitchen, {a(B + 'zebra-blinds/', 'zebra blinds')} or {a(B + 'roman-blinds/', 'Roman blinds')} bridge the practical kitchen and the softer living space.",
        ]),
        ("Cleaning kitchen blinds", [
            f"Wipe aluminium slats with a damp cloth and mild detergent; dust roller and Roman blinds regularly. See our {a('/blog/how-to-clean-curtains-and-blinds-dubai/', 'cleaning guide')} for more tips.",
        ]),
    ],
    "office": [
        ("Reception areas", [
            "Receptions set the first impression. Consider matching blinds to your brand colours, or logo sunscreen blinds facing the street, with softer curtains in waiting areas.",
        ]),
        ("Heat and energy", [
            "Sunscreen and blackout blinds reduce direct sun on glass, helping rooms stay more comfortable in Dubai's hottest months. Choosing the right fabric openness depends on how much glare control versus view you need — samples help you compare on site.",
        ]),
        ("Easy maintenance for busy offices", [
            "Roller and vertical blinds are quick to dust and spot-clean, which suits high-traffic spaces. Motorized blinds on large façades reduce wear from manual use.",
            ("img", VERTICAL[0], "Vertical blinds in an office"),
        ]),
    ],
    "choose-shop": [
        ("6. Can they handle every window?", [
            "Many homes need a mix: curtains for living rooms, blackout for bedrooms, blinds for kitchens and bathrooms. A company that covers curtains, blinds and motorization can plan everything consistently and install it in one visit.",
        ]),
        ("7. Are fabrics and systems explained clearly?", [
            ("ul", ["Blackout from fabric, coating or lining?",
                    "What fullness is included?",
                    "Which heading and track?",
                    "Is the fabric suitable for sun-facing windows?"]),
        ]),
        ("Red flags to watch for", [
            ("ul", ["No home measurement offered for complex windows",
                    "Quotes that list only a single total price",
                    "Pressure to decide before seeing samples",
                    "Installation or hardware charged as vague extras"]),
        ]),
    ],
}

EXTRA_FAQ = {
    "how-to-measure": [("Do I measure the window or the track?", "Measure the window first, then add stacking space to get the track width. If a track is already installed and correctly placed, measure the track itself.")],
    "blackout-guide": [("Which blackout is better: lining or triple-weave?", "Both work well. A lining lets you choose almost any decorative fabric; triple-weave fabrics are lighter and softer with blackout built in.")],
    "readymade-vs-custom": [("Can ready-made curtains be altered to fit?", "Hemming shorter is possible, but adding width or length is not — which is why tall and wide windows usually need made-to-measure.")],
    "sheer-guide": [("What colour sheer is best?", "White and ivory are the most popular for brightness; warmer beige tones soften strong light and suit warm interiors.")],
    "cleaning": [("Can I steam curtains while they hang?", "Yes, most fabrics can be steamed hanging — keep the steamer moving and test a hidden area first.")],
    "curtains-vs-blinds": [("What works best for floor-to-ceiling windows?", "Curtains on a ceiling track usually suit full-height glass best; blinds work too but large sizes may need several panels.")],
    "motorized-guide": [("Can motorized curtains work on a timer?", "Yes — many systems support schedules, so curtains open and close at set times.")],
    "pinch-vs-wave": [("Which heading is easier to maintain?", "Both are easy day to day. Wave curtains unclip quickly from their carriers; pinch pleats unhook from the track.")],
    "rods-tracks-hooks": [("How far apart should brackets be?", "It depends on the track and curtain weight; heavier blackout curtains need brackets closer together. We plan this at installation.")],
    "bedroom-ideas": [("What colour curtains help you sleep?", "Colour matters less than blackout performance — any colour with a blackout lining can darken the room. Calm neutrals create a restful mood.")],
    "living-room-ideas": [("Can I mix curtains and blinds in a living room?", "Yes — blinds on smaller side windows with curtains on the main window is a common and practical combination.")],
    "floral-patterned": [("Do floral curtains suit modern interiors?", "Yes — large-scale, muted florals and botanical prints sit well in modern rooms when the rest of the scheme is simple.")],
    "kitchen": [("Are Roman blinds suitable for kitchens?", "Yes, on windows away from the hob and sink. Choose an easy-care fabric.")],
    "office": [("Which blinds work for glass partitions?", "Vertical and roller blinds are common choices for glass partitions and meeting rooms.")],
    "choose-shop": [("What should a curtain quotation include?", "Fabric and lining, fullness, heading and stitching, track or rod, any motor, and installation — each listed separately.")],
}

for _post in POSTS:
    k = _post["key"]
    if k in TITLES:
        _post["title"] = TITLES[k]
    _post["takeaways"] = TAKEAWAYS[k]
    _post["sections"] = _post["sections"][:-1] + EXTRA.get(k, []) + _post["sections"][-1:]
    _post["faq"] = _post["faq"] + EXTRA_FAQ.get(k, [])


# ---------------------------------------------------------------------------
# Second expansion — deeper sections for shorter posts.
# ---------------------------------------------------------------------------
MORE = {
    "blackout-guide": [
        ("Blackout options at a glance", [
            ("ul", [f"<strong>Blackout-lined curtains</strong> — any decorative fabric with full darkness. See {a(P + 'blackout-curtains/', 'blackout curtains')}.",
                    f"<strong>Blackout {a(P + 'wave-curtains/', 'wave curtains')}</strong> — modern folds on a ceiling track.",
                    f"<strong>Blackout {a(P + 'roman-curtains/', 'Roman curtains')}</strong> — neat for small bedroom windows.",
                    f"<strong>{a(B + 'blackout-roller-blinds/', 'Blackout roller blinds')}</strong> — slim, simple and cost-effective.",
                    f"<strong>{a(P + 'motorized-curtains/', 'Motorized blackout')}</strong> — for wide glass and bedside control."]),
            ("img", BLACKOUT_ROLLER[0], "Blackout roller blind"),
        ]),
    ],
    "readymade-vs-custom": [
        ("What installation adds", [
            "A made-to-measure order normally includes fitting the track or rod at the right height and width, hanging the curtains, and adjusting the hooks so the hem sits evenly. On gypsum ceilings or concrete walls, the right fixings make the difference between a track that stays straight for years and one that sags.",
            ("img", WAVE_INSTALL[1], "Wave curtains after professional installation"),
        ]),
    ],
    "sheer-guide": [
        ("Sheers and Dubai's climate", [
            "Strong year-round sun can fade and weaken very fine fabrics over time on south- and west-facing windows. Choosing a slightly heavier sheer or linen-look weave for those windows — and pairing it with a blackout or dim-out layer that takes the harshest afternoon sun — helps sheers last longer.",
        ]),
        ("Where to see sheer fabrics", [
            f"Browse our {a('/catalogue/', 'curtain fabric catalogues')} to shortlist sheers, then we bring samples to your free home visit so you can see how each one filters light in your own room.",
        ]),
    ],
    "curtains-vs-blinds": [
        ("Cost considerations", [
            "Prices depend on the window size, the fabric or material and the operating system. Simple roller blinds are often the most economical; wooden blinds, motorized systems and full-height lined curtains cost more. The best value usually comes from matching each room's needs rather than using one product everywhere.",
            ("img", PROJECTS[6], "Curtains and blinds in a Dubai home"),
        ]),
    ],
    "motorized-guide": [
        ("Common questions about reliability", [
            "Motorized tracks are designed for daily use. Most problems come from installation — a track that is not level, or fabric that is too heavy for the motor — which is why sizing the motor and track to the curtain weight matters. Keep the remote's batteries fresh and avoid forcing a motorized curtain by hand unless the system is designed for it.",
        ]),
        ("Motorized curtains for home cinemas", [
            f"In media rooms, {a(P + 'cinema-curtains/', 'cinema curtains')} on a motorized track can open and close from the same remote as your screen system, depending on compatibility.",
        ]),
    ],
    "pinch-vs-wave": [
        ("How to decide in one minute", [
            ("ol", ["Is your interior modern and minimal? Choose wave.",
                    "Is it classic, formal or a majlis? Choose pinch pleat.",
                    "Do you need sheers and blackout on a double track? Wave is the easiest.",
                    "Do you love heavy velvets or jacquards? Pinch pleat shows them best."]),
            ("img", WAVE_INSTALL[0], "Wave curtain installation in a Dubai home"),
        ]),
    ],
    "rods-tracks-hooks": [
        ("Rod finishes and styles", [
            "Rods come in finishes such as matt black, brushed metal, brass-tone and wood, with finials from simple end caps to decorative shapes. Match the finish to door handles, light fittings or furniture legs for a coordinated room.",
            ("img", EYELET[0], "Eyelet curtains on a curtain rod"),
        ]),
    ],
    "bedroom-ideas": [
        ("Curtains and blinds together", [
            f"For the darkest bedrooms, fit a {a(B + 'blackout-roller-blinds/', 'blackout roller blind')} inside the window recess and hang decorative curtains in front. The blind handles darkness; the curtains add softness and style.",
            ("img", PINCH[3], "Bedroom curtains in a soft neutral fabric"),
        ]),
    ],
    "living-room-ideas": [
        ("Length and fullness", [
            "Floor-skimming curtains suit most living rooms; a slight puddle of 5–10 cm adds drama in formal spaces. Generous fullness — about twice the track width for wave curtains — keeps curtains looking rich even when closed.",
        ]),
    ],
    "floral-patterned": [
        ("Florals in Roman curtains and blinds", [
            f"If full-length patterned curtains feel like too much, try florals on {a(P + 'roman-curtains/', 'Roman curtains')} or {a(B + 'roman-blinds/', 'Roman blinds')}. The pattern shows flat when lowered and folds neatly when raised.",
            ("img", ROMAN_C[2], "Roman curtain in a room"),
        ]),
    ],
    "kitchen": [
        ("Materials compared for kitchens", [
            ("ul", ["<strong>Aluminium:</strong> moisture-tolerant, wipe-clean, slim.",
                    "<strong>Roller fabric:</strong> neat and simple; choose easy-clean fabrics.",
                    "<strong>Wood:</strong> warm look, but keep away from steam and splashes.",
                    "<strong>Fabric curtains:</strong> soft look; best on windows away from cooking."]),
            ("img", ZEBRA[1], "Zebra blinds near a dining area"),
        ]),
    ],
    "office": [
        ("Choosing sunscreen fabric openness", [
            "Sunscreen fabrics are made with different openness levels — the percentage of the weave that is open. A more open weave keeps more view; a tighter weave blocks more glare and heat. Seeing samples against your own windows is the best way to choose.",
        ]),
    ],
    "choose-shop": [
        ("Showroom or home visit?", [
            "A showroom lets you see many fabrics at once; a home visit lets you judge them in your own light and have every window measured. The best experience combines both — browse online or in the showroom, then confirm choices at home.",
            ("img", PROJECTS[13], "Finished curtains in a Dubai apartment"),
        ]),
    ],
    "cleaning": [],
}

for _post in POSTS:
    extra = MORE.get(_post["key"], [])
    if extra:
        _post["sections"] = _post["sections"][:-1] + extra + _post["sections"][-1:]
