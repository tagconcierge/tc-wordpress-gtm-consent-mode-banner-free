<?php

namespace TagConcierge\GtmConsentModeBanner\Util;

class OutputUtil
{
    private $inlineScripts = ['header' => [], 'footer' => []];
    private $externalScripts = ['header' => [], 'footer' => []];

    public function __construct()
    {
        add_action( 'wp_head', [$this, 'wpHead'], 0 );
        add_action( 'wp_footer', [$this, 'wpFooter'], 1 );
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

    public function wpFooter(): void
    {
        foreach ($this->externalScripts['footer'] as $script) {
            echo '<script type="text/javascript" src="'
                . esc_url($script)
                . '"></script>' . "\n";
        }

        if (0 === count($this->inlineScripts['footer'])) {
            echo '<!-- gtm-cookies no-footer-scripts -->';
            return;
        }
        echo '<script type="text/javascript" data-gtm-cookies-scripts>';
        foreach ($this->inlineScripts['footer'] as $script) {
            echo filter_var($script, FILTER_FLAG_STRIP_BACKTICK) . "\n";
        }
        echo "</script>\n";
    }
}
