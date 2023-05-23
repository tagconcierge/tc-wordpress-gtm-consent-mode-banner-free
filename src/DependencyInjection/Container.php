<?php

namespace TagConcierge\GtmCookiesFree\DependencyInjection;

use TagConcierge\GtmCookiesFree\Service\GtmSnippetService;
use TagConcierge\GtmCookiesFree\Service\SettingsService;
use TagConcierge\GtmCookiesFree\Util\OutputUtil;
use TagConcierge\GtmCookiesFree\Util\SettingsUtil;

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
        $this->gtmSnippetService = new GtmSnippetService($this->settingsUtil, $this->outputUtil);
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
