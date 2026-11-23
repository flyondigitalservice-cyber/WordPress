<?php
/**
 * Hand-drawn doodle library.
 *
 * Every illustration is a loose, pen-style inline SVG. Strokes use
 * currentColor so a doodle takes the colour of its parent, and the shared
 * #ras-ink filter (printed once in header.php) adds a slight wobble so the
 * lines read as drawn by hand rather than plotted.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Shared SVG <defs>: ink-wobble filter. Printed once, right after <body>. */
function rasaaya_doodle_defs() {
    echo '<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>'
        . '<filter id="ras-ink" x="-5%" y="-5%" width="110%" height="110%">'
        . '<feTurbulence type="fractalNoise" baseFrequency="0.04" numOctaves="2" seed="7" result="n"/>'
        . '<feDisplacementMap in="SourceGraphic" in2="n" scale="2.4" xChannelSelector="R" yChannelSelector="G"/>'
        . '</filter></defs></svg>';
}

/**
 * Return a doodle's SVG markup.
 *
 * @param string $name  Doodle key.
 * @param string $class Extra CSS classes.
 */
function rasaaya_doodle( $name, $class = '' ) {
    $s = 'stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"';

    $art = array(

        // Wavy double underline — sits under a word.
        'underline' => array( '0 0 300 26', 'none',
            '<path ' . $s . ' d="M4 15 C50 7 98 6 150 9 C196 11 246 8 296 5"/>'
            . '<path ' . $s . ' stroke-width="1.8" d="M24 21 C86 15 168 15 278 13"/>' ),

        // Loose oval that overshoots where it started, like a pen circling a word.
        'circle' => array( '0 0 220 90', 'none',
            '<path ' . $s . ' d="M120 7 C64 2 12 16 9 44 C6 72 74 84 128 80 C180 76 212 58 208 36 C204 13 152 4 96 11"/>' ),

        // Curly arrow with a loop, pointing right-down.
        'arrow-loop' => array( '0 0 130 90', null,
            '<path ' . $s . ' d="M6 20 C30 4 60 8 68 30 C74 47 57 57 49 45 C41 32 64 23 83 36 C98 47 108 62 116 76"/>'
            . '<path ' . $s . ' d="M100 74 L117 79 L115 61"/>' ),

        // Short swoosh arrow pointing down.
        'arrow-down' => array( '0 0 60 90', null,
            '<path ' . $s . ' d="M12 6 C40 18 46 46 30 80"/>'
            . '<path ' . $s . ' d="M18 70 L30 82 L40 67"/>' ),

        // Swoosh arrow pointing left.
        'arrow-left' => array( '0 0 110 60', null,
            '<path ' . $s . ' d="M104 40 C78 52 44 46 12 22"/>'
            . '<path ' . $s . ' d="M12 38 L10 21 L27 18"/>' ),

        // Little sparkle burst.
        'sparkle' => array( '0 0 40 40', null,
            '<path ' . $s . ' d="M20 3 C20 12 21 16 20 24 M20 28 L20 37 M3 20 C11 19 14 20 15 20 M25 20 L37 21 M8 8 L13 13 M27 27 L32 32 M32 8 L27 13 M8 32 L13 27"/>' ),

        // Hand-drawn tick.
        'tick' => array( '0 0 26 26', null,
            '<path ' . $s . ' stroke-width="3" d="M3 14 C6 16 8 19 10 22 C13 14 17 8 23 3"/>' ),

        // Small heart.
        'heart' => array( '0 0 40 36', null,
            '<path ' . $s . ' fill="#E7A2A2" fill-opacity=".55" d="M20 33 C7 24 2 15 5 9 C9 2 17 4 20 11 C22 4 30 1 34 8 C38 15 33 24 20 33 Z"/>' ),

        // Tulsi / herb sprig.
        'sprig' => array( '0 0 120 170', null,
            '<path ' . $s . ' d="M62 166 C58 132 63 104 58 74 C55 52 60 30 63 9"/>'
            . '<g fill="#9DBF8F" fill-opacity=".45">'
            . '<path ' . $s . ' d="M59 132 C40 122 24 126 14 140 C30 147 48 144 59 132 Z"/>'
            . '<path ' . $s . ' d="M60 110 C78 98 96 101 106 114 C90 122 72 121 60 110 Z"/>'
            . '<path ' . $s . ' d="M58 84 C40 72 26 74 17 86 C31 94 48 94 58 84 Z"/>'
            . '<path ' . $s . ' d="M59 62 C75 50 92 52 100 64 C86 71 70 71 59 62 Z"/>'
            . '<path ' . $s . ' d="M61 36 C48 26 36 28 30 37 C40 44 52 43 61 36 Z"/>'
            . '<path ' . $s . ' d="M62 18 C70 10 80 10 85 17 C78 23 69 23 62 18 Z"/>'
            . '</g>'
            . '<path ' . $s . ' stroke-width="1.4" d="M57 133 C44 134 30 138 20 140 M62 110 C76 110 90 112 100 114 M56 84 C44 84 30 85 22 86 M61 62 C73 62 85 63 95 64"/>' ),

        // Khal-batta (mortar & pestle) with a few spice specks.
        'mortar' => array( '0 0 150 130', null,
            '<path ' . $s . ' d="M92 56 L124 14 C128 9 135 13 131 19 L102 60"/>'
            . '<path ' . $s . ' fill="#E8D9BD" fill-opacity=".6" d="M16 60 C42 51 106 51 134 60 C106 69 42 69 16 60 Z"/>'
            . '<path ' . $s . ' d="M19 62 C21 96 46 114 75 114 C104 114 128 96 131 62"/>'
            . '<path ' . $s . ' d="M55 114 L50 123 L100 123 L95 114"/>'
            . '<path ' . $s . ' stroke-width="1.4" d="M34 84 L40 90 M44 96 L50 101 M108 86 L102 92 M98 99 L92 103"/>'
            . '<g fill="#D99A21" stroke="none"><circle cx="58" cy="44" r="2.4"/><circle cx="70" cy="38" r="1.8"/><circle cx="48" cy="36" r="1.6"/><circle cx="64" cy="28" r="1.4"/></g>' ),

        // Cutting-chai glass with steam.
        'chai' => array( '0 0 120 140', null,
            '<path ' . $s . ' d="M50 42 C42 31 58 23 50 10 M67 42 C59 29 76 22 67 7"/>'
            . '<path fill="#C98B4A" fill-opacity=".35" d="M33 74 C50 79 70 70 87 74 L82 124 C80 131 41 131 39 124 Z"/>'
            . '<path ' . $s . ' d="M29 52 L38 124 C40 131 80 131 83 124 L92 52"/>'
            . '<path ' . $s . ' d="M27 52 C50 45 70 45 94 52"/>'
            . '<path ' . $s . ' d="M33 74 C50 79 70 70 87 74"/>'
            . '<path ' . $s . ' stroke-width="1.4" d="M47 56 L51 120 M61 56 L61 121 M75 56 L71 120"/>' ),

        // Knobbly fresh haldi (turmeric) root.
        'haldi' => array( '0 0 150 80', null,
            '<path ' . $s . ' fill="#E3A21A" fill-opacity=".5" d="M10 46 C13 31 30 28 40 34 C44 21 59 19 65 30 C73 21 89 23 93 36 C106 29 124 36 132 46 C125 59 106 61 95 55 C87 66 66 66 60 57 C48 63 29 62 23 53 C17 57 10 55 10 46 Z"/>'
            . '<path ' . $s . ' stroke-width="1.3" d="M40 35 C37 42 38 50 42 55 M65 31 C62 40 63 49 66 56 M93 37 C90 44 91 51 95 55"/>'
            . '<path ' . $s . ' d="M132 46 C138 44 142 40 144 35"/>' ),

        // Masala dabba seen from above — seven little katoris of spice, with handwritten labels.
        'dabba' => array( '0 0 360 340', null,
            '<path ' . $s . ' stroke-width="2.8" fill="#EFE6D3" fill-opacity=".55" d="M180 30 C262 28 322 88 320 170 C318 252 258 310 178 308 C96 306 38 248 40 166 C42 88 102 32 186 34"/>'
            . '<path ' . $s . ' stroke-width="1.5" d="M180 44 C252 43 306 96 305 170 C304 244 250 294 179 293 C107 292 55 240 56 167 C57 98 110 46 184 46"/>'
            . '<g stroke-width="2.2">'
            . '<circle ' . $s . ' fill="#E3A21A" fill-opacity=".75" cx="180" cy="168" r="36"/>'
            . '<circle ' . $s . ' fill="#B8442C" fill-opacity=".7" cx="180" cy="88" r="32"/>'
            . '<circle ' . $s . ' fill="#8A6A3B" fill-opacity=".7" cx="249" cy="128" r="32"/>'
            . '<circle ' . $s . ' fill="#3D2B1C" fill-opacity=".75" cx="249" cy="208" r="32"/>'
            . '<circle ' . $s . ' fill="#D9A6A0" fill-opacity=".8" cx="180" cy="248" r="32"/>'
            . '<circle ' . $s . ' fill="#B7A276" fill-opacity=".75" cx="111" cy="208" r="32"/>'
            . '<circle ' . $s . ' fill="#9DBF8F" fill-opacity=".75" cx="111" cy="128" r="32"/>'
            . '</g>'
            // labels, in pen
            . '<g font-family="Caveat, cursive" font-size="24" fill="currentColor" stroke="none">'
            . '<text x="12" y="30">mirchi</text><text x="292" y="62">jeera</text><text x="318" y="252">rai</text>'
            . '<text x="226" y="334">kala namak</text><text x="0" y="300">ajwain</text><text x="0" y="96">saunf</text>'
            . '<text x="156" y="176" font-size="23" fill="#3D2B1C">haldi</text></g>'
            . '<g ' . $s . ' stroke-width="1.6">'
            . '<path d="M60 32 C90 34 120 50 152 70"/><path d="M290 66 C276 80 270 92 264 100"/>'
            . '<path d="M316 246 C300 236 288 226 276 218"/><path d="M226 322 C214 304 204 290 196 276"/>'
            . '<path d="M46 284 C66 262 80 242 90 228"/><path d="M46 100 C62 108 72 114 82 120"/></g>' ),

        // Scribbled strike-through, drawn over a word.
        'strike' => array( '0 0 300 30', 'none',
            '<path ' . $s . ' stroke-width="3.2" d="M4 18 C60 12 120 16 180 13 C220 11 262 14 296 10"/>'
            . '<path ' . $s . ' stroke-width="2.2" d="M10 22 C90 17 170 20 290 15"/>' ),

        // Loose hand-drawn X.
        'cross' => array( '0 0 40 40', null,
            '<path ' . $s . ' stroke-width="3.4" d="M7 6 C16 16 24 25 34 35 M33 5 C24 15 15 25 6 34"/>' ),

        // Sun with uneven rays.
        'sun' => array( '0 0 90 90', null,
            '<circle ' . $s . ' fill="#E5A94C" fill-opacity=".35" cx="45" cy="45" r="17"/>'
            . '<path ' . $s . ' d="M45 6 L45 18 M45 72 L46 84 M6 45 L18 44 M72 45 L84 46 M17 17 L25 25 M65 65 L73 72 M73 17 L65 25 M17 73 L25 65"/>' ),
    );

    if ( ! isset( $art[ $name ] ) ) {
        return '';
    }
    list( $viewbox, $aspect, $paths ) = $art[ $name ];
    $par = $aspect ? ' preserveAspectRatio="' . $aspect . '"' : '';

    return '<svg class="doodle doodle-' . esc_attr( $name ) . ' ' . esc_attr( $class ) . '" viewBox="' . $viewbox . '"' . $par
        . ' aria-hidden="true" focusable="false"><g fill="none" filter="url(#ras-ink)">' . $paths . '</g></svg>';
}
