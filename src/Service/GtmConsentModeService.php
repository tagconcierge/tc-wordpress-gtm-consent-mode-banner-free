<?php

namespace TagConcierge\ConsentModeBannerFree\Service;

use TagConcierge\ConsentModeBannerFree\Util\OutputUtil;
use TagConcierge\ConsentModeBannerFree\Util\SettingsUtil;

class GtmConsentModeService {

	private $settingsUtil;

	private $outputUtil;

	public function __construct( SettingsUtil $settingsUtil, OutputUtil $outputUtil) {
		$this->settingsUtil = $settingsUtil;
		$this->outputUtil = $outputUtil;

		$this->initialScripts();
		$this->bannerScripts();
	}
	private function initialScripts(): void {
		if ('1' === $this->settingsUtil->getOption('disabled')) {
			return;
		}

		$consentTypes = json_encode(array_reduce($this->settingsUtil->getOption('consent_types', []), function( $agg, $type) {
			if ('' === $type['name']) {
				return $agg;
			}
			$agg[$type['name']] = $type['default'];
			return $agg;
		}, []));

		$script = "window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', $consentTypes);

try {
  var consentPreferences = JSON.parse(localStorage.getItem('consent_preferences'));
  if (consentPreferences !== null) {
     gtag('consent', 'update', consentPreferences);
  }
} catch (error) {}";

		$this->outputUtil->addInlineScript($script, false);
	}

	private function bannerScripts(): void {
		if ('1' === $this->settingsUtil->getOption('disabled')) {
			return;
		}

		$settings = array_reduce([
			'banner_display_mode',
			'banner_display_wall',
			'banner_title',
			'banner_description',
			'banner_buttons_accept',
			'banner_buttons_settings',
			'banner_buttons_reject',
			'banner_settings_title',
			'banner_settings_description',
			'banner_settings_buttons_save',
			'banner_settings_buttons_close',
			'banner_settings_buttons_reject',
			'banner_settings_buttons_accept'
		], function( $agg, $setName) {
			$agg[$setName] = $this->settingsUtil->getOption($setName, '');
			return $agg;
		}, []);

		$savedConsentTypes = is_array($this->settingsUtil->getOption('consent_types'))
			? $this->settingsUtil->getOption('consent_types')
			: [];

		$consentTypes = array_filter($savedConsentTypes, function ( $type) {
			return $type['name'];
		});
		$config = json_encode([
			'display' => [
				'mode' => $settings['banner_display_mode'],
				'wall' => 1 == $settings['banner_display_wall']
			],
			'consent_types' => $consentTypes,
			'modal' => [
				'title' => $settings['banner_title'],
				'description' => nl2br($settings['banner_description']),
				'buttons' => [
					'accept' => $settings['banner_buttons_accept'],
					'settings' => $settings['banner_buttons_settings'],
					'reject' => $settings['banner_buttons_reject'],
				]
			],
			'settings' => [
				'title' => $settings['banner_settings_title'],
				'description' => nl2br($settings['banner_settings_description']),
				'buttons' => [
					'save' => $settings['banner_settings_buttons_save'],
					'close' => $settings['banner_settings_buttons_close'],
					'reject' => $settings['banner_settings_buttons_reject'],
					'accept' => $settings['banner_settings_buttons_accept']
				]
			],
		]);
		$script = "var config = $config;
  cookiesBannerJs(
    function() {
      try {
        var consentPreferences = JSON.parse(localStorage.getItem('consent_preferences'));
        return consentPreferences;
      } catch (error) {
        return null;
      }
    },
    function(consentPreferences) {
      gtag('consent', 'update', consentPreferences);
      localStorage.setItem('consent_preferences', JSON.stringify(consentPreferences));
    },
    config
  );";
		$this->outputUtil->loadExternalScript('https://public-assets.tagconcierge.com/consent-banner/1.2.3/cb.min.js');
		$this->outputUtil->addInlineScript($script);
	}
}
