<?php

namespace TagConcierge\GtmCookiesFree;

use TagConcierge\GtmCookiesFree\DependencyInjection\Container;

class GtmCookiesFree
{
    public const SNAKE_CASE_NAMESPACE = 'gtm_cookies_free';

    public const SPINE_CASE_NAMESPACE = 'gtm-cookies-free';

    private $container;

    public function initialize()
    {
        $this->container = new Container();
    }
}
