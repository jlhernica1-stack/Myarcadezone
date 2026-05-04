<?php
/**
 * Renderiza un sprite estático, animado por frames o sprite sheet.
 *
 * Formatos de sprite_url:
 *   Estático:      /assets/sprites/ryu.gif
 *   Multi-frame:   /assets/sprites/ryu-1.png|/assets/sprites/ryu-2.png|...
 *   Sprite sheet:  sheet|/assets/sprites/ryu-sheet.png|5|120|160
 *                        (sheet | ruta | nº frames | ancho frame | alto frame)
 */
function render_sprite(string $sprite_url, string $alt = '', string $extra_class = ''): string {
    $sprite_url = trim($sprite_url);
    if ($sprite_url === '') return '';

    static $counter = 0;
    $uid = 'sa' . (++$counter);
    $kf  = "kf_$uid";

    // ── SPRITE SHEET ────────────────────────────────────────────
    if (str_starts_with($sprite_url, 'sheet|')) {
        $parts = explode('|', $sprite_url);
        // parts: sheet | url | frames | frameW | frameH
        $url    = trim($parts[1] ?? '');
        $frames = max(1, (int)($parts[2] ?? 1));
        $fw     = max(1, (int)($parts[3] ?? 100));
        $fh     = max(1, (int)($parts[4] ?? 100));
        $total  = $fw * $frames;
        $dur    = $frames * 120; // 120ms por frame

        $cls = trim("sprite-sheet $extra_class");
        $html  = '<div class="' . htmlspecialchars($cls) . '" id="' . $uid . '">';
        $html .= '<style>';
        $html .= "#{$uid}{width:{$fw}px;height:{$fh}px;background-image:url('" . htmlspecialchars($url) . "');background-repeat:no-repeat;background-position:0 0;animation:$kf {$dur}ms steps($frames) infinite;}";
        $html .= "@keyframes $kf{from{background-position:0 0}to{background-position:-{$total}px 0}}";
        $html .= '</style>';
        $html .= '</div>';
        return $html;
    }

    // ── MULTI-FRAME o ESTÁTICO ───────────────────────────────────
    $frames = array_values(array_filter(array_map('trim', explode('|', $sprite_url))));
    $n = count($frames);

    if ($n === 0) return '';

    if ($n === 1) {
        $cls = trim("char-sprite $extra_class");
        return '<img src="' . htmlspecialchars($frames[0]) . '" alt="' . htmlspecialchars($alt) . '" class="' . $cls . '">';
    }

    // Animación multi-frame
    $dur  = $n * 300;
    $fpct = round(100 / $n, 2);

    $html  = '<div class="sprite-anim-wrap ' . htmlspecialchars($extra_class) . '" id="' . $uid . '">';
    foreach ($frames as $i => $frame) {
        $html .= '<img src="' . htmlspecialchars($frame) . '" alt="' . htmlspecialchars($alt) . '" class="sprite-anim-frame">';
    }
    $html .= '<style>';
    $visible_end = round($fpct - 0.1, 2);
    $html .= "@keyframes $kf{0%{opacity:1}{$visible_end}%{opacity:1}{$fpct}%{opacity:0}100%{opacity:0}}";
    foreach ($frames as $i => $frame) {
        $delay = $i * 300;
        $html .= "#{$uid} .sprite-anim-frame:nth-child(" . ($i + 1) . "){animation:$kf {$dur}ms linear {$delay}ms infinite;}";
    }
    $html .= '</style>';
    $html .= '</div>';

    return $html;
}
