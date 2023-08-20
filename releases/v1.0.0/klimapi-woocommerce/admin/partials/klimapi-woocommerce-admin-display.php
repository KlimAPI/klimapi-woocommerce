<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://klimapi.com
 * @since      1.0.0
 *
 * @package    Klimapi_Woocommerce
 * @subpackage Klimapi_Woocommerce/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo __('KlimAPI - CO2 Compensation for WooCommerce Shops', 'klimapi'); ?></h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields('klimapi_option_group');
        do_settings_sections('klimapi-admin');
        submit_button();
        ?>
    </form>
</div>
