<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://klimapi.com
 * @since      1.0.0
 *
 * @package    Klimapi_Woocommerce
 * @subpackage Klimapi_Woocommerce/public/partials
 */

if (!wc()->session) {
    return;
}

$projects = wc()->session->get('klimapi_orders');

if (wc()->cart->get_cart_contents_total() == 0 || ! $projects) {
    return;
}

?>
<h3><?php echo __('Offset your purchase:', 'klimapi'); ?> <small><?php if (wc()->session->get('klimapi_settings')->split > 0 && wc()->session->get('klimapi_settings')->split < 100) {
    ?> (<?php printf(esc_html__('We pay %d%% of the compensation', 'klimapi'), wc()->session->get('klimapi_settings')->split); ?>)<?php
    } ?><?php if (wc()->session->get('klimapi_settings')->split == 100) {
    ?> (<?php echo __('We pay the entire compensation', 'klimapi'); ?>)<?php
    } ?></small></h3>
<div class="klimapi-wrapper">
    <?php foreach ($projects as $key => $project) { ?>
    <div class="klimapi-project <?php echo "klimapi-order-" . $key ?>" data-klimapi-order="<?php echo $project->order_id ?>">
        <div
            class="klimapi-project-header
            <?php if (wc()->session->get('klimapi_compensation_selected') === $project->order_id) {
                echo " klimapi-project-selected";
            } ?>"
            style="background-image: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 70%, rgba(0,0,0,1) 100%), url(<?php echo $project->project->images[0] ?>)"
        >
            <div class="klimapi-project-header-title">
                <?php echo $project->project->title ?>
            </div>
            <div class="klimapi-project-header-co2">
                <span class="klimapi-project-header-co2-kg"><?php echo $project->kg_amount ?></span> kg CO2e • <span class="klimapi-project-header-co2-price"><?php echo $project->price ?></span> €
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<div data-klimapi-hint>
    Powered by <a href="https://klimapi.com/" target="_blank">KlimAPI</a>
</div>