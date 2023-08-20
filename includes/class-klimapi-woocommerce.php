<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://klimapi.com
 * @since      1.0.0
 *
 * @package    Klimapi_Woocommerce
 * @subpackage Klimapi_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Klimapi_Woocommerce
 * @subpackage Klimapi_Woocommerce/includes
 * @author     KlimAPI Team <integrations@klimapi.com>
 */
class Klimapi_Woocommerce
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Klimapi_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('KLIMAPI_WOOCOMMERCE_VERSION')) {
            $this->version = KLIMAPI_WOOCOMMERCE_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'klimapi-woocommerce';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->plugin_updater();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Klimapi_Woocommerce_Loader. Orchestrates the hooks of the plugin.
     * - Klimapi_Woocommerce_i18n. Defines internationalization functionality.
     * - Klimapi_Woocommerce_Admin. Defines all hooks for the admin area.
     * - Klimapi_Woocommerce_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-klimapi-woocommerce-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-klimapi-woocommerce-i18n.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-klimapi-woocommerce-updater.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-klimapi-woocommerce-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-klimapi-woocommerce-public.php';

        $this->loader = new Klimapi_Woocommerce_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Klimapi_Woocommerce_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Klimapi_Woocommerce_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Klimapi_Woocommerce_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function plugin_updater()
    {

        new Klimapi_Woocommerce_Updater($this->get_version());
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Klimapi_Woocommerce_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action('admin_menu', $plugin_admin, 'klimapi_settings_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'klimapi_settings_init');

        $this->loader->add_action('pre_update_option_klimapi_options', $plugin_admin, 'refresh_settings');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Klimapi_Woocommerce_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        //This hook is used to calculate the compensation and add it as a fee to the cart when activated
        $this->loader->add_action('woocommerce_cart_calculate_fees', $plugin_public, 'calculate_cart');
        $this->loader->add_action('woocommerce_thankyou', $plugin_public, 'process_offset', 1);
        $this->loader->add_action('woocommerce_thankyou', $plugin_public, 'klimapi_thankyou', 10);

        $this->loader->add_action('wp_ajax_nopriv_klimapi_woocommerce_get_projects', $plugin_public, 'get_projects');
        $this->loader->add_action('wp_ajax_klimapi_woocommerce_get_projects', $plugin_public, 'get_projects');

        $this->loader->add_action('wp_ajax_nopriv_klimapi_woocommerce_select_project', $plugin_public, 'select_project');
        $this->loader->add_action('wp_ajax_klimapi_woocommerce_select_project', $plugin_public, 'select_project');

        //Shortcode to display the compensation widget
        $this->loader->add_shortcode('klimapi_widget', $plugin_public, 'klimapi_widget');
        //$this->loader->add_shortcode( 'klimapi_thankyou', $plugin_public, 'klimapi_thankyou' );


        $this->loader->add_filter('woocommerce_get_item_data', $plugin_public, 'show_product_emissions', 10, 2);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Klimapi_Woocommerce_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
