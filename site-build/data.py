"""Site data for dubaicurtainexperts.ae — contacts, Drive assets, page copy.

Every fact here is either supplied by the owner (phone, email, address, hours,
parent company) or taken from the owner's Google Drive folder structure
(product categories, photos, catalogues). No invented statistics or prices.
"""

BRAND = "Dubai Curtain Experts"
PHONE = "+971 50 859 9803"
TEL = "+971508599803"
WA = "971508599803"
EMAIL = "info@dubaicurtainexperts.ae"
ADDRESS = "Empire Plaza Shopping Center, Shop 49, Naif Road, Deira, Dubai, UAE"
HOURS = "Monday – Saturday, 8:00 AM – 5:30 PM (Sunday closed)"
PARENT = "Casa Vera Home"
PARENT_LEGAL = "Mukhtar Curtain LLC"
PARENT_URL = "https://casaverahome.ae/"
MAP_URL = "https://maps.google.com/?q=Empire+Plaza+Naif+Road+Deira+Dubai"

# --------------------------------------------------------------------------
# Drive images to import: drive_id -> (seo file name, alt text)
# Grouped by the owner's Drive folder, which is the product category.
# --------------------------------------------------------------------------
IMAGES = {}


def reg(folder_slug, alt, ids):
    """Register images for a folder; returns the list of ids in order."""
    out = []
    for n, (did, ext) in enumerate(ids, 1):
        IMAGES[did] = (f"{folder_slug}-{n}.{ext}", alt)
        out.append(did)
    return out


