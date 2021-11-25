<?php
/**
 * Plugin Name:       WooCommerce Birthday 
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Birthday Reminder for WooCommerce Customers.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Praveen Dias 
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       woo_bday 
 * Domain Path:       /languages
 */

/**
 * Useful Definitions.
 */
define('WOO_BDAY_DIR', __DIR__);
define('WOO_BDAY_URI', plugin_dir_url(__FILE__));

/**
 * Die if not defined ABSPATH.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Let's add WooCommerce birthday reminders.
 */
require_once( WOO_BDAY_DIR . '/inc/class-woo-bday.php');
$woo_bday = new Woo_Bday();

register_activation_hook(  __FILE__, array($woo_bday, 'activation') );
register_deactivation_hook(  __FILE__, array($woo_bday, 'deactivation') );