<?php

namespace TagConcierge\GtmConsentModeBanner\Service;

use TagConcierge\GtmConsentModeBanner\Util\OutputUtil;
use TagConcierge\GtmConsentModeBanner\Util\SettingsUtil;

class GtmConsentModeService
{
    private $settingsUtil;

    private $outputUtil;

    public function __construct(SettingsUtil $settingsUtil, OutputUtil $outputUtil)
    {
        $this->settingsUtil = $settingsUtil;
        $this->outputUtil = $outputUtil;
        $this->dataLayerVariableName = true === $this->eventDeferring || true === $this->requireConsentBeforeGtmLoad ? 'gtmCookieDataLayer' : 'dataLayer';

        $this->initialScripts();
        $this->bannerScripts();
    }
    private function initialScripts(): void
    {
        if ('1' === $this->settingsUtil->getOption('disabled')) {
            return;
        }

        $consentTypes = json_encode(array_reduce($this->settingsUtil->getOption('consent_types'), function($agg, $type) {
            if ('' === $type['name']) {
                return $agg;
            }
            $agg[$type['name']] = $type['default'];
            return $agg;
        }, []));

        $script = <<<EOD
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', $consentTypes);

try {
  var consentPreferences = JSON.parse(localStorage.getItem('consent_preferences'));
  if (consentPreferences !== null) {
     gtag('consent', 'update', consentPreferences);
  }
} catch (error) {}
EOD;
        $this->outputUtil->addInlineScript($script, false);
    }

    private function bannerScripts(): void
    {
        if ('1' === $this->settingsUtil->getOption('disabled')) {
            return;
        }

        $settings = array_reduce([
            'banner_display_mode',
            'banner_title',
            'banner_description',
            'banner_buttons_accept',
            'banner_buttons_settings',
            'banner_settings_title',
            'banner_settings_description',
            'banner_settings_buttons_save',
            'banner_settings_buttons_close',
        ], function($agg, $setName) {
            $agg[$setName] = $this->settingsUtil->getOption($setName);
            return $agg;
        }, []);

        $consentTypes = array_filter($this->settingsUtil->getOption('consent_types'), function ($type) {
            return $type['name'];
        });

        $config = json_encode([
            'display' => [
                'mode' => $settings['banner_display_mode'],
            ],
            'consent_types' => $consentTypes,
            'modal' => [
                'title' => $settings['banner_title'],
                'description' => $settings['banner_description'],
                'buttons' => [
                    'accept' => $settings['banner_buttons_accept'],
                    'settings' => $settings['banner_buttons_settings'],
                ]
            ],
            'settings' => [
                'title' => $settings['banner_settings_title'],
                'description' => $settings['banner_settings_description'],
                'buttons' => [
                    'save' => $settings['banner_settings_buttons_save'],
                    'close' => $settings['banner_settings_buttons_close'],
                ]
            ],
        ]);
        $script = <<<EOD
    var config = $config;
        config.styles = {
            '.button': {
                'text-decoration': 'none',
                background: 'none',
                color: '#333333',
                padding: '4px 10px',
                'border': '1px solid #000',
            },
            '#consent-banner-js-modal': {
                background: "#fff",
                padding: '10px 30px 30px',
                'box-shadow': 'rgba(0, 0, 0, 0.4) 0 0 20px'
            },
            '#consent-banner-js-modal .consent-banner-js-modal-wrapper': {
              margin: '0 auto',
              display: 'flex',
              'justify-content': 'center'
            },
            '#consent-banner-js-modal .consent-banner-js-modal-buttons': {
              'margin-top': '12px',
              'text-align': 'right'
            },
            '#consent-banner-js-modal .consent-banner-js-modal-buttons [href="#accept"]': {
                'color': 'rgb(255 255 255)',
                'border': '1px solid #083b99',
                'background-color': '#083b99'
            },
            '#consent-banner-js-modal .consent-banner-js-modal-buttons [href="#settings"]': {
                'margin-left': '10px'
            },
            '#consent-banner-js-settings .consent-banner-js-settings-buttons': {
              'margin-top': '12px',
              'text-align': 'right'
            },
            '#consent-banner-js-settings .consent-banner-js-settings-buttons [href="#save"]': {
                'color': 'rgb(255 255 255)',
                'border': '1px solid #083b99',
                'background-color': '#083b99'
            },
            '#consent-banner-js-settings .consent-banner-js-settings-buttons [href="#close"]': {
                'margin-left': '10px'
            },
            '#consent-banner-js-settings': {
              position: 'fixed',
              top: '50%',
              left: '50%',
              transform: 'translate(-50%, -50%)',
              background: '#fff',
              'box-shadow': 'rgba(0, 0, 0, 0.4) 0 0 20px',
              padding: '10px 30px 30px'
            },
            '#consent-banner-js-settings ul': {
              'list-style': 'none',
              'padding-left': 0
            },
            '#consent-banner-js-settings ul label': {
                'font-weight': 'bold',
                'font-size': '1.1em',
                'margin-left': '5px'
            },
            '#consent-banner-js-settings ul li': {
                'border-bottom': '1px solid rgba(0, 0, 0, .2)',
                'margin-bottom': '15px'
            },
            '#consent-banner-js-settings ul p': {
                'margin-left': '25px'
            }
        };
  cookiesBannerJs(
    function() {
      try {
        var consentPreferences = JSON.parse(localStorage.getItem('consent_preferences'));
        if (consentPreferences !== null) {
           gtag('consent', 'update', consentPreferences);
        }
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
  );
EOD;
        $this->outputUtil->loadExternalScript('https://public-assets.tagconcierge.com/consent-banner.min.js');
        $this->outputUtil->addInlineScript($script);
    }
}