WAVE = reg("wave-curtains-dubai", "Wave curtains made to measure in Dubai", [
    ("1koffrOvbgJNN1Y6jqjJxq4Z9Amu55enB", "webp"),
    ("1xJ4S8-Dtr5FrR_TTuHjAfCGnJ_l_zoZg", "webp"),
    ("1gpIW9Ihs43rp0lH0OXzjLls1GJuXWTGk", "jpeg"),
    ("1iy8iSBl-MfSbnR6VoADS6WBU-KEGxgok", "webp"),
    ("1ezCDaX9gehuCwiDs0ze0Ar40oGs1K9PD", "webp"),
    ("1gJ8CSH3IRjB3HrnaGHKcTVq3ngvCfo80", "jpeg"),
    ("1oMQLkF2u5KIBgV3g2qP28eLMCv2DEGF5", "webp"),
    ("1h5ysdMsBsIUhHbxvhN0ozKyHPp06huXV", "webp"),
    ("1DfVIei2WmitdX-r5X40GOLfRleDVKdzB", "webp"),
    ("1QQMy2LUhHxYn5K-KIjOErSpS5ZLtVhNl", "webp"),
])
WAVE_INSTALL = reg("wave-curtain-installation-dubai", "Wave curtain installation by Dubai Curtain Experts", [
    ("1Y71d7q96OCbe5TQaC569y3AbUzX3lUDY", "jpeg"),
    ("1VjlYgwTJ7CusfgofTt2fiZqFQBnJFTah", "jpeg"),
    ("1HdaHVd2o5kUln0z1BnEYFBStEm83NhLv", "jpeg"),
    ("1lbzzVFvExgVuge50oRfO-dDQGbN8x9eo", "jpeg"),
    ("16V-en-DMWX--KWqOWwg5ZscJ0u95OEAQ", "jpeg"),
])
INSTALL_PHOTO = reg("curtain-installation-process", "Curtain track installation in a Dubai home", [
    ("1knaWCxyVwSPI6SM02yoHCPda1a-HLWjY", "webp"),
])
PINCH = reg("pinch-pleat-curtains-dubai", "Pinch pleat curtains made to measure in Dubai", [
    ("1aRYltCopd0LtM3nkVif70ktqj00AxKfi", "webp"),
    ("1fhTU59kAJCJTRt9hkbeqJYs4zvCvcE88", "webp"),
    ("1AJCmqm_d3ZdbWVsez2xepurjz7BAk0B0", "webp"),
    ("1nlfwu8IOtupbehR4UCSp2A1mL4rOdL04", "webp"),
    ("1hBrPYFzKaKc16JoQDMheeekotBD7mNyv", "jpg"),
    ("1BmMoU8ByxcWuq8RtIdPjceJFA5qfEdlG", "webp"),
    ("1bIXZSNkrnjM3sYUEz02kVBq5fJ27dqo0", "webp"),
])
EYELET = reg("eyelet-curtains-dubai", "Eyelet curtains made to measure in Dubai", [
    ("1WpTv6w4qn-voHgGC4geeluMTvEHAquG7", "webp"),
    ("1K1MGb8sVP2syweYfCw28XsOx6B30efuX", "webp"),
    ("10-7_Mc9kKRvO1XfwaPtPS6rrs1DR1_gi", "webp"),
    ("1lyvokvXvLQ6oa6wulUgL9VAnIZ_FKDXC", "webp"),
    ("1VQByKFBTOwtGU1s3cGbByt-bg73BU-gr", "webp"),
    ("1KFfkZvQ5hPPk_S1e2WeOueFMWBh2H2AF", "webp"),
    ("1r4tHn-ABcFUULV1lFy8gzOnw5dXuWYij", "webp"),
    ("1xuByTjyeVOBeL5lFSTGNsT26vL3MmK_V", "webp"),
])
AMERICAN = reg("american-style-curtains-dubai", "American style curtains in Dubai", [
    ("1nW9YeUwmiR2nfnETMdAiGgENB6akPH2P", "webp"),
    ("1ftnKQVm6Kp_u1LxWer4bc2Mvz-wFL_2y", "webp"),
    ("1_CUkq9-6N_4ktRSJolAgWd_FkBZPOAbf", "webp"),
    ("1OOvFpY3CVqKX2RolbyROm2B1QHtAWwlg", "webp"),
    ("1x0Q8_t_9dxP9PQAj7K91wZBMFUaIEol4", "jpg"),
    ("1E-QSgZ5LtSItYg9FJXI_mgiOVzDxSPb_", "jpg"),
])
ROMAN_C = reg("roman-curtains-dubai", "Roman style curtains made to measure in Dubai", [
    ("1zHS2qPcwEF-8KjfYg8X4YHHyLoddmSrE", "webp"),
    ("1nURwVmqq5CO36Yky9MsTKVcj40zPGWky", "webp"),
    ("15m5yhS-1myCJpGyw1qzLXVraSFwBQFWk", "webp"),
    ("1frirpdMbuMAHurMAO3zltf-RHu3_Titw", "webp"),
    ("1KMP27NbAP8o8D_5wLYh3sI9EuV8nKa3v", "webp"),
    ("1IzVmdp4Ew4mGKZ0GYpZF9QCQOAOJ3YFr", "jpg"),
    ("1ueunTMXVXISFaPEQHiNkMen05Myf_aLh", "jpg"),
    ("1gHckDjxXsype2Uihfr1vnrWWx3dbLucA", "jpg"),
    ("1D3xmhA-ayB1lG3SeR6-ZHTa_0EChifJz", "jpg"),
    ("1ISN-qcSIlIZ3UZ1jmO4DaH3uIaf1MoPt", "jpg"),
])
BEADED = reg("beaded-curtains-dubai", "Beaded curtains in Dubai", [
    ("1AQtqwW8nMnYi6AjH1IV42HZc1XokD0c8", "webp"),
    ("1iTKI72-7UuBBtAPZAI4HEPX1QvaD_vhu", "jpg"),
    ("1zCQs5rzuHn8SHCsQFhHeE0QOI0CqMA4w", "jpg"),
    ("1xNTEXmItR_PKgfR-10TwXcp0dutFiCd0", "jpg"),
    ("1E0nA6oD-ESqb9dZ04urFdAWaOKNgGRO1", "jpg"),
])
CINEMA = reg("cinema-curtains-dubai", "Cinema and home theatre curtains in Dubai", [
    ("1x5UGj6rW36uYEaLF8V2YqSsxjfvWLsbB", "jpg"),
    ("18PX98YFxVGk16QGzPp0MaJB4NnNZ4kNS", "jpg"),
])
KIDS = reg("kids-curtains-dubai", "Kids room curtains in Dubai", [
    ("1l4OwbWTqN50t4lvGvqfvrgc9Z94WlRtJ", "jpeg"),
    ("1Jq6xLm5Lpfwk6HaD-Y9Mjc-JwGvuoE0I", "jpeg"),
    ("1xrroDHcsZrHAei_f83tj7gNpEnwWg9mX", "jpeg"),
    ("1N7zfSk-_mOnoFWHFbiCMWacaxLM1LiNY", "jpg"),
    ("1zYaN0RRTiH4btTAuQ-WSaFdFAi2kDYhE", "webp"),
])
HOSPITAL = reg("hospital-curtains-dubai", "Hospital cubicle curtains in Dubai", [
    ("11WZr7ei3azDCr2HE-ezL_J9h5Bs7_oO7", "jpg"),
    ("1Fiv_jBiLelcXvXhT7eUyq8NkATQS9zdT", "jpg"),
    ("1Z8deoy4OBga_iHu2Ax2aGDGyeF4crp-d", "jpg"),
    ("1K-3pLkUP6ABgF0DMwvTj0GX1vg00L8rP", "jpg"),
])
LOGO_BLINDS = reg("logo-printed-sunscreen-blinds-dubai", "Customised logo sunscreen blinds in Dubai", [
    ("1tH1ZUzTTFwhlL_rewjSQtbk6ZHfNrzHJ", "jpg"),
    ("1gww0CAUX_KCYGaeFBz9U4YF5iE0IfayY", "jpg"),
    ("1n4nymKiDheicjPDT0g9zOo3sqPjIce3S", "jpg"),
    ("1-GBANzaCZyTsA3HlWLuiMKwr3Wm2guvE", "jpg"),
    ("1iISTtkP7w8Ohmlu6kzDU9SZ7AJ-uzaYl", "jpg"),
])
PRINTED = reg("printed-blinds-dubai", "Custom printed blinds in Dubai", [
    ("1DlRqT-Y8-zngSkI74mc0U1DZ7QQbcx6u", "jpg"),
    ("15zfSCc0uy0E605-O4hzXOhDVoyA9Oy4r", "jpg"),
    ("1MDLyRmXuIIQPRsKvo1lzzdT-hCaChJrQ", "jpg"),
    ("1M-Ukwf2rhTrpn8e3gvUSjFe38Jg-POri", "jpg"),
    ("1zqwWsrbK4ItSQLAZf73GnMlSu6RkFks7", "jpg"),
    ("1D295GWEezFPvCqOl_KgsSc3vRIbW1fmp", "jpg"),
    ("14omaMChPdDmb9TPDfBa4-xA_dYVUFchY", "jpg"),
    ("1kuqyCdimO7CoiDjgBI7HCtdgoqBW52Oi", "jpg"),
    ("16jrucpMxJUAIcVMJ21z9ACYFs6clxx5o", "jpg"),
])
ZEBRA = reg("zebra-blinds-dubai", "Zebra (day and night) blinds in Dubai", [
    ("16ZatRsgBCOMBllej2B6eZS7VBNEt08pt", "jpg"),
    ("1pYer3DlJDcCil-NeeMHCOZN7jr6o_439", "jpg"),
    ("1GeuPgyHo6eA911kgkcK6O9t7zk0s56yX", "jpg"),
    ("1R_GPgZ23mv4etEizeSeTD6TvPYjiSlO-", "jpg"),
    ("1YQEbfGqwgPw_GoCUAMdE20OXdxdTjb5h", "jpg"),
    ("1YyA8Lapl0Gz7l7iCg52hC02qKAsM63-J", "jpg"),
])
VERTICAL = reg("vertical-blinds-dubai", "Vertical blinds in Dubai", [
    ("1pVQ9BY_Jzv7m7M2M8tXSI_d8gaxbln7G", "jpg"),
    ("12i5JF43Js6a_EadcAFwliDTcqDU3xAoH", "jpg"),
    ("1lGN_P465Yqc8q4g0wlC55mkJYElTz9kv", "jpg"),
    ("1btMrcPVa61w7wou1qx2-CaNH-Emm3F9j", "webp"),
])
WOODEN = reg("wooden-blinds-dubai", "Wooden venetian blinds in Dubai", [
    ("10vBCpJyoeXxkPxWv1SXxHS5rk__OAoJu", "jpg"),
    ("1XyHjz7chHlyc2Q4SHZeEOHUJiuxmCAGP", "jpg"),
    ("1XcY3_apHOAYgFjVqHIMGx_Gqc05GwBkt", "jpg"),
    ("1FPtZzZsMmBf41a-FjRq0y7xz9Uat2I8f", "webp"),
    ("1Ev14Z26n8y81ntRMD7rsg6zMvlufHzid", "webp"),
    ("1TEbGS25HiFOS6JC8LahPasGu06VQTeni", "jpg"),
    ("1DWxI1y4B9IyBtCXCQyyobHn34dS2V92c", "jpg"),
    ("1gDRC2kBdLUEK5CCl3_cYUWpjvgurMOj6", "jpg"),
    ("1dHW1_0_0za7oakCjz8O6XVxtatPa0k7a", "jpg"),
    ("1t4fGnW2EVaDWWqO6U8d8-EYDSam1lOiO", "jpg"),
    ("1ikdLeZboV4zbrY8Vi2wpAUF_dfQ-VJSc", "jpg"),
])
ALU = reg("aluminium-venetian-blinds-dubai", "Aluminium venetian blinds 25mm in Dubai", [
    ("112-lMIVNjWyNU5lGnbR3mjmnerRARbFo", "jpg"),
    ("1z21KijmdbCpRCVYw1zGYon_smfUJp0MH", "jpg"),
    ("1G6XIRslqFa2AjoyXD2MkuDI-0jZYVi41", "webp"),
    ("1AErqNbIh_hRFreG6KVCPcyuFMqOKP_V4", "webp"),
    ("1dsoODjXq1Rpn4mNEHzNm9oxCZ6OajXqL", "png"),
    ("1i4hYWQlAqzcGKAYHqlPOPC1P8O20gTsh", "jpg"),
])
BAMBOO = reg("bamboo-blinds-dubai", "Bamboo blinds in Dubai", [
    ("1Ed0OwGUW7OGbwkZEYcV2NN1MfYb3B2eI", "jpg"),
    ("184FmuPlxz_CpvoxdC6THTolh3v92udtM", "jpg"),
    ("1n0aUE_malSaLcxJjn59D32U0ZGKER1Jv", "jpg"),
    ("1L0VgiOjpTIhgDz3G4CMHF779bHWR5kad", "jpg"),
])
ROMAN_B = reg("roman-blinds-dubai", "Roman blinds made to measure in Dubai", [
    ("1fk04yhYTNjkQYQlIDWUAa97M_sDK4nrj", "jpg"),
    ("11S_wrbGTnrGqAynGzQArd6OGYZDhreia", "jpg"),
    ("1hW-SMiynlVL859xJT6cUCIrVNIyNPGzr", "jpg"),
    ("1WuYOtYPQivjllf0gX-IYleyr1a42TpXp", "jpg"),
    ("1eAdDJwyVOPy8-24u7C55mRkRRjMQDcbw", "jpg"),
    ("1ubXKJX-_I1h5XdBhBVPl4EjTWGPt0SCd", "jpg"),
    ("1KBWjPbtuxLdepLt9HR2sby70GZbLjcBz", "jpg"),
])
SUNSCREEN = reg("sunscreen-roller-blinds-dubai", "Sunscreen roller blinds in Dubai", [
    ("1ZoHAHnYCGHDXC9YEKFlTiGmaold46ztc", "jpg"),
    ("1-lvxt7AypTsP-UDVNSrYbRJzkn8TO5RI", "jpg"),
])
BLACKOUT_ROLLER = reg("blackout-roller-blinds-dubai", "Blackout roller blinds in Dubai", [
    ("172ML6cSYfGOS6AKuXi-o_PsHiQbKB5dK", "jpg"),
    ("1Ht_bhX8clapGsQ3f19NclyoapF6nOGp0", "jpg"),
])
# Real installation photos from the owner's "Curtains" folder.
PROJECTS = reg("curtain-installation-project-dubai", "Curtains installed by Dubai Curtain Experts", [
    ("1OVfSWpG_XMS08v08AtkIqCbeLN29X4Ja", "jpg"),
    ("1OC-xqsmDlMv-ogvgpeuKceOAT1SSKoX2", "jpg"),
    ("1biQJj_dzaOVrqxPLtISs0slDwer7iZJu", "jpg"),
    ("15TdDape-aZETT3wnvA5SMHmvwYdE8qdi", "jpg"),
    ("1uZHM0CR-SJKo8b8qEW1x1wKR380MSb_z", "jpg"),
    ("1_2AEIBIDym6umEj0jhxK1DAG6SeUED9j", "jpg"),
    ("1RgLd9YVcCX_NDW-5Rq3-F--eqneDcjWf", "jpg"),
    ("1ua69W9KPji2PMYTJbKM9jYAZT_hV0yy7", "jpg"),
    ("1TscoULNbfKVlLvN6BfjzBdhvmjNhH5Id", "jpg"),
    ("1uCXCvGS8icwd7L_NlilueQMNPdhE9KGP", "jpg"),
    ("1VV-nNfEahoKbLQwjKvD8kQruugM8OX9R", "jpg"),
    ("1RPbmDOr6ab5hSxaVJ_Y6YYzTFP2SIIt4", "jpg"),
    ("1TCyCHOlr4O9r0ph-L7_3hijvBQVORlyY", "jpg"),
    ("1cmK0bwle4tzA1knyZKxGLRChbgzZkWcd", "jpg"),
    ("1u6nS10Y7yb1SmBQLZ2gjcH5EbHvNQXAB", "jpg"),
    ("1paGnhiNMwF06xR7qbgkdz_jdOhwilZtq", "jpg"),
    ("1H0DOf5bcUBhn-MFzxT5rCBoOvC3hXDDi", "jpg"),
    ("11cqAAf7UJWapzcv374Y6hpJjf43tjbll", "jpg"),
    ("1od1o-cBMpGIPVwI6IvT59lmW37a0oeQK", "jpg"),
    ("115PlAGduwHbJ_9LJgsv9VXE_9iCa3MwH", "jpg"),
])

