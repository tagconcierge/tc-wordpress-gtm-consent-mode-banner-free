<?php

namespace TagConcierge\GtmConsentModeBanner;

use TagConcierge\GtmConsentModeBanner\DependencyInjection\Container;

class GtmConsentModeBanner
{
    public const SNAKE_CASE_NAMESPACE = 'gtm_consent_mode_banner';

    public const SPINE_CASE_NAMESPACE = 'gtm-consent-mode-banner';

    private $container;

    public function initialize()
    {
        $this->container = new Container();
    }
}
