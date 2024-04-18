=== GTM Consent Mode Banner ===
Contributors: Tag Concierge
Tags: google tag manager, consent mode, cookies banner, privacy
Requires at least: 5.1.0
Tested up to: 6.4
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lightweight (1.9 kB) Consent/Cookies Banner compatible with GTM Consent Mode.

== Description ==

GTM Consent Mode Banner is a lightweight (1.9 kB), user-friendly WordPress plugin designed to integrate Google Tag Manager Consent Mode seamlessly into your website. It offers a simple and efficient way for website owners to comply with cookie and privacy regulations like GDPR and CCPA, by enabling visitors to select their cookie preferences easily.

## Features

- **Simple Setup**: Integrate with Google Tag Manager with minimal configuration.
- **Customizable Consent Banner**: Tailor the appearance and message of your consent banner.
- **User Preference Control**: Allows users to specify their consent for different types of cookies (e.g., necessary, analytics, marketing).
- **Compliance with Privacy** Laws: Helps in making your website compliant with GDPR, CCPA, and other privacy regulations.
- **Lightweight and Fast**: Designed to be non-intrusive and does not affect your website's load time.


== Installation ==

1. Upload or install `GTM Consent Mode Banner` plugin from WordPress plugins directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. That's it! Consent State will be pushed to DataLayer adhering to Consent Mode API
4. Use plugin settings to customise the content and consent settings.
5. Optionally use plugin settings to install GTM snippets

== Frequently Asked Questions ==

= How to inject GTM tracking snippet? =

By default this plugin push consent information to the GTM DataLayer object that can be installed by other plugins or directly in the theme code.
It can also embed GTM snippets, go to settings to configure it.

= Do I need to configure anything in GTM to enable consent mode =

Once your Consent Banner is deployed you need to review all your GTM tags to ensure they have correct consent checks settings.
You can use Consent Overview screen to quickly review and update tags. [Read more here](https://docs.tagconcierge.com/article/59-how-to-configure-gtm-consent-mode)

== Screenshots ==

1. Consent Banner in action
2. Consent Banner displayed as modal
3. Consent Banner displayed as bar without "wall"
4. Consent Banner settings screen with all consent types listed
5. Consent Banner WP Admin basic settings
6. Consent Banner WP Admin banner content settings
7. Consent Banner WP Admin GTM snippets settings
8. Consent Banner WP Admin consent types settings


== Changelog ==

= 1.0.0 =

* Initial version
