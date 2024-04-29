<?php
/**
 * Plugin Name: Consent Mode Banner
 * Plugin URI:  https://wordpress.org/plugins/gtm-consent-mode-banner
 * Description: Lightweight (1.9 kB) Consent/Cookies Banner compatible with Google Consent Mode.
 * Version:     1.0.0
 * Author:      Tag Concierge
 * Author URI:  https://tagconcierge.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gtm-consent-mode-banner
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use TagConcierge\GtmConsentModeBanner\GtmConsentModeBanner;

require __DIR__ . '/vendor/autoload.php';

$plugin = new GtmConsentModeBanner();
$plugin->initialize();
