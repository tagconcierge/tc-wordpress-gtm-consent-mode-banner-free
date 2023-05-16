<?php

namespace TagConcierge\GtmCookiesFree\Service;

use TagConcierge\GtmCookiesFree\Util\OutputUtil;
use TagConcierge\GtmCookiesFree\Util\SettingsUtil;

class GtmSnippetService
{
    private $settingsUtil;

    private $outputUtil;

    public function __construct(SettingsUtil $settingsUtil, OutputUtil $outputUtil)
    {
        $this->settingsUtil = $settingsUtil;
        $this->outputUtil = $outputUtil;

        $this->initialize();
    }

    private function initialize(): void
    {
        if ('1' === $this->settingsUtil->getOption('disabled')) {
            return;
        }
        $script = <<<EOD
window.gtmCookies = {
    deferredEvents: [],
    isConsentGranted: function () { return '1' === localStorage.getItem('GTM_COOKIES_CONSENT');},
    setConsent: function (isGranted) { return localStorage.setItem('GTM_COOKIES_CONSENT', true === isGranted ? '1' : '0'); },
    callbacks: {},
    on: function(event, callback) {
        if (false === gtmCookies.callbacks.hasOwnProperty(event)) {
            gtmCookies.callbacks[event] = [];
        }
        
        gtmCookies.callbacks[event].push(callback);
    },
    emit: function(event, data) {
        if (false === gtmCookies.callbacks.hasOwnProperty(event)) {
            return;
        }
        gtmCookies.callbacks[event].forEach(function(callback) { callback(data); });
    }
};

window.dataLayer = window.dataLayer || [];
EOD;
        $this->outputUtil->addInlineScript($script, false);


        if ('1' === $this->settingsUtil->getOption('defer_events')) {
            $consentEventName = $this->settingsUtil->getOption('consent_event_name');
            $script = <<<EOD
dataLayer.originPush = dataLayer.push;
dataLayer.push = function(item) {
    if (true === gtmCookies.isConsentGranted()) {
        return dataLayer.originPush(item);
    }
    
    const result = gtmCookies.deferredEvents.push(item);
    
    if ('object' === typeof item) {
        if (item.hasOwnProperty('event') && '{$consentEventName}' === item['event']) {
            gtmCookies.emit('consentGranted');
        }
    }
    
    return result;
};

gtmCookies.on('consentGranted', function() {
    gtmCookies.setConsent(true);
    gtmCookies.deferredEvents.forEach(function(event){
        dataLayer.originPush(event);
    });
});
EOD;
            $this->outputUtil->addInlineScript($script, false);
        }

        if (false === empty($this->settingsUtil->getOption('gtm_snippet_head'))) {
            add_action( 'wp_head', [$this, 'headSnippet'], 0 );
        }

        if (false === empty($this->settingsUtil->getOption('gtm_snippet_body'))) {
            add_action( 'wp_body_open', [$this, 'bodySnippet'], 0 );
        }
    }

    public function headSnippet(): void
    {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_head'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }

    public function bodySnippet(): void
    {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_body'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }
}
