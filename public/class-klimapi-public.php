<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://klimapi.com
 * @since      1.0.0
 *
 * @package    Klimapi
 * @subpackage Klimapi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Klimapi
 * @subpackage Klimapi/public
 * @author     KlimAPI Team <integrations@klimapi.com>
 */
class Klimapi_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        //KlimAPI styles
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/klimapi-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {


        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/klimapi-public.js', array( 'jquery' ), $this->version, false);
        wp_localize_script($this->plugin_name, 'ajax_object', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
    }

    /**
     * TBD
     *
     * @since    1.0.0
     */
    public function klimapi_widget()
    {

        ob_start();

        include plugin_dir_path(__FILE__) . 'partials/klimapi-public-display.php';

        return ob_get_clean();
    }

    /**
     * TBD
     *
     * @since    1.0.0
     */
    public function klimapi_thankyou($order_id)
    {

        ob_start();

        include plugin_dir_path(__FILE__) . 'partials/klimapi-public-thankyou.php';

        echo ob_get_clean();
    }

    /**
     * TBD
     *
     * @since    1.0.0
     */
    public function show_product_emissions($item_data, $cart_item)
    {

        $results = wc()->session->get('klimapi_results');

        if (!isset($results)) {
            return $item_data;
        }

        //get product id
        $product_id = $cart_item['product_id'];

        $result = array_filter($results, function ($order) use ($product_id) {
            return $order->product_id == $product_id;
        });

        if (isset(array_values($result)[0]) && array_values($result)[0]->result > 0) {
            $item_data[] = array(
                'key' => "CO2 Emissionen",
                'value' => array_values($result)[0]->result . ' kg'
            );
        }

        return $item_data;
    }

    /**
     * TBD
     *
     * @since    1.0.0
     */
    public function calculate_cart()
    {

        $cart_total = wc()->cart->get_cart_contents_total();

        //Abort on empty cart
        if ($cart_total == 0) {
            return;
        }

        if (wc()->session->get('klimapi_cart_total') == $cart_total) {
            $orders = wc()->session->get('klimapi_orders');
            $order = array_values(array_filter($orders, function ($order) {
                return $order->order_id == wc()->session->get('klimapi_compensation_selected');
            }));

            if (isset($order[0])) {
                $settings = wc()->session->get('klimapi_settings');
                wc()->cart->add_fee('CO2 Kompensation (' . intval($order[0]->kg_amount) . ' kg)', $order[0]->price * ((100 - intval($settings->split)) / 100), true);
                return;
            }

            //No need to recalc and compensation is not selected
            if (wc()->session->get('klimapi_compensation_selected') == -1) {
                return;
            }

            //Lost order, continue
            print_r('same lost');
        }

        //Get api key from options
        $klimapi_options = get_option('klimapi_options');
        $api_key = $klimapi_options['api_key'];
        $shop_key = $klimapi_options['shop_key'];

        //Plugin is not configured
        if (empty($api_key) || empty($shop_key)) {
            return;
        }

        //Get all cart items
        $cart_items = wc()->cart->get_cart();

        //Map the cart items to the format required by the API
        $items = array_values(array_map(function ($item) {
            return [
                'amount' => $item['quantity'],
                'total' => $item['line_total'],

                'product' => [
                    'product_id' => $item['product_id'],
                    'name' => $item['data']->get_name(),
                    'description' => $item['data']->get_short_description(),
                    'price' => wc_format_decimal($item['data']->get_price(), 2),

                    'categories' => array_map(function ($category) {
                        return $category->name;
                    }, get_the_terms($item['product_id'], 'product_cat')),

                    'weight' => $item['data']->get_weight() ? wc_format_decimal($item['data']->get_weight(), 2) : null,
                    'weight_unit' => $item['data']->get_weight() ? get_option('woocommerce_weight_unit') : null,

                    'made_in' => $item['data']->get_meta('country_of_origin'),
                ],
            ];
        }, $cart_items));

        //call the api
        $response = wp_remote_post(KLIMAPI_API_ENDPOINT . '/v1/shops/' . $shop_key . '/cart', [
            'headers' => [
                'content-type' => 'application/json',
                'X-API-KEY' => $api_key,
                'X-LOCALE' => strtoupper(substr(get_bloginfo('language'), 0, 2)),
                'X-CURRENCY' => get_option('woocommerce_currency'),
            ],
            'body' => json_encode($items),
            'timeout' => 2,
        ]);

        //Abort on error
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
            return;
        }

        //Get the response body
        $response_body = json_decode(wp_remote_retrieve_body($response));

        //Store result to session
        wc()->session->set('klimapi_cart_total', $cart_total);
        wc()->session->set('klimapi_total', $response_body->total);
        wc()->session->set('klimapi_settings', $response_body->settings);
        wc()->session->set('klimapi_results', $response_body->results);
        wc()->session->set('klimapi_orders', $response_body->orders);

        //if not already set check for setting to add the compensation
        if ((wc()->session->get('klimapi_compensation_selected') === null && $response_body->settings->default) || wc()->session->get('klimapi_compensation_selected') !== null) {
            wc()->session->set('klimapi_compensation_selected', $response_body->orders[0]->order_id);
            wc()->cart->add_fee('CO2 Kompensation (' . intval($response_body->total) . ' kg)', $response_body->orders[0]->price * ((100 - intval($response_body->settings->split)) / 100), true);
        }
    }

    /**
     * TBD
     *
     * @since    1.0.0
     */
    public function process_offset($order_id)
    {

        if (wc()->session->get('klimapi_compensation_selected') == null || wc()->session->get('klimapi_compensation_selected') == -1) {
            return;
        }

        $order = wc_get_order($order_id);

        //check of order already has metadata
        if ($order->get_meta('klimapi_order')) {
            return;
        }

        $klimapi_orders = wc()->session->get('klimapi_orders');
        $klimapi_order = array_values(array_filter($klimapi_orders, function ($order) {
            return $order->order_id == wc()->session->get('klimapi_compensation_selected');
        }));

        //Get api key from options
        $klimapi_options = get_option('klimapi_options');
        $api_key = $klimapi_options['api_key'];
        $shop_key = $klimapi_options['shop_key'];

        //Plugin is not configured
        if (empty($api_key) || empty($shop_key)) {
            return;
        }

        //call the api
        $response = wp_remote_post(KLIMAPI_API_ENDPOINT . '/v1/shops/' . $shop_key . '/cart/' . wc()->session->get('klimapi_compensation_selected') . '/process', [
            'headers' => [
                'content-type' => 'application/json',
                'X-API-KEY' => $api_key,
                'X-LOCALE' => strtoupper(substr(get_bloginfo('language'), 0, 2)),
                'X-CURRENCY' => get_option('woocommerce_currency'),
            ],
            'body' => json_encode([
                'recipient_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'recipient_email' => $order->get_billing_email(),

                'customer_ident' => wc()->session->get_customer_id(),

                'metadata' => [
                    'woocommerce_order_id' => $order_id,
                ]
            ]),
            'timeout' => 2,
        ]);

        //Abort on error
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
            return;
        }

        //add klimapi order to order
        $order->add_meta_data('klimapi_order', $klimapi_order[0]);
        $order->save_meta_data();
    }

    /**
     * TBD
     *
     * @since    1.0.0
     */
    public function get_projects()
    {

        echo json_encode([
            'orders' => wc()->session->get('klimapi_orders'),
            'selected' => wc()->session->get('klimapi_compensation_selected'),
        ]);

        wp_die();
    }

    /**
     * TBD
     *
     * @since    1.0.0
     */
    public function select_project()
    {

        if (wc()->session->get('klimapi_compensation_selected') === $_POST['order']) { //ToDo escape
            wc()->session->set('klimapi_compensation_selected', -1);
        } else {
            wc()->session->set('klimapi_compensation_selected', $_POST['order']); //ToDo escape
        }

        echo json_encode([
            'orders' => wc()->session->get('klimapi_orders'),
            'selected' => wc()->session->get('klimapi_compensation_selected'),
        ]);

        wp_die();
    }
}
