<?php
/**
 * Google Drive importer.
 *
 * The Casa Vera Home Drive folders are shared "anyone with the link", so the
 * site downloads each file straight from Drive, adds it to the Media Library,
 * attaches it to its page and (for the first photo of a page) sets the
 * featured/hero image. PDFs become catalogue cards with the in-page viewer.
 *
 * Run it from Appearance → CFM Setup, with `wp cfm drive-import`, or as an
 * administrator via POST /wp-json/cfm/v1/drive-import. Files already imported
 * are skipped, so it is safe to run again after adding entries below.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

/**
 * Drive files to import: file ID, page slug, optional title, featured flag.
 *
 * @return array<int,array{id:string,slug:string,title?:string,featured?:bool,excerpt?:string}>
 */
function cfm_drive_manifest() {
	return array(
		// Catalogues & technical data sheets (PDF).
		array( 'id' => '1W438S0qUCw-Ozt5ikLXZItq851hStTKv', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber & EPDM Gym Mats — Catalogue', 'excerpt' => 'Rubber and EPDM gym mats and tiles: thicknesses, colours and applications.' ),
		array( 'id' => '1lWNzFkI1Mv7CdsDEAbbisiDaJv_u9TX3', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Extreme — Technical Data Sheet', 'excerpt' => 'Heavy-duty rubber gym tiles for free-weight and drop zones.' ),
		array( 'id' => '1ZHPp56n1IyHmkrtZ0tjTAuv5SABaMx6w', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Active Rolls — Technical Data Sheet', 'excerpt' => 'Rubber flooring rolls for gyms, studios and large training areas.' ),
		array( 'id' => '12iT6imeJ1wwCxpTrU6u3ZPDB6HpwbI-t', 'slug' => 'kids-play-mats', 'title' => 'TerraPlay Square — Technical Data Sheet', 'excerpt' => 'Square rubber safety tiles for play areas and playgrounds.' ),

		// Rubber gym mats.
		array( 'id' => '15BjzYq7Zj9XzJ0JQ3efZGjfv7v70s_qf', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber gym mat tiles', 'featured' => true ),
		array( 'id' => '1_U73Q95zFBSzHwIdEQCv1JVj2anUbCgk', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM rubber gym mats' ),
		array( 'id' => '1E1TpjV3DY4rb-emV0X-IkWMJ49I5ieiq', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM colour-fleck gym mats' ),
		array( 'id' => '1yOTr_A1eMhXiDLZ1W2-JVKuWp14mxKSn', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM rubber mats, 15–50 mm' ),
		array( 'id' => '1P2ecP_E1qBtM8vSQCtXnk7ycAEyX5rrR', 'slug' => 'rubber-gym-mats', 'title' => 'EPDM gym mat installation' ),
		array( 'id' => '1kBmkY7oSGwPNHyI3FLRt38e4ZMGOufXH', 'slug' => 'rubber-gym-mats', 'title' => 'Vinyl and rubber gym mats' ),
		array( 'id' => '19QgURUjIipT7eawHVRUIdXMx3KpEZTbe', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Extreme rubber tile' ),
		array( 'id' => '1NOd5q9Q40y0VCAXzYczcTXWDsYiKFU-5', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Square rubber tile' ),
		array( 'id' => '1kjTDx7ouBJl5bcu9SAbzz85Gao94xy-r', 'slug' => 'rubber-gym-mats', 'title' => 'TerraFit Active rubber rolls' ),
		array( 'id' => '1O-EiGRUY4r2UhEWogojSpa22AxIOaB7x', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber roll gym mats' ),
		array( 'id' => '1DLn08OCt6g72rrviiWd2Mf23MGRLba5J', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber roll gym mats installed' ),
		array( 'id' => '1oWth5q92itdcgA7Y9wYe6ByqjrGXntgC', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber mats' ),

		// Custom / logo mats.
		array( 'id' => '1I4orsrCvu0ZTOU7WKUYIgYPI6qU7Gjh_', 'slug' => 'custom-logo-mats', 'title' => 'Customised mat', 'featured' => true ),

		// Kids play mats.
		array( 'id' => '1114NR1IT9b3W5C-zyrUBTVQHvslVc3oX', 'slug' => 'kids-play-mats', 'title' => 'Kids play area matting', 'featured' => true ),
		array( 'id' => '1phPRT5HMbTgUkk7NI84UpqJJIQp9XT8l', 'slug' => 'kids-play-mats', 'title' => 'Colourful EPDM play surface' ),

		// Project photos.
		array( 'id' => '1e7RC9z2pQOgU8gLszCSXQ8_LtPzUfZFV', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1bbzwAzII5BQY702xN8CKghSDdjxiEsIV', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1b2e_qENdXI-R7cagx12R117vi2veBjs_', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1o8G9og6lDgXU6UH8cleiU8HAhbxVxs02', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '18ivfR9bIw_UaNSvLtCbdDljLrGEszmWM', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1jQVZ7ip4-gTH6dgCpiagmCrE0tvMQORl', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1JzxAM3SivT-aTHelqX88B9X125MY-EGY', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1XYCgf21EOe4SHTdJyI5jCk06kJT1yVmk', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1brsl82jxdawrgYnh16qObVKWuR5mH7Zl', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1oWh5ilNDckO0iXQIEjjlnkAJZyoxHfc8', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1xG5x4ulZopag8Vqh9WI72-XncoEjrEri', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '19ydUSZsB9r7yth3qK1CvgJS0Y4W0US-S', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1_Z4Jfm6pyMQc0LXXJ7bzWyABuZvNnAuQ', 'slug' => 'projects', 'title' => 'Mat project' ),
		array( 'id' => '1z_v9Ne6ctrQl6guiOhTr_3LwhgK7SB7z', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1uVeDulnbzXAeW6pX5bvTYLE4T2BRnYzZ', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1WvjHAYC41qW8_KZam2jd7tswZmyOcXp6', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1q2jm_dfbrbJKNwRtpWzVt8AC7Qz34ALU', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1UUh--a5LsRFz_saXO5nPOax2tNqsgVrU', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1eg_c42-WHzoG1ZzemdKds2vyGtO5cqQw', 'slug' => 'projects', 'title' => 'Poured rubber project' ),
		array( 'id' => '1ozpXs9d9V9Hjc0FhlTSn2vALjESPo-PD', 'slug' => 'projects', 'title' => 'Poured rubber project' ),

		// MATS folder — catalogues & data sheets (PDF).
		array( 'id' => '1gWgnDWBNoNJMdqHAdtCsTLzxvz328k-t', 'slug' => 'custom-logo-mats', 'title' => 'Promotional & Logo Mats Guide 2026', 'excerpt' => 'Every printed logo mat in the range — Jet-Print Light Eco, Eco, Velour, Vision, Horizon, Outdoor, Patio, Event and Big-Size — with sizes and artwork tips.' ),
		array( 'id' => '146NjWD1Ntuubj4p9I_9fY-dcfl170Lgn', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Plus — Product Sheet', 'excerpt' => 'Deep-pile printed logo mat: sizes, widths, backing and guarantee.' ),
		array( 'id' => '1-uX5yv1jCnlXzs1WVGsf5JwRYTpGSTMv', 'slug' => 'recessed-entrance-matting', 'title' => 'Fitted Entrance Systems — Specification', 'excerpt' => 'Aluminium grid and tile entrance systems for matwells: infills, heights, grid colours and guarantees.' ),
		array( 'id' => '1xXt0nUXyxWA3uJByzBIT0im7Codth757', 'slug' => 'entrance-mats', 'title' => 'Iron-Horse — Technical Data Sheet', 'excerpt' => 'High-twist nylon entrance mat: widths, absorption, colours and guarantee.' ),
		array( 'id' => '1JVpC7HSMaFr412Fb6BqlKyeA8RoA7CrZ', 'slug' => 'entrance-mats', 'title' => 'EcoAbsorb — Technical Data Sheet', 'excerpt' => 'Our most absorbent entrance mat, made with recycled yarn.' ),
		array( 'id' => '1AyDGwfxS8TV54WU7jkAjR8DHSnIZoMJI', 'slug' => 'outdoor-scraper-mats', 'title' => 'Brush Mat — Technical Data Sheet', 'excerpt' => 'Needle-punch brush mat for outdoor scraping and entrance wells.' ),
		array( 'id' => '1fzaTn3bcW_PTWhSnwXxev8EvtvCo0sfx', 'slug' => 'coir-doormats', 'title' => 'Coir Mat — Technical Data Sheet', 'excerpt' => 'Natural coir in 8 colours, logo inlay up to 180 × 400 cm.' ),
		array( 'id' => '1J2xEe1DLxsum8W2cM-wnZlWW0PUZu1hN', 'slug' => 'anti-fatigue-mats', 'title' => 'Kleen-Comfort Office — Technical Data Sheet', 'excerpt' => 'Carpet-top anti-fatigue mat for counters and workstations.' ),
		array( 'id' => '1AsD4OvBNzAgMwh8Fr2aVt_nAv45svaQZ', 'slug' => 'anti-fatigue-mats', 'title' => 'Kleen-Comfort Safety — Technical Data Sheet', 'excerpt' => 'Industrial rubber-top anti-fatigue mat for production lines.' ),
		array( 'id' => '1GQV4v-u95kCwOUZo3FTiUO7V7LpK7Tp7', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'Kleen-Wave — Technical Data Sheet', 'excerpt' => 'Anti-microbial wet-area mat for pools, showers and changing rooms.' ),
		array( 'id' => '11esjTWokSL0Psew_Fi8qgJOW8arU_uAW', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'Ablution Mats — Catalogue', 'excerpt' => 'PVC ablution and wet-area mats for mosques, washrooms and pools.' ),
		array( 'id' => '1Z_pByBavDAeak4c-AdRQPw_HAmrGSOd4', 'slug' => 'lift-day-mats', 'title' => 'Kleen-Cooler Mat — Technical Data Sheet', 'excerpt' => 'Small absorbent mat for water dispensers and coffee points.' ),
		array( 'id' => '1-NwO6OnHO7uLj1zmxzax3wPzavC6t-5r', 'slug' => 'car-floor-mats', 'title' => 'Parking Mat — Technical Data Sheet', 'excerpt' => 'Printed parking-bay mat for showrooms, garages and residences.' ),
		array( 'id' => '1MuihIeZXCIbg6TiEr7Oz12KhvepeziFi', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber Mats — Catalogue', 'excerpt' => 'Stud, coin, hollow, ramp and horse rubber mats.' ),

		// Custom logo mats.
		array( 'id' => '1zyQX6t0qIAw_-8gHyvgAPHxJrb4BHlrn', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print logo mat' ),
		array( 'id' => '1nO9ffYtgRJjc-9kmzcMOoUCURhufBh-Q', 'slug' => 'custom-logo-mats', 'title' => 'Logo carpet finishes compared' ),
		array( 'id' => '1etZMagNHOvi0NL3vvYkmsAONfxKockAy', 'slug' => 'custom-logo-mats', 'title' => 'Custom logo mat' ),
		array( 'id' => '1ucusWBTzWkdiJ2h8WUuux6sAeG8fVVXF', 'slug' => 'custom-logo-mats', 'title' => 'Printed logo mat at an entrance' ),
		array( 'id' => '1zFdtCogYglG4Ap27_Fl6rUfpcQw03Txk', 'slug' => 'custom-logo-mats', 'title' => 'Full-colour logo mat' ),
		array( 'id' => '1njj4zdcbRrqYl6pYoEwcapxMnuuIDjzt', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Eco logo mat' ),
		array( 'id' => '16eBihPzih8gEFJP8jgLlE2A78RvUoFOi', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Velour logo mat' ),
		array( 'id' => '19bRKOUHoBKv6XgrnWvKL7tApzHS-7zsM', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Vision logo mat' ),
		array( 'id' => '1jEitTem9hjMKe6AZhcljnX9ilYDE1R0y', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Plus deep-pile logo mat' ),
		array( 'id' => '1QCqHcJy-93O0P_AFFXsq7vW1hPkBOIa8', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Light Eco promotional mat' ),
		array( 'id' => '1ch0U6vDfKI_RfyATrsJd30vZnDSfY91K', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Outdoor logo mat' ),
		array( 'id' => '1amzYlMjOM4q6XEYDXaMy2VsYEIEnAyCB', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Horizon printed runner' ),
		array( 'id' => '1qjgfq5Qg2S5NuxMsjlswii6K0YSNVSPd', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Event floor graphic' ),
		array( 'id' => '1Bhy-Fh-qwVZ7hl2Msgau2p-lYQwKiDEz', 'slug' => 'custom-logo-mats', 'title' => 'Jet-Print Patio promotional mat' ),
		array( 'id' => '1algJE_kUvGz8iF4mWaFY2GkwszgNO1vh', 'slug' => 'custom-logo-mats', 'title' => 'PU paint logo mat' ),
		array( 'id' => '1tRTdnjIZKCchwneWhmikA-qLx1ugtkTr', 'slug' => 'custom-logo-mats', 'title' => 'PU paint logo mat' ),
		array( 'id' => '14EL3janQigywkbhgPBqrxjtq_ZrJzwk5', 'slug' => 'custom-logo-mats', 'title' => 'UV printed logo mat' ),

		// Entrance mats.
		array( 'id' => '12w9kqWlH4q6a3sUvBEHv7_f5zAeX-p55', 'slug' => 'entrance-mats', 'title' => 'Iron-Horse entrance mat, Black Mink' ),
		array( 'id' => '1dUN-NQXlJ639HYXkD5Kug35Guqe8ySs7', 'slug' => 'entrance-mats', 'title' => 'Iron-Horse entrance mat, Black Steel' ),
		array( 'id' => '15UcTVyO1ht5WA4RSKfrTQcV_koc11ldQ', 'slug' => 'entrance-mats', 'title' => 'Iron-Horse entrance mat, Black Scarlet' ),
		array( 'id' => '16G9taHIF9q1h27Eb7zGlcA5uHTQD6Ras', 'slug' => 'entrance-mats', 'title' => 'Iron-Horse Light entrance mat' ),
		array( 'id' => '1jkvmZyiZSWb1EkUi9zA4L3W0Av3IGj4l', 'slug' => 'entrance-mats', 'title' => 'EcoAbsorb entrance mat' ),
		array( 'id' => '1Bs38XafccnSLdZlpQ6gOPNe_O89ol4_c', 'slug' => 'entrance-mats', 'title' => 'EcoAbsorb entrance mat in use' ),
		array( 'id' => '1HDvZIF9nC17-guL1gQpMe4pjMiQvnmgR', 'slug' => 'entrance-mats', 'title' => 'Monotone entrance mat, Scarlet' ),
		array( 'id' => '1s79Gmav0U2mxnZic8UK_xrY9hUMj7JRQ', 'slug' => 'entrance-mats', 'title' => 'CapeCod entrance mat' ),
		array( 'id' => '17Ql6lgpltCntytXryNQHwV0sUymKrVnR', 'slug' => 'entrance-mats', 'title' => 'Turtle Mat washable doormat' ),
		array( 'id' => '1yi8XE-nC8b--WK4c8wSVEWk8RMlfn7bF', 'slug' => 'entrance-mats', 'title' => 'Turtle Mat washable doormat' ),

		// Outdoor scraper mats.
		array( 'id' => '1tZjBuA8JwVbF1r-74kbq6KP63jw5037n', 'slug' => 'outdoor-scraper-mats', 'title' => 'Brush scraper mat' ),
		array( 'id' => '1J9EXP20cDmFxyLcA7CLmH18M7DExAM21', 'slug' => 'outdoor-scraper-mats', 'title' => 'Brush scraper mat at an entrance' ),
		array( 'id' => '19zf0MBrOl4pDOpzb1Xcflke6G-Q64RjE', 'slug' => 'outdoor-scraper-mats', 'title' => 'Outdoor logo scraper mat' ),
		array( 'id' => '1M8tC6Mf6uN_aNUlC-mRjYr6frhqH2T5L', 'slug' => 'outdoor-scraper-mats', 'title' => 'Green-Horse outdoor mat' ),
		array( 'id' => '1QqIB_QcVD1WjRp9qxnfJUR04Regw36G5', 'slug' => 'outdoor-scraper-mats', 'title' => 'Rubber scraper mat' ),
		array( 'id' => '1dsKFggWRy8efq5BhXXo_eOPG2hzjoTMq', 'slug' => 'outdoor-scraper-mats', 'title' => 'Hollow rubber mat' ),

		// Coir doormats.
		array( 'id' => '1z7-E3frigd0ZWU5IKakmvTcCJPsRa2Bt', 'slug' => 'coir-doormats', 'title' => 'Coir entrance mat' ),
		array( 'id' => '1pGxEU0HP0FU97in1KeG56IFqPd0xtufy', 'slug' => 'coir-doormats', 'title' => 'Natural coir mat' ),
		array( 'id' => '1CRQv-P-ULYaNnDMKeVgQhI-OnIS9wtWh', 'slug' => 'coir-doormats', 'title' => 'Coir mat with logo' ),
		array( 'id' => '13CwQky_mCoF0xkmIlWSt1mJTZna7DwR3', 'slug' => 'coir-doormats', 'title' => 'Coir mat in an entrance well' ),
		array( 'id' => '1KjCXDcCZWPp65e3k1VTPkO8rEbrSx8Ds', 'slug' => 'coir-doormats', 'title' => 'Coir doormat' ),

		// Recessed entrance matting.
		array( 'id' => '1YfARgfGSeNZxZ95ccuC87Cp8PGcfdcfl', 'slug' => 'recessed-entrance-matting', 'title' => 'Recessed aluminium entrance system' ),
		array( 'id' => '1FHbeRf-PdowTNS968srLrKYaDiMBaEkA', 'slug' => 'recessed-entrance-matting', 'title' => 'Recessed entrance system in a lobby' ),
		array( 'id' => '13eaSWl3ANafyN0raAn6WQAkqS2LKDgf_', 'slug' => 'recessed-entrance-matting', 'title' => 'Entrance system infill detail' ),
		array( 'id' => '1eXpl_y9KJ-qX2cp_AZIqGTDTlhcdnpjw', 'slug' => 'recessed-entrance-matting', 'title' => 'Brush mat in an entrance well' ),

		// Anti-fatigue mats.
		array( 'id' => '1RPO4_Tvi0kBJ5qcxzth4kcNGmIhcoBLR', 'slug' => 'anti-fatigue-mats', 'title' => 'Kleen-Comfort Safety anti-fatigue mat' ),
		array( 'id' => '1qxMmSUGcKTjNto-kSfpTAcQPqj-ZxKTL', 'slug' => 'anti-fatigue-mats', 'title' => 'Anti-fatigue mat at a workstation' ),
		array( 'id' => '1IV8e1wDvw_-tSNnfpENKVm4jLLD5ig6J', 'slug' => 'anti-fatigue-mats', 'title' => 'Kleen-Comfort Design anti-fatigue mat' ),
		array( 'id' => '177ssLLmADkWNZtqJXYUHWGbZq_jQn1Fb', 'slug' => 'anti-fatigue-mats', 'title' => 'Kleen-Comfort Office anti-fatigue mat' ),
		array( 'id' => '1gXxXsfkArbPlvre7mx8dCmfmuV9wQ7qG', 'slug' => 'anti-fatigue-mats', 'title' => 'Kleen-Comfort Soft anti-fatigue mat' ),
		array( 'id' => '1FELRtpSM-X948ZgfKBQeSrtbAq6550B1', 'slug' => 'anti-fatigue-mats', 'title' => 'Standing mat' ),

		// Anti-slip & wet-area mats.
		array( 'id' => '1Ei3Utv3oWnDFBRIiNNVxgGBsRn_NgEhJ', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'Kleen-Wave wet-area mat' ),
		array( 'id' => '1cBvLiUIu_C8kAgCtI0UVVcv4fHAC591w', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'Kleen-Wave poolside matting' ),
		array( 'id' => '1TYBuGTG8vyLvAIDD0Z_yZz8Ezra73SOC', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'Pool mats' ),
		array( 'id' => '1d3ku3T-w7XqdtniZMgPtZ_ESeHQac9vF', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'Ablution mats' ),
		array( 'id' => '1E0CZIFjg5303svTsuoPZHibsk7OC8dSK', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'PVC wet-area mat' ),
		array( 'id' => '1rDHx68Xzug6jbfDKyUT2BKZIyojWQu6D', 'slug' => 'anti-slip-wet-area-mats', 'title' => 'PVC ablution mat' ),

		// PVC coil, lift, car and rubber mats.
		array( 'id' => '1cGRkJo5p4FYvM6nGJ2fHoMTVugHWLfqK', 'slug' => 'pvc-coil-mats', 'title' => 'PVC coil mat' ),
		array( 'id' => '1YgTcgwQkmS1SkepBqzdHzuis5AsuRhl0', 'slug' => 'lift-day-mats', 'title' => 'Printed lift and corridor mat' ),
		array( 'id' => '1hNjhkFA6Ahi_BKPK_xzFSlNnNAV1EjU1', 'slug' => 'lift-day-mats', 'title' => 'Kleen-Cooler mat' ),
		array( 'id' => '1RsDcQ7xyHTstIPJlpIUdO0bTkciXeRTb', 'slug' => 'car-floor-mats', 'title' => 'Car boot protector mat' ),
		array( 'id' => '1DNpmLrQBrz_TIEWd6mmU7LQTyDu2d2Al', 'slug' => 'car-floor-mats', 'title' => 'Car boot protector mat' ),
		array( 'id' => '1zfAeg5dn3ii360MX8Kv057P9v9LejAMX', 'slug' => 'car-floor-mats', 'title' => 'Car protector set' ),
		array( 'id' => '1-eQ5P53JYcvr37chDcV0IWewqR4b8vLo', 'slug' => 'car-floor-mats', 'title' => 'Printed parking mat' ),
		array( 'id' => '1L81wBKsyBEjD6kxfnNk21iK4-__0ofnX', 'slug' => 'car-floor-mats', 'title' => 'Parking mat in a garage' ),
		array( 'id' => '1V9iQXOYXQLGBzKuyJQ78x21KKIOFvY-R', 'slug' => 'rubber-gym-mats', 'title' => 'Stud rubber mat' ),
		array( 'id' => '1Hu_ytOYnu-ASbNDASBiUIkLALzjh7QXd', 'slug' => 'rubber-gym-mats', 'title' => 'Stud rubber mat' ),
		array( 'id' => '1NCo7t6_N05LxAlNAvHLV9ZrrCL4Mh2pG', 'slug' => 'rubber-gym-mats', 'title' => 'Hollow rubber mat' ),
		array( 'id' => '1wqJb53mrkY4f2-QNSbycGQXnhA3-JcTn', 'slug' => 'rubber-gym-mats', 'title' => 'Rubber ramp mat' ),
	);
}

/**
 * Import pending Drive files.
 *
 * @param int $limit Max files this run (0 = all). Keeps each request short on small hosts.
 * @return array{imported:int,featured:int,failed:int,remaining:int,notes:string[]}
 */
function cfm_drive_import( $limit = 0 ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	$done   = (array) get_option( 'cfm_drive_imported', array() );
	$result = array( 'imported' => 0, 'featured' => 0, 'failed' => 0, 'remaining' => 0, 'notes' => array() );
	$count  = 0;

	foreach ( cfm_drive_manifest() as $file ) {
		if ( isset( $done[ $file['id'] ] ) && get_post( (int) $done[ $file['id'] ] ) ) {
			continue;
		}
		if ( $limit && $count >= $limit ) {
			++$result['remaining'];
			continue;
		}
		++$count;
		$page = cfm_get_page( $file['slug'] );
		if ( ! $page ) {
			$result['notes'][] = sprintf( 'No page "%s" — run the page installer first.', $file['slug'] );
			++$result['failed'];
			continue;
		}

		$tmp = download_url( 'https://drive.google.com/uc?export=download&id=' . rawurlencode( $file['id'] ), 120 );
		if ( is_wp_error( $tmp ) ) {
			$result['notes'][] = sprintf( '%s: %s', $file['id'], $tmp->get_error_message() );
			++$result['failed'];
			continue;
		}
		$ext = cfm_drive_sniff_ext( $tmp );
		if ( '' === $ext ) {
			wp_delete_file( $tmp );
			$result['notes'][] = sprintf( '%s: not an image or PDF (is the file still shared?).', $file['id'] );
			++$result['failed'];
			continue;
		}
		$title = isset( $file['title'] ) ? $file['title'] : $page->post_title;
		$id    = media_handle_sideload(
			array(
				'name'     => sanitize_file_name( $file['slug'] . '-' . sanitize_title( $title ) . '.' . $ext ),
				'tmp_name' => $tmp,
			),
			$page->ID,
			$title,
			array( 'post_excerpt' => isset( $file['excerpt'] ) ? $file['excerpt'] : '' )
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$result['notes'][] = sprintf( '%s: %s', $file['id'], $id->get_error_message() );
			++$result['failed'];
			continue;
		}
		$done[ $file['id'] ] = $id;
		update_option( 'cfm_drive_imported', $done, false );
		++$result['imported'];

		if ( 'pdf' !== $ext ) {
			update_post_meta( $id, '_wp_attachment_image_alt', sprintf( '%s — %s', $title, cfm_opt( 'brand_name' ) ) );
			if ( ! empty( $file['featured'] ) && ! get_post_meta( $page->ID, '_thumbnail_id', true ) ) {
				set_post_thumbnail( $page->ID, $id );
				++$result['featured'];
			}
		}
	}
	return $result;
}

/**
 * File type from its first bytes (Drive download names carry no extension).
 *
 * @param string $path Temp file.
 * @return string jpg|png|webp|gif|pdf or ''.
 */
function cfm_drive_sniff_ext( $path ) {
	$head = (string) file_get_contents( $path, false, null, 0, 16 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( 0 === strpos( $head, '%PDF' ) ) {
		return 'pdf';
	}
	if ( 0 === strpos( $head, "\xFF\xD8\xFF" ) ) {
		return 'jpg';
	}
	if ( 0 === strpos( $head, "\x89PNG" ) ) {
		return 'png';
	}
	if ( 0 === strpos( $head, 'GIF8' ) ) {
		return 'gif';
	}
	if ( 0 === strpos( $head, 'RIFF' ) && 'WEBP' === substr( $head, 8, 4 ) ) {
		return 'webp';
	}
	return '';
}

add_action( 'rest_api_init', 'cfm_drive_rest' );
/**
 * Administrator-only REST routes: run the installer and the Drive import.
 */
function cfm_drive_rest() {
	$admin = static function () {
		return current_user_can( 'manage_options' );
	};
	register_rest_route(
		'cfm/v1',
		'/drive-import',
		array(
			'methods'             => 'POST',
			'permission_callback' => $admin,
			'args'                => array( 'limit' => array( 'default' => 8, 'sanitize_callback' => 'absint' ) ),
			'callback'            => static function ( WP_REST_Request $request ) {
				return rest_ensure_response( cfm_drive_import( (int) $request['limit'] ) );
			},
		)
	);
	register_rest_route(
		'cfm/v1',
		'/install',
		array(
			'methods'             => 'POST',
			'permission_callback' => $admin,
			'callback'            => static function () {
				return rest_ensure_response( cfm_install() );
			},
		)
	);
	register_rest_route(
		'cfm/v1',
		'/rebuild',
		array(
			'methods'             => 'POST',
			'permission_callback' => $admin,
			'args'                => array(
				'slugs' => array(
					'required' => true,
					'type'     => 'array',
					'items'    => array( 'type' => 'string' ),
				),
			),
			'callback'            => static function ( WP_REST_Request $request ) {
				$slugs = array_values( array_intersect( array_map( 'sanitize_title', (array) $request['slugs'] ), array_keys( cfm_pages_data() ) ) );
				cfm_install_pages( $slugs );
				return rest_ensure_response( array( 'rebuilt' => $slugs ) );
			},
		)
	);
}
