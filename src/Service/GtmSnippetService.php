<?php

namespace TagConcierge\GtmCookiesFree\Service;

use TagConcierge\GtmCookiesFree\Util\OutputUtil;
use TagConcierge\GtmCookiesFree\Util\SettingsUtil;

class GtmSnippetService
{
    private $settingsUtil;

    private $outputUtil;

    public function __construct(SettingsUtil $settingsUtil, OutputUtil $outputUtil)
    {
        $this->settingsUtil = $settingsUtil;
        $this->outputUtil = $outputUtil;

        $this->initialize();
    }

    private function initialize(): void
    {
        if ('1' === $this->settingsUtil->getOption('disabled')) {
            return;
        }

        if ('1' === $this->settingsUtil->getOption('defer_events')) {
            $script = <<<EOD
window.dataLayer = window.dataLayer || [];

EOD;
            $this->outputUtil->addInlineScript($script, false);
        }

        if (false === empty($this->settingsUtil->getOption('gtm_snippet_head'))) {
            add_action( 'wp_head', [$this, 'headSnippet'], 0 );
        }

        if (false === empty($this->settingsUtil->getOption('gtm_snippet_body'))) {
            add_action( 'wp_body_open', [$this, 'bodySnippet'], 0 );
        }
    }

    public function headSnippet(): void
    {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_head'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }

    public function bodySnippet(): void
    {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_body'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }
}
