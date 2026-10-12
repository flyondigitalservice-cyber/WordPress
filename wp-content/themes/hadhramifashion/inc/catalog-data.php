<?php
/**
 * Starter catalogue for Hadhrami Fashion.
 *
 * Every image is a Google Drive file ID from the shared brand folder. The theme
 * renders them straight from Drive until they are imported into the Media
 * Library (Appearance → Hadhrami Setup → Import images).
 *
 * Names, prices and copy are starting points — edit them freely in
 * Products → All Products after the theme is activated.
 *
 * @package HadhramiFashion
 */

defined( 'ABSPATH' ) || exit;

return array(

	/*
	 * Brand imagery used across the home page and collection tiles.
	 */
	'brand' => array(
		'hero'           => '1SJw3UiKOamMArOn2PAbxKOHtg4kfpHXh',
		'hero_alt'       => '1y8O6LazxG25PXrprpMZMyfoQhR2hTHZq',
		'legacy'         => '1gkKQPZMUlsq8GTz3Ang8sSeo1r7VGH1R',
		'atelier'        => '1TzHLhebplWAt4bpmnKTUjr6YLnAUZTgJ',
		'tile_suits'     => '1FEcvDhyoTQqjTRtnrZLA4RFYps6BQCOp',
		'tile_shirts'    => '11fTTl70F8g3fak30Rqm03Y7i_S986jQw',
		'tile_cloth'     => '1sJ6DzyITOOa6QYDNEpTo2h_HxGJFppn0',
		'tile_occasion'  => '1dm1Z9Ww03Vup_Ayp_S_DY6UsiT7aQcYJ',
		'tile_wedding'   => '1GSGKmD7N5SXj5qbdhsHxPAKC8tNRCRvc',
		'tile_essential' => '19DdZsdeC5cYLG8UNTFM6Wmv85lRV4CvB',
	),

	/*
	 * Suits — made to measure, delivered within 24 hours.
	 * `image` is the model shot, `swatch` the cloth it is cut from.
	 */
	'suits'  => array(
		array( 'The Mukalla', 'Two-piece suit · Super 120s wool', 34900, '1Dm52hTvCrUpe6k23EsYolohU7ixWYEkI', '1daq6WjUeISFefKJqb2-8TQW-yLpViWU5', 'Italian wool, Super 120s', 'business' ),
		array( 'The Tarim', 'Two-piece suit · Wool flannel', 36900, '1a9Hi0a2fol3Y03kNQO9v2mjAaeXMA1Nw', '1QnrGh5Pb3wNKR1nIqU4-C7oWz7DMK470', 'Brushed wool flannel', 'business' ),
		array( 'The Shibam', 'Two-piece suit · Pinstripe worsted', 38900, '1sjyc7E4XyUlSZ1OIPciLT64V-ZkQJZZJ', '1Zvcj0zd2xwo9jsTr9t8Gt-5NCPf7XoN2', 'Worsted wool, chalk stripe', 'business' ),
		array( 'The Seiyun', 'Two-piece suit · Tropical wool', 32900, '1xBFQ1mfHLgFOeYkDtd40FOcPWloQ99dZ', '1RzVV4EjMjaJEy8bRHzbdaY8nuyFaQC0m', 'Lightweight tropical wool', 'summer' ),
		array( 'The Shihr', 'Two-piece suit · Wool-silk', 44900, '1-f9gfsyuX7Bl8FTVvPs1TGJjs57IgOtS', '1N7jtqaAm1mpyuyHNHArYxVrAkb35Qc_8', 'Wool & silk blend', 'occasion' ),
		array( 'The Dawan', 'Two-piece suit · Birdseye', 34900, '1GT36TSgZOUXBBBzVuaIhtLHLShwTjQsd', '1ekz7j8cc4QKOoS9FLvdgzcmpS9Z7gTw5', 'Birdseye worsted', 'business' ),
		array( 'The Qatn', 'Two-piece suit · Linen-wool', 31900, '17sF7UkAiShOw7jCdR0GOiRIBZ3kVkb41', '1Z89CmG7aoQjotbT7Tk-7kRRXwxuwxxBH', 'Linen & wool, open weave', 'summer' ),
		array( 'The Ghayl', 'Two-piece suit · Sharkskin', 36900, '1xyPDhbb06BM18-QOvkAAJHCTljcGZeBf', '18xadfUQhzmX9kATw5r3XTpNLXiVYRvjE', 'Sharkskin worsted', 'business' ),
		array( 'The Hajjarain', 'Three-piece suit · Herringbone', 42900, '1_pHarKYOAMUhz54aR95XqQnQZUcNd-4w', '1hKkIHp92tdsXbt37ITof8d1-Dta6F6Z_', 'Herringbone wool', 'occasion' ),
		array( 'The Wadi', 'Two-piece suit · Hopsack', 33900, '1qRh0Q5CiqJ6L_ZZKirlSik3QUGV7As6R', '1pFS41EJhLZgL7avH775ZOQMnqYiLY3C-', 'Breathable hopsack', 'summer' ),
		array( 'The Sultan', 'Dinner suit · Satin peak lapel', 46900, '1jnu-ONBpOVZR9spyvGekHC4wOZPylzJJ', '1HyK3_bijEewkzpFCw3I94jfgq1G7rWsX', 'Barathea wool, silk satin facings', 'wedding' ),
		array( 'The Nikah', 'Three-piece wedding suit', 49900, '1564yWBcNMKooj6nXgjKSlyOMz9H5pE_j', '1-0IRbAo5-lmj8YL_bOyPeB9gOpWkWt9M', 'Wool & mohair', 'wedding' ),
		array( 'The Mahra', 'Two-piece suit · Glen check', 37900, '1mnRS0Bye4ouwJ68GDCmPh33EidCq-VEB', '1FIimjmThQkPPPNQ4vZh60ClY0baSQE8D', 'Prince of Wales check', 'business' ),
		array( 'The Socotra', 'Two-piece suit · Stretch wool', 35900, '1Zl779jJJWH-6LVso_Y33ZLVM58tXkHm9', '1IGcs-7c-yGsS123NWAboC-J6hC2NGu0G', 'Performance stretch wool', 'business' ),
		array( 'The Aden', 'Two-piece suit · Windowpane', 38900, '1jVG4A7OgDhCxfQdKw3ZCP27Vxxt246Pi', '1SERAvz5VGv7bWnlLw0jMmfobcOlTJZtc', 'Windowpane worsted', 'occasion' ),
		array( 'The Barakah', 'Three-piece suit · Velvet-touch', 47900, '1GuviaBlm7-JoT5nv8450A1dJULfeUBYs', '13fUsZRVFifBJvsxWIlTcXClOAu19pCYE', 'Brushed wool, velvet hand', 'wedding' ),
		array( 'The Sana', 'Two-piece suit · Nailhead', 35900, '1RDdwgFlisxAe2NG0rna88-0WgcqGsBoK', '1SFaWw4w9W47mCGy2CcAnZZu_UXO7bJQQ', 'Nailhead worsted', 'business' ),
		array( 'The Bayt', 'Two-piece suit · Twill', 33900, '10ljIhmcAAS5lLsf03Zj2HfR6KtxYh8Tm', '1ZvJVzM50IYrpsl1Y8pfAjMDaU8aOJSrl', 'Fine wool twill', 'business' ),
		array( 'The Dar', 'Two-piece suit · Travel wool', 34900, '1UIh85lFomoTmAmLxdUx9Mcuqa_itGQcH', '1jOuVH80V_Cp_xgrpZ3EJNtdB12Fg4VoK', 'Crease-resistant travel wool', 'summer' ),
		array( 'The Amanah', 'Two-piece suit · Solaro', 39900, '1aFq5qDY9AYLbevbPXD6HGYtAPDwaXf7G', '1jSR0muKNWd4jEjJtNzBerP68BLZPXGSQ', 'Solaro sun-cloth', 'occasion' ),
		array( 'The Riyan', 'Two-piece suit · Basketweave', 34900, '1j4ZSibZ_fMYu6iZyN44rVH0IAoOIm5u-', '1q88j5WP9_mq6ZUJv0yIRF55dXpV26c0M', 'Basketweave wool', 'summer' ),
		array( 'The Nasab', 'Three-piece suit · Flannel', 43900, '16j9IjXiDCLhkS-d0fUcdxPNsDKQFFl3Q', '1hxdeBdXJKugUpW-xVF9YingY1y8EIEPH', 'Heritage flannel', 'occasion' ),
		array( 'The Diwan', 'Two-piece suit · Pick-and-pick', 36900, '1H-lvpz-e8kwIxytu6kP8LyeQ_HtuAsiB', '1BkzzRnUy-Lj-6nXZwfUGYYopJ_bMpMpq', 'Pick-and-pick worsted', 'business' ),
		array( 'The Qasr', 'Dinner suit · Shawl lapel', 48900, '1_NODSGVDZWOrBfRKtCBMM2PfmhSvvaFG', '1zdqGak5-moM41n7Fp6EOMU_Zj8Tn9ZhA', 'Barathea, grosgrain shawl', 'wedding' ),
		array( 'The Mirbat', 'Two-piece suit · Melange', 32900, '1_E51hvYQU7IBs8VHNvj6cWps9V9Eys-T', '1vb6bp5KPJxY4O9g7n53Q5c5PeZgFSRsS', 'Wool melange', 'business' ),
		array( 'The Hadramawt', 'Two-piece suit · Signature cloth', 45900, '12r2ueXYR1c73vyAY1CGeWsSFv5wQEgbw', '1GGFFfXKYt5K4C1VdMiY7fJGj_kcu_RHm', 'House signature wool', 'occasion' ),
		array( 'The Lahj', 'Two-piece suit · Seersucker', 31900, '1X1gigGBHoAS8h1XdfQNtdzjOGKK_0knJ', '1o_xMbPmazxLPGvG64osR7hSPRsequp2g', 'Wool seersucker', 'summer' ),
		array( 'The Zabid', 'Two-piece suit · Cavalry twill', 37900, '1nFalKyuTqy9UAUAdJivPSjuMYJ4sFtjc', '1HNU0rGG-tchZIHoiAJVqK1yrAiugf5qI', 'Cavalry twill', 'business' ),
		array( 'The Marib', 'Three-piece suit · Tweed', 44900, '1QU5PwrXx1OYmlPbzLg5FaCCZaCnJGTrb', '1vV03Bs38f2DoBPqPlHGLXCpiv4512Y7b', 'Soft Shetland tweed', 'occasion' ),
		array( 'The Bab', 'Two-piece suit · Sateen', 36900, '1J_oQMSFNhprCWa-hZDlJONsRW_PBgsNT', '1JqqsPwSud5uuB2FSVeuh-5uPsR-dQ2z5', 'Wool sateen', 'occasion' ),
		array( 'The Rubat', 'Two-piece suit · Fresco', 35900, '15z6J87rXp7mB1jqTZfTSry6DLANmA59V', '1Qz0DV6m1o0hvgJclgt1Tvk24NaFtVrMt', 'High-twist fresco', 'summer' ),
		array( 'The Maqam', 'Two-piece suit · Textured weave', 36900, '1NbNTW3pwSooJQvveIzXFlqkzTnF31N8U', '1LzLmECANzo4Io8p4Ipepsmmydkb8_snr', 'Textured dobby weave', 'business' ),
		array( 'The Najm', 'Two-piece suit · Mohair blend', 41900, '1JrA_iuOafcCjngaEUUTHpSYuAzRwpb5b', '12fLe7fJ8ZM3YcbaJgjG1vQO3BR_0ORrg', 'Kid mohair & wool', 'wedding' ),
		array( 'The Fajr', 'Two-piece suit · Light flannel', 34900, '1dRdEQOEeCpv5MzaKda3tA61hGYYAfX3N', '1zufX4XSoMWL20ZwU7jCIhMOElQB8Y8td', 'Light flannel', 'business' ),
		array( 'The Layl', 'Two-piece suit · Midnight worsted', 39900, '1vM1PnJV0JY1HB_JqJhJT0vGqtMv_Wk_q', '1ZD3Ea145ozshaXJbn_GqYiIYjdZGAQ9l', 'Midnight worsted', 'wedding' ),
		array( 'The Sahil', 'Two-piece suit · Linen', 29900, '1oyXXVDHqxWqLhvn7by2V3IVFhatjYYsN', '1jdw-zeirgX17PVqJuiw2uh9xX2ZJoLS-', 'Irish linen', 'summer' ),
		array( 'The Ward', 'Two-piece suit · Twill stretch', 33900, '1txmNCJmpQI4xo0MCaG9u38FT4IoU48-0', '1rNTR--EKxZZ29uTZGt2bGk6q8m3NtuhL', 'Stretch twill', 'business' ),
		array( 'The Kahf', 'Two-piece suit · Heavy worsted', 37900, '18hLoBwBGly0yEoH4C19_qS3cy8dtqM4G', '1p4qxdO-FoURTzUnUfWxx8k2fm4D6hb8R', 'Heavy worsted', 'business' ),
	),

	/*
	 * Shirts — ready to wear (2-hour delivery) or made to measure.
	 * array( name, subtitle, price, image, colour, express-eligible ).
	 */
	'shirts' => array(
		array( 'The Mukalla White', 'Poplin shirt · Spread collar', 3490, '1yk5h04sIJ26ldgDyAmLh1OefnL0c1MGt', 'White', true ),
		array( 'The Classic White', 'Twill shirt · Cutaway collar', 3690, '1OCGsetzA5icpqMSOACx0_IoyiSJcj5O_', 'White', true ),
		array( 'The Royal Oxford White', 'Royal oxford · Button-down', 3990, '1m8rlNeNooINoWC-nWO333RydmmADcDmv', 'White', true ),
		array( 'The Herringbone White', 'Herringbone shirt · Spread collar', 4290, '1pl4S_Z4Nu-4AclIaClukFrl-XuQbBLx5', 'White', true ),
		array( 'The Stretch White', 'Cotton stretch · Semi-spread', 3790, '1ofZHnFU3ZAIlcsve0GX0VsyFCtxpfEdB', 'White', true ),
		array( 'The Ivory', 'Fine poplin · Spread collar', 3690, '1c5O7oSPp5Wv4zJf2noL2nak-G0Pn-Q7A', 'Ivory', true ),
		array( 'The Sky Blue', 'Poplin shirt · Spread collar', 3490, '1aCILaZd0Zcx1zH2KZHq3tZ4Pkmm1GhGS', 'Blue', true ),
		array( 'The Pale Blue', 'Twill shirt · Cutaway collar', 3690, '1iHxAY2NtcdDCr6Bz0rJs8UpEZcAFU-w2', 'Blue', true ),
		array( 'The Classic Blue', 'Poplin shirt · Spread collar', 3490, '14taFtenALGhaV_diO7pt4NSLoepkdJ3y', 'Blue', true ),
		array( 'The Deep Blue', 'Poplin shirt · Semi-spread', 3690, '1FjTTjhlefKFZGb-uCoWUAq42a2-dU12x', 'Blue', true ),
		array( 'The Herringbone Blue', 'Herringbone shirt', 4290, '1a-v8EPtLbECvFhvp233ZPO-3w0JKuknq', 'Blue', false ),
		array( 'The Stone Blue', 'Herringbone shirt', 4290, '1ikdSkmvpdSo5o3IWN-fxs1pqWm9WOArK', 'Blue', false ),
		array( 'The Dobby Blue', 'Dobby weave shirt', 4490, '1lGg6i-gavTBHZ6IeGNoiMoOoscX2An8E', 'Blue', false ),
		array( 'The Harlow Blue', 'Textured cotton shirt', 4290, '1Txc_8YLu9Kzv7hj-LAhHH8orQec0IgOb', 'Blue', false ),
		array( 'The Navy', 'Poplin shirt · Spread collar', 3690, '15fmI0xVedPXvNyZwZhaZfYEnuOid_WDQ', 'Navy', true ),
		array( 'The Black', 'Poplin shirt · Spread collar', 3690, '1S8V_y62vUwI1xE-nFAOZ3Drc_sGCFu9Z', 'Black', true ),
		array( 'The Black Herringbone', 'Herringbone shirt', 4290, '17eatxYG-Y3srQzB756H_VVJYagmY7mE1', 'Black', false ),
		array( 'The Pink', 'Poplin shirt · Spread collar', 3490, '1sEruUcTiqoIysYFcuQwHSrXZVdbzhKMl', 'Pink', true ),
		array( 'The Lavender', 'Poplin shirt · Spread collar', 3490, '11SvG4FYlpxaiQsKaMZWZ8EUVuuyMggvc', 'Lavender', true ),
		array( 'The Plum', 'Twill shirt · Cutaway collar', 3890, '1z-elB-DFbwJ5TCbR2ftYepcZ-gyjJycJ', 'Plum', false ),
		array( 'The Sage Herringbone', 'Herringbone shirt', 4290, '1YLGlQ-qlq1JY6D9b910GoLkyHROJhC5f', 'Green', false ),
		array( 'The Olive Flannel', 'Stretch flannel shirt', 4690, '11gOBUklx_9MA_fWZLHgqFX3lRaJURjK4', 'Olive', false ),
		array( 'The Blue Stripe', 'Bengal stripe shirt', 3990, '1xGj7np9VsHrUDXRXjKHCYKaotPbwXgbB', 'Stripe', true ),
		array( 'The Burgundy Stripe', 'Bengal stripe shirt', 3990, '1Mj4TP98lV6p4XFgdHgd34GiUNc5RCYhx', 'Stripe', false ),
		array( 'The Navy Stripe', 'Fine stripe shirt', 3990, '1WTv9k30PNJD7cLjjNUGYQGsBghjbLdlQ', 'Stripe', false ),
		array( 'The Vintage Stripe', 'Cotton stretch stripe', 4190, '1pwPjmpHyQfljK8K54kVTfN3tEdOfExUI', 'Stripe', false ),
		array( 'The Blue Pinstripe', 'Pinstripe shirt', 3990, '1TOYxA9sVLw1Ui_PPkLYN5lXzB4AK_irK', 'Stripe', true ),
		array( 'The Navy Pinstripe', 'Pinstripe shirt', 3990, '1ZpTWzKfxD2GDBjTUed5cZPaj_J-fJZ44', 'Stripe', false ),
		array( 'The Grey Pinstripe', 'Pinstripe shirt', 3990, '1K6i-9K5BsU7Ih_fVJiUq-IIgcbNclgS5', 'Stripe', false ),
		array( 'The Black Pinstripe', 'Pinstripe shirt', 3990, '15IL-waMc-xh4bScoyvjG18nnVAkPC22y', 'Stripe', false ),
		array( 'The Blue Gingham', 'Gingham check shirt', 3790, '1BYXJtCmIJmcQapItIGTzyZQz8smNqmvc', 'Check', true ),
		array( 'The Navy Gingham', 'Gingham check shirt', 3790, '1hy0vtW9BmcAjt1EJXnFJjY199GlluKkQ', 'Check', false ),
		array( 'The Grey Gingham', 'Gingham check shirt', 3790, '18s17c77ELA8zu1SIuk_qegqCZOg2orGz', 'Check', false ),
		array( 'The Navy Windowpane', 'Windowpane check shirt', 3990, '11aRpGt3FpIQHrXT4hoMYZeEV9Bbgtlco', 'Check', false ),
		array( 'The Pink Microcheck', 'Non-iron microcheck', 4190, '1XO68ICaEzh84OMeW9LPPR1DBoaPkjlVL', 'Check', false ),
		array( 'The Navy Microcheck', 'Non-iron microcheck', 4190, '1dL_dKG0u3oiMXkxkreGnJEGRLa1pdrGr', 'Check', false ),
		array( 'The Burgundy Dobby', 'Dobby twill shirt', 4290, '105IxiG1F4hLmo_ipLYz1-8nOdmmo8kfh', 'Burgundy', false ),
		array( 'The Micro Floral', 'Printed cotton shirt', 4490, '1erVsmaraeKB9cSCuQNviYBC8tR8MFX_O', 'Print', false ),
		array( 'The Ink Print', 'Printed cotton shirt', 4490, '1wk9E_OgCKb3ySrIzXzEzn1BITiDBbJuG', 'Print', false ),
		array( 'The Basketweave', 'Basketweave shirt', 4190, '18zTJu5O-RYPKxrsURBAF8Z4wxW-LFO_G', 'White', false ),
		array( 'The Peak Stitch', 'Textured shirt', 4190, '1QBUiFBUt1MWpKo6gv56sFTb17kwwb3FN', 'White', false ),
		array( 'Tuxedo · Plain Front', 'Evening shirt', 4990, '1AxtyFbvBVbyvGn_IkWMmFeIETr8A4hHs', 'Evening', true ),
		array( 'Tuxedo · Round Front', 'Evening shirt', 5290, '1I8enkzKdooJG9o2T4yezawYYsUhCLLBK', 'Evening', false ),
		array( 'Tuxedo · Square Front', 'Evening shirt', 5290, '1s7bTjP_kaXTxMr3eOJL1hPoM-SMx2nDV', 'Evening', false ),
		array( 'Tuxedo · Black', 'Evening shirt', 4990, '1ILqaZGeKZrAY4ntzqvJKtrD4ba0MGdZ8', 'Evening', true ),
		array( 'Tuxedo · Light Blue', 'Evening shirt', 4990, '1oXpaiox0gW80Y5gX8cZe_P9AAOybdr7g', 'Evening', false ),
	),
);
