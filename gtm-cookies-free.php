<?php
/**
 * Plugin Name: GTM Cookies Free
 * Plugin URI:  https://wordpress.org/plugins/gtm-cookies-free
 * Description: TODO_DESC
 * Version:     0.0.1
 * Author:      Tag Concierge
 * Author URI:  https://tagconcierge.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gtm-cookies-free
 * Domain Path: /languages
 */

use TagConcierge\GtmCookiesFree\GtmCookiesFree;

require __DIR__ . '/vendor/autoload.php';

$plugin = new GtmCookiesFree();
$plugin->initialize();
