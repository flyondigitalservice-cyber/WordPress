<?php
/**
 * Product details taken from the supplier catalogues and data sheets in the
 * Casa Vera Home "MATS" Drive folder. Merged into the product pages by
 * cfm_pages_data(): 'specs' replaces the generic list, 'range' and 'compare'
 * add sections, 'faqs' are appended.
 *
 * @package CFM
 */

defined( 'ABSPATH' ) || exit;

/**
 * Catalogue details by product slug.
 *
 * @return array<string,array>
 */
function cfm_catalogue_data() {
	return array(
		'custom-logo-mats'          => array(
			'specs'   => array(
				'Printed in 66 standard colours, or matched to your Pantone, RAL, HKS or RGB brand colours',
				'Standard widths 60, 75, 85, 115, 150 and 200 cm; lengths up to 7 m, printed runners up to 20 m',
				'Thickness from 7 mm (promotional) to 14 mm (heavy-duty); non-slip nitrile rubber backing, PVC-free',
				'Rectangles, rounds, half-moons or cut to the outline of your logo',
				'Washable, colourfast and lightfast finishes; safe over underfloor heating',
				'Guarantee of up to 5 years, depending on the finish',
			),
			'range'   => array(
				'title' => 'Eight ways to put your logo on the floor',
				'items' => array(
					array( 'Jet-Print Light Eco', 'Our best-selling promotional logo mat. Nylon with at least 75% recycled content, vivid colours and fully washable.' ),
					array( 'Jet-Print Eco', 'The premium logo mat for busy business entrances: sharp branding, high dirt and moisture absorption, built for daily laundering.' ),
					array( 'Jet-Print Vision', 'A velvet-like nylon 6.6 surface for very fine detail, with a deeper pile that keeps working in heavy traffic.' ),
					array( 'Jet-Print Velour', 'A soft velour finish for photo-quality images, gradients and small lettering — ideal for receptions and showrooms.' ),
					array( 'Jet-Print Plus', 'A plush, deeper pile you can feel underfoot, for hotels, villas and premium entrances.' ),
					array( 'Jet-Print Outdoor', 'Grass-like monofilament nylon on a ribbed rubber backing for terraces, gates and exterior doors.' ),
					array( 'Jet-Print Horizon', 'Printed runners 100–200 cm wide and up to 20 m long that turn corridors and walkways into brand space.' ),
					array( 'Event & Big-Size', 'Flat, PVC-free floor graphics up to 4 × 40 m for exhibitions, launches, stands and red-carpet events.' ),
				),
			),
			'compare' => array(
				array(
					'title' => 'Which logo finish suits your space?',
					'intro' => 'All four finishes carry your logo, have an anti-slip backing and are easy to clean. The right one depends on traffic, location and how detailed the design is.',
					'head'  => array( 'Finish', 'How it is made', 'Thickness', 'Best for' ),
					'rows'  => array(
						array( 'Jet Print', 'Jet-printed nylon or polypropylene, non-slip rubber backing', '8–12 mm', 'Indoor and covered entrances with medium traffic — offices, shops, schools, hotels' ),
						array( 'Jet Print Plus', 'High-density premium nylon with richer colour depth, heavy-duty rubber backing', '10–14 mm', 'High-traffic areas — malls, hospitals, corporate buildings, airports' ),
						array( 'PU Paint', 'Logo hand-finished in PU paint for a bold, 3D textured look', '10–14 mm', 'Very high traffic and outdoor entrances — factories, warehouses, workshops' ),
						array( 'UV Print', 'UV-cured print with high precision and strong fade resistance', '8–12 mm', 'Branding-led interiors — showrooms, receptions, salons, boutiques' ),
					),
				),
				array(
					'title' => 'Printed logo mats — technical comparison',
					'head'  => array( 'Product', 'Thickness', 'Weight', 'Dirt held', 'Standard widths', 'Washable' ),
					'rows'  => array(
						array( 'Jet-Print Light', '7 mm', '1.9 kg/m²', '470 g/m²', '60–200 cm', 'Yes' ),
						array( 'Jet-Print', '9 mm', '2.48 kg/m²', '600 g/m²', '60–200 cm', 'Yes' ),
						array( 'Jet-Print Vision', '9 mm', '2.75 kg/m²', '800 g/m²', '60–200 cm', 'Yes' ),
						array( 'Jet-Print Outdoor', '8 mm', '3.6 kg/m²', '—', '50–200 cm', 'No' ),
						array( 'Jet-Print Horizon', '10 mm', '3.8 kg/m²', '700 g/m²', '100–200 cm (runners to 20 m)', 'No' ),
					),
				),
			),
			'faqs'    => array(
				array( 'Which logo finish should I choose?', 'For most offices and shops a Jet Print mat is the best value. Choose Jet Print Plus for malls, hospitals and other high-traffic doors, PU paint for outdoor and industrial entrances, and UV print when the design is detailed and the space is all about branding. Send us a photo of the entrance and we will recommend one.' ),
				array( 'How should I prepare my artwork?', 'Vector files (AI, EPS, CDR or a print-ready PDF) are best; photos and pixel artwork should be 300 dpi at full size. Keep lettering at least 5 cm tall, leave about 5 cm between the design and the border, and avoid white or very light backgrounds — dark or patterned backgrounds hide dirt far better. We check and adjust every file before printing.' ),
			),
		),

		'entrance-mats'             => array(
			'specs'   => array(
				'Surfaces: solution-dyed high-twist nylon, micro-nylon blends and cotton/microfibre for homes',
				'Absorbs up to 1.1 kg of dirt and 6 litres of water per m², depending on the mat',
				'Thickness 7–15 mm; phthalate-free nitrile rubber backing with 20 mm bevelled border',
				'Standard widths 85, 115, 150 and 200 cm; lengths up to 7 m; borderless versions for wall-to-wall',
				'Fire rated EN 13501-1 Cfl-s1, antistatic, slip-resistant (NFSI high-traction)',
				'Machine washable and safe over underfloor heating; 5-year guarantee on the main ranges',
			),
			'range'   => array(
				'title' => 'Entrance mats that work as hard as your door',
				'items' => array(
					array( 'Iron-Horse', 'The all-round commercial entrance mat: twisted nylon pile that holds 800 g of dirt and 3–4 litres of water per m², in 9 colours.' ),
					array( 'Monotone', 'The same proven performance in 11 single-tone colours, from Raven Black to Sahara and Regal Red, to match your interior.' ),
					array( 'EcoAbsorb', 'Our most absorbent mat — up to 1.1 kg of dirt and 6 litres of water per m² — made with 50% recycled yarn.' ),
					array( 'Iron-Horse Light', 'A lighter, easier-to-handle version for shops, clinics and smaller offices.' ),
					array( 'CapeCod', 'A designer entrance mat in coffee, navy and slate tones for hotels, residences and boutiques.' ),
					array( 'Turtle Mat', 'Cotton-microfibre doormats for villas and apartments, machine washable at 40 °C and guaranteed for 5 years.' ),
				),
			),
			'compare' => array(
				array(
					'title' => 'Entrance mats compared',
					'head'  => array( 'Mat', 'Thickness', 'Weight', 'Dirt held', 'Colours', 'Washable' ),
					'rows'  => array(
						array( 'Iron-Horse', '11–12 mm', '2.4 kg/m²', '800 g/m²', '9', 'Yes' ),
						array( 'Iron-Horse XL', '13–14 mm', '2.54 kg/m²', '1,000 g/m²', '4', 'Yes' ),
						array( 'Monotone', '11–12 mm', '2.4 kg/m²', '800 g/m²', '11', 'Yes' ),
						array( 'EcoAbsorb', '15 mm', '2.85 kg/m²', '1,100 g/m²', '1', 'Yes' ),
						array( 'Water Horse', '7 mm', '3.2 kg/m²', '1,000 g/m²', '2', 'No' ),
					),
				),
			),
			'faqs'    => array(
				array( 'How much dirt can an entrance mat really hold?', 'A good nylon entrance mat holds around 800 g of dirt and 3–4 litres of water per square metre before it needs cleaning; our EcoAbsorb holds up to 1.1 kg and 6 litres. That is why we size mats so visitors take at least three or four steps on them.' ),
			),
		),

		'outdoor-scraper-mats'      => array(
			'specs'   => array(
				'Brush Mat: needle-punch polypropylene, about 13.5 mm thick, suitable for outdoor use',
				'Rolls 200 × 2,000 cm; plain mats up to 360 × 3,000 cm, logo mats up to 200 × 700 cm',
				'Colours: natural, red, blue, black, anthracite, brown and grey',
				'Rubber scraper options: ring, hollow, stud and ramp-edged rubber mats',
				'Grass-like Jet-Print Outdoor and Green-Horse surfaces for printed or green outdoor mats',
				'Drains water and stays put in sun, sand and rain',
			),
			'range'   => array(
				'title' => 'Scraper mats for outside the door',
				'items' => array(
					array( 'Brush Mat', 'Stiff needle-punch fibres scrape sand and grit off shoes; can carry a logo and fits entrance wells.' ),
					array( 'Jet-Print Outdoor', 'A grass-like printed mat on a ribbed rubber backing — your logo outside, in all weather.' ),
					array( 'Green-Horse', 'A green, artificial-grass style mat for terraces, gardens and villa entrances.' ),
					array( 'Rubber scraper mats', 'Hollow, ring and stud rubber mats that let sand and water fall through — ideal for loading bays and gates.' ),
				),
			),
		),

		'coir-doormats'             => array(
			'specs'   => array(
				'100% natural brush coir on a PVC backing',
				'Widths 100 and 200 cm, lengths up to 8 m; larger areas joined invisibly',
				'Heights 14, 17 and 30 mm — the 30 mm version suits deep entrance wells',
				'8 colours: natural, red, russet, blue, black, brown, grey and green',
				'Logo or lettering inlaid up to 180 × 400 cm',
				'Optional ramped rubber edging for loose-laid mats',
			),
			'faqs'    => array(
				array( 'Can coir mats have a logo?', 'Yes. Your logo or address can be inlaid in coloured coir, with a design area of up to 180 × 400 cm. Coir logo mats are popular for villas, hotels and heritage-style entrances.' ),
			),
		),

		'recessed-entrance-matting' => array(
			'specs'   => array(
				'G-Guard aluminium grid systems at 12 mm (low profile) or 17 mm (standard) height, up to 4 m wide',
				'Kleen-System aluminium and stainless-steel profiles at 12, 17 or 22 mm, any width',
				'Infills: fibre-bonded or ribbed polypropylene, natural coir, ribbed rubber, recycled rubber, brush and scrub',
				'Grid colours: black, light, medium and dark gold, gun grey and bronze anodising',
				'Slip ratings R11 to R13; fire rating Cfl-s1; wheelchair and trolley friendly',
				'Long-life aluminium base with replaceable infills; frames and ramps for surface-laid installs',
			),
			'range'   => array(
				'title' => 'Fitted entrance systems',
				'items' => array(
					array( 'G-Guard', 'Heavy-duty anodised aluminium grids made largely from recycled aluminium, with six infill types for any traffic level.' ),
					array( 'G-Guard Eco', 'The same grid with sustainable goat-hair infills in over 60 colours — for entrances that should look as good as they work.' ),
					array( 'Kleen-System', 'Aluminium profiles on stainless-steel wire with brush, ribbed rubber or scrub inserts; mix them for scraping and drying zones.' ),
					array( 'Concourse & Matrix tiles', 'Interlocking 50 × 100 cm rubber tiles, 12 or 18 mm deep, for large or irregular entrances.' ),
				),
			),
			'compare' => array(
				array(
					'title' => 'Choosing the right infill',
					'head'  => array( 'Infill', 'What it does best', 'Suggested for' ),
					'rows'  => array(
						array( 'Fibre-bonded polypropylene', 'Dense, hard-wearing wiper for extreme foot traffic', 'Heavy foot and trolley traffic, sports venues' ),
						array( 'Multi-directional polypropylene', 'Very durable, low upkeep, absorbs sound', 'Public buildings, corporate entrances' ),
						array( 'Natural coir', 'Highly absorbent with a warm, natural look', 'Libraries, retail, schools' ),
						array( 'Ribbed rubber', 'Hygienic, non-slip and economical', 'Heavy traffic, sports venues' ),
						array( 'Recycled rubber', 'Absorbs impact, sound and vibration', 'Hospitals, schools, sports entrances' ),
						array( 'Brushed ribbed polypropylene', 'Strong scraping plus extra absorbency', 'Education, public buildings, retail' ),
					),
				),
			),
		),

		'anti-fatigue-mats'         => array(
			'specs'   => array(
				'Cushioned core of 11.5 mm nitrile foam bonded to a phthalate-free nitrile rubber base',
				'Kleen-Comfort Office: 4 mm carpet top (recycled polyester/polypropylene), 18 mm total',
				'Kleen-Comfort Safety: industrial rubber top, 15 mm total, for production lines',
				'Standard sizes 55 × 78, 60 × 85, 85 × 150 and 85 × 285 cm; longer runs made to order',
				'Slip resistance NFSI high-traction; fire rating Efl-s1; lightfast and colourfast',
				'Bevelled edges to reduce trips and let trolleys roll over',
			),
			'range'   => array(
				'title' => 'Comfort for every standing job',
				'items' => array(
					array( 'Kleen-Comfort Office', 'A carpet-topped comfort mat for receptions, service counters and standing desks.' ),
					array( 'Kleen-Comfort Safety', 'An industrial rubber top over cushioned foam for production lines and workshops.' ),
					array( 'Kleen-Comfort Soft', 'Extra-soft cushioning for cashiers, pharmacies and anyone standing all day.' ),
					array( 'Kleen-Comfort Design', 'Patterned comfort mats that look at home in shops, kitchens and open-plan offices.' ),
				),
			),
		),

		'anti-slip-wet-area-mats'   => array(
			'specs'   => array(
				'Kleen-Wave: 100% recyclable PET with anti-microbial properties',
				'Barefoot slip resistance DIN 51097 classes A, B and C',
				'9 mm thick, 58 × 100 cm modular tiles that link to cover any area',
				'Works from −35 °C to +70 °C; low emissions (TVOC class A+)',
				'Colours: dark blue, pastel blue and grey',
				'PVC ablution mats in zigzag and S-type profiles for mosques and washrooms',
			),
			'range'   => array(
				'title' => 'Safer underfoot when it is wet',
				'items' => array(
					array( 'Kleen-Wave', 'An open, drainage-friendly wet-area mat for pool decks, showers and changing rooms.' ),
					array( 'Zigzag PVC mats', 'Hollow PVC matting that lets water drain away — ideal for ablution areas and washrooms.' ),
					array( 'S-type PVC mats', 'Soft, flexible PVC tiles for barefoot areas around pools and spas.' ),
				),
			),
		),

		'lift-day-mats'             => array(
			'specs'   => array(
				'Printed lift mats in any size, with your logo, the day of the week or a welcome message',
				'Kleen-Cooler mat: 85 × 60 cm, high-twist nylon on nitrile rubber, holds 3–4 litres of water per m²',
				'Cooler mats in 9 colours, from slate and navy to scarlet and taupe',
				'Fire rating Cfl-s1; slim 11–12 mm profile that lift doors clear easily',
			),
		),

		'car-floor-mats'            => array(
			'specs'   => array(
				'Parking mats: printed monofilament nylon on nitrile rubber, about 8 mm thick',
				'Standard parking sizes 115 × 250 and 150 × 300 cm; custom sizes on request',
				'Optional drainage holes; fire rating Bfl-s1',
				'Car protector sets and boot liners for showrooms and premium vehicles',
				'Your logo, number plate or showroom branding printed in full colour',
			),
			'range'   => array(
				'title' => 'Mats for cars and car spaces',
				'items' => array(
					array( 'Parking mats', 'Printed mats for villa garages, showrooms and private parking bays — they keep oil and sand off the floor.' ),
					array( 'Car protector sets', 'Protective floor and boot mats for car dealers, valets and workshops.' ),
					array( 'Custom-fit car mats', 'Made-to-measure mats for your car model, with optional logo or initials.' ),
				),
			),
		),
	);
}
