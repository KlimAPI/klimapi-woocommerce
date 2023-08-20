<?php

/**
 * @link              https://klimapi.com
 * @since             1.0.0
 * @package           Klimapi_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       KlimAPI WooCommerce
 * Plugin URI:        https://klimapi.com/resources/plugins/woocommerce
 * Description:       Calculate and compensate the unavoidable CO2 emissions of your products and services together with your customers.
 * Version:           1.0.0
 * Author:            KlimAPI Team
 * Author URI:        https://klimapi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       klimapi-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('KLIMAPI_WOOCOMMERCE_VERSION', '1.0.0');


define('KLIMAPI_WOOCOMMERCE_API_ENDPOINT', 'https://api.klimapi.com');
define('KLIMAPI_WOOCOMMERCE_CERTIFICATES_ENDPOINT', 'https://certificates.klimahelden.eu');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-klimapi-woocommerce-activator.php
 */
function activate_klimapi_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-klimapi-woocommerce-activator.php';
    Klimapi_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-klimapi-woocommerce-deactivator.php
 */
function deactivate_klimapi_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-klimapi-woocommerce-deactivator.php';
    Klimapi_Woocommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_klimapi_woocommerce');
register_deactivation_hook(__FILE__, 'deactivate_klimapi_woocommerce');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-klimapi-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_klimapi_woocommerce()
{

    $plugin = new Klimapi_Woocommerce();
    $plugin->run();
}
run_klimapi_woocommerce();
