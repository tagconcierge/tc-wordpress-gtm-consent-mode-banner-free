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

        $deferEvents = '1' === $this->settingsUtil->getOption('defer_events') ? 'true' : 'false';
        $consentEventName = $this->settingsUtil->getOption('consent_event_name') ? $this->settingsUtil->getOption('consent_event_name') : '';
        $requireConsentBeforeGtmLoad = '1' === $this->settingsUtil->getOption('gtm_snippet_consent_required') ? 'true' : 'false';

        $script = <<<EOD
window.dataLayer = window.dataLayer || [];

window.gtmCookies = {
    config: {
        gtmLoaded: false,
        deferEvents: {$deferEvents},
        consentEventName: '{$consentEventName}',
        requireConsentBeforeGtmLoad: {$requireConsentBeforeGtmLoad}
    },
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
    },
    grantConsent: function() {
        dataLayer.push({'event': gtmCookies.config.consentEventName});
        gtmCookies.setConsent(true);
        gtmCookies.emit('consentGranted');
    },
    repushEvents: function() {
        let events = [];
        while (0 < gtmCookies.deferredEvents.length) {
            events.push(gtmCookies.deferredEvents.pop());
        }
        
        while (0 < events.length) {
            dataLayer.originPush(events.pop());
        }
    }
};

gtmCookies.on('gtmLoaded', function() {
    gtmCookies.config.gtmLoaded = true;
});
EOD;
        $this->outputUtil->addInlineScript($script, false);


        if ('1' === $this->settingsUtil->getOption('defer_events')) {
            $script = <<<EOD
dataLayer.originPush = dataLayer.push;
dataLayer.push = function(item) {
    const isGtmObject = 'object' === typeof item && item.hasOwnProperty('event') && item['event'].includes('gtm.');
    let result = null;
    
    if (true === gtmCookies.config.requireConsentBeforeGtmLoad && true === isGtmObject) {
        result = dataLayer.originPush(item);
    } else if (true === gtmCookies.config.requireConsentBeforeGtmLoad && false === gtmCookies.config.gtmLoaded) {
        result = gtmCookies.deferredEvents.push(item);
    } else if (true === gtmCookies.isConsentGranted()) {
        result = dataLayer.originPush(item);
    } else {
        result = gtmCookies.deferredEvents.push(item);
    }
        
    if (true === isGtmObject && 'gtm.load' === item['event']) {
        gtmCookies.emit('gtmLoaded');
    }
    
    return result;
};

gtmCookies.on('consentGranted', function() {    
    if (true === gtmCookies.config.requireConsentBeforeGtmLoad) {
        gtmCookies.on('gtmLoaded', function() {
            gtmCookies.repushEvents();
        });
    } else {
        gtmCookies.repushEvents();
    }
});

if (true === gtmCookies.config.requireConsentBeforeGtmLoad) {
    gtmCookies.on('gtmLoaded', function() {
        gtmCookies.repushEvents();
    });
}
EOD;
            $this->outputUtil->addInlineScript($script, false);
        }

        $requireConsentBeforeGtmLoad = '1' === $this->settingsUtil->getOption('gtm_snippet_consent_required');

        if (false === empty($headSnippet = $this->settingsUtil->getOption('gtm_snippet_head'))) {
            if (true === $requireConsentBeforeGtmLoad) {
                $gtmId = $this->extractGtmId($headSnippet);
                $script = <<<EOD
const gtmCookiesLoadHeadSnippet = function() {
    if (false === gtmCookies.isConsentGranted()) {
        setTimeout(gtmCookiesLoadHeadSnippet, 1000);
        return;
    }
    
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{$gtmId}');
};

gtmCookiesLoadHeadSnippet();
EOD;
                $this->outputUtil->addInlineScript($script);
            } else {
                add_action( 'wp_head', [$this, 'headSnippet'], 0 );
            }
        }

        if (false === empty($bodySnippet = $this->settingsUtil->getOption('gtm_snippet_body'))) {
            if (true === $requireConsentBeforeGtmLoad) {
                $gtmId = $this->extractGtmId($bodySnippet);
                $script = <<<EOD
const gtmCookiesLoadBodySnippet = function() {
    if (false === gtmCookies.isConsentGranted()) {
        setTimeout(gtmCookiesLoadBodySnippet, 1000);
        return;
    }

    const noscript = document.createElement('noscript');
    const iframe = document.createElement('iframe');
    iframe.setAttribute('src', 'https://www.googletagmanager.com/ns.html?id={$gtmId}');
    iframe.setAttribute('height', '0');
    iframe.setAttribute('width', '0');
    iframe.setAttribute('style', 'display:none;');
    noscript.appendChild(iframe);
    document.body.appendChild(noscript);
};

gtmCookiesLoadBodySnippet();
EOD;
                $this->outputUtil->addInlineScript($script);
            } else {
                add_action( 'wp_body_open', [$this, 'bodySnippet'], 0 );
            }
        }
    }

    public function headSnippet($headSnippet): void
    {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_head'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }

    public function bodySnippet(): void
    {
        echo filter_var($this->settingsUtil->getOption('gtm_snippet_body'), FILTER_FLAG_STRIP_BACKTICK) . "\n";
    }

    private function extractGtmId(string $string): string
    {
        preg_match('/GTM-[a-zA-Z0-9]+/', $string, $matches);

        return false === empty($matches) ? $matches[0] : '';
    }
}
