<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://klimapi.com
 * @since      1.0.0
 *
 * @package    Klimapi_Woocommerce
 * @subpackage Klimapi_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Klimapi_Woocommerce
 * @subpackage Klimapi_Woocommerce/admin
 * @author     KlimAPI Team <integrations@klimapi.com>
 */
class Klimapi_Woocommerce_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    static function klimapi_sanitize($input)
    {
        $sanitary_values = array();

        if (isset($input['api_key'])) {
            $sanitary_values['api_key'] = sanitize_text_field($input['api_key']);
        }

        if (isset($input['shop_key'])) {
            $sanitary_values['shop_key'] = sanitize_text_field($input['shop_key']);
        }

        return $sanitary_values;
    }

    static function klimapi_section_info()
    {
    }

    static function api_key_callback()
    {
        $klimapi_options = get_option('klimapi_options');

        printf(
            '<input class="regular-text" type="password" name="klimapi_options[api_key]" id="api_key" value="%s" required>',
            isset($klimapi_options['api_key']) ? esc_attr($klimapi_options['api_key']) : ''
        );
    }

    static function shop_key_callback()
    {
        $klimapi_options = get_option('klimapi_options');

        printf(
            '<input class="regular-text" type="text" name="klimapi_options[shop_key]" id="shop_key" value="%s" required>',
            isset($klimapi_options['shop_key']) ? esc_attr($klimapi_options['shop_key']) : ''
        );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Klimapi_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Klimapi_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/klimapi-woocommerce-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Klimapi_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Klimapi_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/klimapi-woocommerce-admin.js', array( 'jquery' ), $this->version, false);
    }

    /**
     * Add the Admin menu
     *
     * @since    1.0.0
     */
    public function klimapi_settings_menu()
    {

        $klimapi_icon = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICAgICAgICAiaHR0cDovL3d3dy53My5vcmcvVFIvMjAwMS9SRUMtU1ZHLTIwMDEwOTA0L0RURC9zdmcxMC5kdGQiPgo8c3ZnIHZlcnNpb249IjEuMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIgogICAgIHdpZHRoPSI2NjguMDAwMDAwcHQiIGhlaWdodD0iNjY4LjAwMDAwMHB0IiB2aWV3Qm94PSIwIDAgNjY4LjAwMDAwMCA2NjguMDAwMDAwIgogICAgIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgogICAgPG1ldGFkYXRhPgogICAgICAgIENyZWF0ZWQgYnkgcG90cmFjZSAxLjE0LCB3cml0dGVuIGJ5IFBldGVyIFNlbGluZ2VyIDIwMDEtMjAxNwogICAgPC9tZXRhZGF0YT4KICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLDY2OC4wMDAwMDApIHNjYWxlKDAuMTAwMDAwLC0wLjEwMDAwMCkiCiAgICAgICBmaWxsPSIjYTdhYWFkIiBzdHJva2U9Im5vbmUiPgogICAgICAgIDxwYXRoIGQ9Ik0xOTk4IDYxNDcgbC0yMzYgLTQxMiAxNDgxIC0zIDE0ODIgLTMgNzMwIC0xMjY2IGM0MDIgLTY5NiA3MzUKLTEyNzIgNzQwIC0xMjc5IDcgLTEwIDczIDk2IDI0OCA0MDAgbDIzOCA0MTMgLTM1IDU5IGMtMjAgMzMgLTM1MiA2MDggLTc0MAoxMjc5IGwtNzA0IDEyMjAgLTE0ODMgMiAtMTQ4NCAyIC0yMzcgLTQxMnoiLz4KICAgICAgICA8cGF0aCBkPSJNNzQ1IDM5NzMgYy0zOTAgLTY3NiAtNzE3IC0xMjQyIC03MjcgLTEyNTggbC0xOCAtMzEgNzQ4IC0xMjY5IDc0OAotMTI3MCA1NzkgLTYgYzMxOSAtNCA5ODMgLTkgMTQ3NSAtMTMgbDg5NSAtNyA3MTAgMTIzMSBjMzkxIDY3OCA3MTYgMTI0NCA3MjQKMTI1OSAxMyAyNyAtNiA2MCAtNzI0IDEyODIgLTQwNiA2OTAgLTc0MyAxMjYwIC03NDkgMTI2NyAtMTAgMTAgLTIzNCAxNAotMTA2NyAyMiAtNTgwIDUgLTEyNDEgMTIgLTE0NzAgMTUgbC00MTUgNiAtNzA5IC0xMjI4eiBtMTg0MyAtMTI0MCBsMiAtMTE4MwotMjk1IDAgLTI5NSAwIDAgMTE3OCBjMCA2NDggMyAxMTgyIDcgMTE4NSA0IDQgMTM1IDYgMjkyIDUgbDI4NiAtMyAzIC0xMTgyegptMTQ0NSAxMTgwIGMzIC03IC0xMjY3IC0xNDgxIC0xMzMxIC0xNTQ2IGwtMjIgLTIxIDAgMzc1IDAgMzc1IDE5MSAyMzUgYzIzNQoyODcgNDIzIDUxMyA0NjMgNTU3IGwzMCAzMiAzMzIgMCBjMTgzIDAgMzM1IC0zIDMzNyAtN3ogbS0zNjYgLTE2NzggYzIxMQotMzQ3IDM5MSAtNjQyIDM5OSAtNjU3IGwxNSAtMjggLTM0MCAwIC0zMzkgMCAtMjUyIDQzOCBjLTEzOSAyNDAgLTI1NCA0NDAKLTI1NiA0NDMgLTUgOSAzNjkgNDQwIDM3OSA0MzYgNSAtMSAxODIgLTI4NiAzOTQgLTYzMnoiLz4KICAgIDwvZz4KPC9zdmc+Cg==";

        add_menu_page(
            $this->plugin_name,
            "KlimAPI",
            'manage_options',
            plugin_dir_path(__FILE__) . 'partials/klimapi-woocommerce-admin-display.php',
            null,
            $klimapi_icon,
            99
        );
    }

    /**
     * Add the Admin menu settings
     *
     * @since    1.0.0
     */
    public function klimapi_settings_init()
    {

        register_setting(
            'klimapi_option_group',
            'klimapi_options',
            array( __CLASS__, 'klimapi_sanitize' )
        );

        add_settings_section(
            'klimapi_setting_section',
            __('Authentication', 'klimapi'),
            array( __CLASS__, 'klimapi_section_info' ),
            'klimapi-admin',
            [
                'after_section' => '<p>' . __('Please enter your API Key and Shop Ident. You can find them in your KlimAPI account.', 'klimapi') . '</p>',
            ]
        );

        add_settings_field(
            'api_key',
            __('API Key', 'klimapi'),
            array( __CLASS__, 'api_key_callback' ),
            'klimapi-admin',
            'klimapi_setting_section'
        );

        add_settings_field(
            'shop_key',
            __('Shop Ident', 'klimapi'),
            array( __CLASS__, 'shop_key_callback' ),
            'klimapi-admin',
            'klimapi_setting_section'
        );
    }

    public function refresh_settings($value)
    {

        if (is_string($value['api_key']) && is_string($value['shop_key']) && preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $value['shop_key']) === 1) {
            //Upload products
            $products = wc_get_products(array( 'limit' => - 1 ));

            foreach (array_chunk($products, 30) as $products_chunk) {
                $this->upload_products($products_chunk, $value['api_key'], $value['shop_key']);
            }
        }

        return $value;
    }

    public function upload_products($products, $api_key, $shop_key)
    {
        wp_remote_post(KLIMAPI_WOOCOMMERCE_API_ENDPOINT . '/v1/shops/' . $shop_key . '/sync/bulk', [
            'blocking' => false,
            'headers'  => [
                'content-type' => 'application/json',
                'X-API-KEY'    => $api_key,
            ],
            'body'     => json_encode(array_map(function ($product) {
                return [
                    'product_id'  => $product->get_id(),
                    'name'        => $product->get_name(),
                    'description' => $product->get_short_description(),
                    'price'       => wc_format_decimal($product->get_price(), 2),

                    'categories' => wc_get_object_terms($product->get_id(), 'product_cat', 'name'),
                    'tags'       => wc_get_object_terms($product->get_id(), 'product_tag', 'name'),

                    'weight'      => $product->get_weight() ? wc_format_decimal($product->get_weight(), 2) : null,
                    'weight_unit' => $product->get_weight() ? get_option('woocommerce_weight_unit') : null,

                    'made_in' => $product->get_meta('country_of_origin'),
                ];
            }, $products)),
        ]);
    }
}
