<?php

namespace TagConcierge\GtmConsentModeBanner\DependencyInjection;

use TagConcierge\GtmConsentModeBanner\Service\GtmConsentModeService;
use TagConcierge\GtmConsentModeBanner\Service\SettingsService;
use TagConcierge\GtmConsentModeBanner\Util\OutputUtil;
use TagConcierge\GtmConsentModeBanner\Util\SettingsUtil;

class Container
{
    private $outputUtil;

    private $gtmSnippetService;

    private $settingsService;

    private $settingsUtil;

    public function __construct()
    {
        $this->outputUtil = new OutputUtil();
        $this->settingsUtil = new SettingsUtil();
        $this->gtmSnippetService = new GtmConsentModeService($this->settingsUtil, $this->outputUtil);
        $this->settingsService = new SettingsService($this->settingsUtil);
    }

    public function getSettingsUtil(): SettingsUtil
    {
        return $this->settingsUtil;
    }

    public function getSettingsService(): SettingsService
    {
        return $this->settingsService;
    }

    public function getOutputUtil(): OutputUtil
    {
        return $this->outputUtil;
    }

    public function getGtmSnippetService(): GtmSnippetService
    {
        return $this->gtmSnippetService;
    }
}