# Photos already in the WordPress Media Library (uploaded by the owner).
EXISTING = {
    "blackout-bedroom": (2276, "https://dubaicurtainexperts.ae/wp-content/uploads/2026/08/Blackout_curtains_in_bedroom_202608101631.jpeg", "Blackout curtains in a Dubai bedroom"),
    "blackout-office": (2281, "https://dubaicurtainexperts.ae/wp-content/uploads/2026/08/Blackout_curtains_in_corporate_b%E2%80%A6_202608101630.jpeg", "Blackout curtains in a corporate office"),
    "sheer-living": (2280, "https://dubaicurtainexperts.ae/wp-content/uploads/2026/08/Curtains_in_minimalist_living_hall_202608101630.jpeg", "Sheer curtains in a minimalist living room"),
    "sheer-bedroom": (2278, "https://dubaicurtainexperts.ae/wp-content/uploads/2026/08/Curtains_blowing_in_bedroom_202608101631.jpeg", "Light sheer curtains in a bedroom"),
    "motor-hall": (2279, "https://dubaicurtainexperts.ae/wp-content/uploads/2026/08/Motorized_curtains_in_modern_hall_202608101630.jpeg", "Motorized curtains in a modern living hall"),
    "motor-bedroom": (2277, "https://dubaicurtainexperts.ae/wp-content/uploads/2026/08/Motorized_smart_curtains_in_bedroom_202608101631.jpeg", "Motorized smart curtains in a bedroom"),
    "hospital-room": (2275, "https://dubaicurtainexperts.ae/wp-content/uploads/2026/08/Curtains_in_hospital_patient_room_202608101630.jpeg", "Hospital privacy curtains in a patient room"),
}

