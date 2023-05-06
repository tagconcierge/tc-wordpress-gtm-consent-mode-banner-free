<?php

namespace TagConcierge\GtmCookiesFree\DependencyInjection;

use TagConcierge\GtmCookiesFree\Service\SettingsService;
use TagConcierge\GtmCookiesFree\Util\SettingsUtil;

class Container
{
    private $settingsService;

    private $settingsUtil;

    public function __construct()
    {
        $this->settingsUtil = new SettingsUtil();
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
}
