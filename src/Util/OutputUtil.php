<?php

namespace TagConcierge\GtmConsentModeBanner\Util;

class OutputUtil
{
    private $inlineScripts = ['header' => [], 'footer' => []];
    private $externalScripts = ['header' => [], 'footer' => []];

    public function __construct()
    {
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