# --------------------------------------------------------------------------
# Catalogues (public Google Drive PDFs — linked, not uploaded, because the
# files are 2–39 MB each).
# --------------------------------------------------------------------------
def drive_view(fid):
    return f"https://drive.google.com/file/d/{fid}/view"


CATALOGUES = [
    ("Curtain & upholstery fabrics", "Fabric collections for curtains, drapes and soft furnishing.", [
        ("Stellar 916", "1JcLP-vCrRfCtqZ6isRSN7dyRy7bhSwWU", "16.6 MB"),
        ("Matrix", "1CUQGyicWwo4YelPWfCCd2GO8CvMmiN8R", "12.7 MB"),
        ("Splendid 249", "1a05-I59wuo3GyneWir-0WB4jqmJ_W_9r", "5.9 MB"),
        ("Gems 917", "1Os81n2JDRQHDgu5eUsyXDjBF5_716Gld", "10.3 MB"),
        ("Linen Life 414", "1ro1hERYaN42n1HWBkROP1gral6c3Xee4", "10.5 MB"),
        ("Moods – Nottingham", "1PGkZxWaUBFzVnh-1BWrLgTEW3lZFqB8v", "4.2 MB"),
        ("Artisan VIII", "1MWYWQRf7GfMxe8MmXwxxgM2RAe19mJS_", "7.1 MB"),
        ("Awesome I", "1YfRE4azycybu1TRTwYAv0OGeKIp38UVC", "4.3 MB"),
        ("Awesome III", "1ZF3VULU5dZUpU5_F92VlhWgI08w7uGad", "3.7 MB"),
        ("Awesome IV", "1dMMh7mY4qRf9dFBnLmjVXLGZStijkZdW", "4.4 MB"),
        ("Awesome V", "1EvB-E9WyKaMnRRJZ01VrUa17taoCZKEu", "5.2 MB"),
        ("Awesome VI", "10eklmrh1s9XuogVUzC9j2NUXVTeMzpkP", "3.3 MB"),
        ("Awesome VII", "1X-TCnoD0YeTzhHOa97-Nmxbe-jFe18Xj", "5.0 MB"),
        ("Awesome VIII", "1sRnJ7qaWGGb6Uvd6reIUKmwnMWuM5iO_", "4.5 MB"),
    ]),
    ("D3 collection", "The D3 e-catalogue series.", [
        ("Albania", "1oaZ18XrtuqhPj0M77HFaJZLYSOfqJM1V", "36.8 MB"),
        ("Belgium", "1veR_DoIoefsvt-qE69wR1EtMlJrv61qV", "28.3 MB"),
        ("Benin", "1xP4T1oRVS0GzeHOeZ_en6MhMriM-3Czg", "34.5 MB"),
        ("Congo", "1Al1iieCzlhRBpFZFFPsBlKqdeVFRjhzL", "17.2 MB"),
        ("Croatia", "1B3ywb5KulAef_3BnTR_iQqO_G4KO2BSJ", "32.2 MB"),
        ("Cyprus", "19EY0FemD-dbrtjGYnTXQNPnkhA269D44", "14.2 MB"),
        ("Denmark", "1zWR5IDayJu9Brcwafq_BOMqqXIo5R-W-", "18.0 MB"),
        ("Estonia", "10FyMfgonPMunTJb4j52Mvu3crtTI08Kv", "24.0 MB"),
        ("Finland", "1sFfeIopn8G1P1MyTmMYBX22SsAQZ9gu7", "22.9 MB"),
    ]),
    ("Outdoor & Sunbrella fabrics", "Outdoor, awning and performance fabrics.", [
        ("Sunbrella Library Vol. I", "14VIZqJRNOKT2KpkK_s3P_hn3y6Lm878M", "14.3 MB"),
        ("Sunbrella Library Vol. II", "10lSfZqKvLslB7RY2Ka7vC0XaBoHr7nug", "24.2 MB"),
        ("Digital Sunbrella Forever", "1N0Kov2LJ-D-nkkwbMudZC5hizorEavfa", "29.7 MB"),
        ("SEU 1", "1NnLQKljNoxlRqtrfOETU0qoTcAVqxRmd", "2.5 MB"),
        ("Awning (AW)", "13DS19146BRiZl8quVliLrdno5EKN-YhP", "38.8 MB"),
        ("Elite (EL)", "1UHV8PfmJ4ZKHitXPTGhy-ItoyFS-MVi4", "28.5 MB"),
    ]),
    ("Custom printing", "Print your own design, photo or logo on fabric and blinds.", [
        ("Custom Print on Fabric", "195b9Z4Cw-2oL3_8PdogrP3ZOM4KnR7-E", "1.5 MB"),
    ]),
]
