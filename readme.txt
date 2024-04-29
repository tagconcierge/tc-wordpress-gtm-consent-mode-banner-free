=== Consent Mode Banner ===
Contributors: Tag Concierge
Tags: google tag manager, consent mode, cookies banner, privacy, consent management, google ads
Requires at least: 5.1.0
Tested up to: 6.5.2
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lightweight (~3kB) Consent/Cookies Banner compatible with Google Consent Mode (GTM & Google Tags).

== Description ==

Consent Mode Banner is a lightweight (~3 kB), user-friendly WordPress plugin designed to integrate Google Consent Mode seamlessly into your website. It offers a simple and efficient way for website owners to comply with cookie and privacy regulations like GDPR and CCPA, by enabling visitors to select their cookie preferences easily.


## Features

- **Simple Setup**: Integrate with Google Tag and Google Tag Manager with minimal configuration.
- **Customizable Consent Banner**: Tailor the appearance and message of your consent banner.
- **User Preference Control**: Allows users to specify their consent for different types of cookies (e.g., necessary, analytics, marketing).
- **Compliance with Privacy** Laws: Helps in making your website compliant with GDPR, CCPA, and other privacy regulations.
- **Lightweight and Fast**: Designed to be non-intrusive and does not affect your website's load time.

## Banner UI

This plugin relies on open-source, lightweight Consent Banner UI available on GitHub:

**[tagconcierge/consent-banner-js](https://github.com/tagconcierge/consent-banner-js)**

== Installation ==

1. Upload or install `Consent Mode Banner` plugin from WordPress plugins directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. That's it! Consent State will be pushed to DataLayer adhering to Consent Mode API
4. Use plugin settings to customise the content and consent settings.
5. Optionally use plugin settings to install GTM snippets

== Frequently Asked Questions ==

= Do I need to configure anything in GTM to enable consent mode? =

Once your Consent Banner is deployed the Consent Mode will be provided and will work with Google Tags
(GA4 and Google Ads) out-of-the-box. For other integrations you need to review your GTM tags to ensure they have correct consent checks settings.
You can use Consent Overview screen to quickly review and update tags. [Read more here](https://docs.tagconcierge.com/article/59-how-to-configure-gtm-consent-mode)

= Is it compatible with direct GA4 or Google Ads integration? =

Yes, both direct usage of Google Tags and Google Tag Manager are supported. The plugin sends standard Consent Mode v2 parameters that will be respected by those tags.

= Is it compatible with Facebook Pixel? =

It can be compatible if Facebook Pixel is implemented via Google Tag Manager and correct consent settings are applied in GTM workspace. [Read more here](https://docs.tagconcierge.com/article/59-how-to-configure-gtm-consent-mode)

= How to inject GTM tracking snippet? =

By default this plugin push consent information to the GTM DataLayer object that can be installed by other plugins or directly in the theme code.
It can also embed GTM snippets, go to settings to configure it.



== Screenshots ==

1. Consent Banner in action
2. Consent Banner displayed as modal
3. Consent Banner displayed as bar without "wall"
4. Consent Banner settings screen with all consent types listed
5. Consent Mode State Preview in Google Tag Assistant
6. Consent Banner WP Admin basic settings
7. Consent Banner WP Admin banner content settings
8. Consent Banner WP Admin consent types settings


== Changelog ==

= 1.0.0 =

* Initial version
