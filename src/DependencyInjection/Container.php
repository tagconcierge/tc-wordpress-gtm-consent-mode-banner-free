<?php

namespace TagConcierge\ConsentModeBannerFree\DependencyInjection;

use TagConcierge\ConsentModeBannerFree\Service\GtmConsentModeService;
use TagConcierge\ConsentModeBannerFree\Service\GtmSnippetService;
use TagConcierge\ConsentModeBannerFree\Service\SettingsService;
use TagConcierge\ConsentModeBannerFree\Util\OutputUtil;
use TagConcierge\ConsentModeBannerFree\Util\SettingsUtil;

class Container
{
    private $outputUtil;

    private $gtmSnippetService;

    private $gtmConsentModeService;

    private $settingsService;

    private $settingsUtil;

    public function __construct()
    {
        $this->outputUtil = new OutputUtil();
        $this->settingsUtil = new SettingsUtil();
        $this->gtmConsentModeService = new GtmConsentModeService($this->settingsUtil, $this->outputUtil);
        $this->gtmSnippetService = new GtmSnippetService($this->settingsUtil);
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

    public function getGtmConsentModeService(): GtmConsentModeService {
        return $this->gtmConsentModeService;
    }

    public function getGtmSnippetService(): GtmSnippetService
    {
        return $this->gtmSnippetService;
    }
}
