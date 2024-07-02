<?php

namespace TagConcierge\GtmConsentModeBanner\Util;

class OutputUtil
{
    private $inlineScripts = ['header' => [], 'footer' => []];
    private $externalScripts = ['header' => [], 'footer' => []];

    public function __construct()
    {
        add_action( 'wp_head', [$this, 'wpHead'], 0 );
        add_action( 'wp_enqueue_scripts', [$this, 'wpEnqueueScripts'] );
    }

    public function loadExternalScript($script, $footer = true): OutputUtil
    {
        $this->externalScripts[true === $footer ? 'footer' : 'header'][] = $script;
        return $this;
    }

    public function addInlineScript($script, $footer = true): OutputUtil
    {

        $this->inlineScripts[true === $footer ? 'footer' : 'header'][] = $script;

        return $this;
    }

    public function wpHead(): void
    {
        if (0 === count($this->inlineScripts['header'])) {
            echo '<!-- gtm-cookies no-header-scripts -->';
            return;
        }
        echo '<script type="text/javascript" data-gtm-cookies-scripts>';
        foreach ($this->inlineScripts['header'] as $script) {
            echo filter_var($script, FILTER_FLAG_STRIP_BACKTICK) . "\n";
        }
        echo "</script>\n";
    }

    public function wpEnqueueScripts(): void
    {
        foreach ($this->externalScripts['footer'] as $script) {
            wp_enqueue_script('gtm-consent-mode-banner', $script);
        }

        foreach ($this->inlineScripts['footer'] as $script) {
            wp_add_inline_script('gtm-consent-mode-banner', $script, 'after');
        }

        wp_register_style( 'gtm-consent-mode-banner', 'https://public-assets.tagconcierge.com/cookies-banner-js/1.1.0/styles/light.css' );
        wp_enqueue_style ( 'gtm-consent-mode-banner' );
    }
}
