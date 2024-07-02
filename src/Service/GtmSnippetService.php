<?php

namespace TagConcierge\ConsentModeBannerFree\Service;

use TagConcierge\ConsentModeBannerFree\Util\SettingsUtil;

class GtmSnippetService {
    const PRIORITY_BEFORE_GTM = 0;
    const PRIORITY_GTM = 1;
    const PRIORITY_AFTER_GTM = 2;

    protected $settingsUtil;

    public function __construct( SettingsUtil $settingsUtil ) {
        $this->settingsUtil = $settingsUtil;

        $this->initialize();
    }

    public function initialize() {
        if ($this->settingsUtil->getOption('disabled') === '1') {
            return;
        }

        if ($this->settingsUtil->getOption('gtm_snippet_head') !== false) {
            add_action( 'wp_head', [ $this, 'headSnippet' ], self::PRIORITY_GTM );
        }

        if ($this->settingsUtil->getOption('gtm_snippet_body') !== false) {
            add_action( 'wp_body_open', [ $this, 'bodySnippet' ], self::PRIORITY_GTM );
        }
    }

    public function headSnippet() {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_head'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }

    public function bodySnippet() {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_body'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }
}