<?php

namespace TagConcierge\ConsentModeBannerFree\Util;

class SanitizationUtil {

    const WP_KSES_ALLOWED_HTML = [
        'a' => [
            'id' => [],
            'href' => [],
            'target' => [],
            'class' => [],
            'style' => [],
            'data-target' => [],
        ],
        'br' => [],
        'div' => [
            'id' => [],
            'style' => [],
            'class' => [],
        ],
        'p' => [
            'id' => [],
            'style' => [],
            'class' => [],
        ],
        'span' => [
            'id' => [],
            'style' => [],
            'class' => [],
            'data-section' => []
        ],
        'b' => [],
        'h3' => [
            'id' => [],
            'style' => [],
            'class' => [],
        ],
        'pre' => [
            'style' => []
        ],
        'strong' => [],
        'img' => [],
        'script' => [],
        'noscript' => [],
        'iframe' => [
            'src' => [],
            'height' => [],
            'width' => [],
            'style' => [],
        ],
    ];

    const WP_KSES_ALLOWED_PROTOCOLS = [
        'http', 'https'
    ];
}
