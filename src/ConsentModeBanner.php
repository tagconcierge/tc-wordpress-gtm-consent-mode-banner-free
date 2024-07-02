<?php

namespace TagConcierge\ConsentModeBannerFree;

use TagConcierge\ConsentModeBannerFree\DependencyInjection\Container;

class ConsentModeBanner {

	const SNAKE_CASE_NAMESPACE = 'gtm_consent_mode_banner';

	const SPINE_CASE_NAMESPACE = 'gtm-consent-mode-banner';

	private $container;

	public function initialize() {
		$this->container = new Container();
	}
}
