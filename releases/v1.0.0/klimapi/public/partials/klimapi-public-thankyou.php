<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://klimapi.com
 * @since      1.0.0
 *
 * @package    Klimapi
 * @subpackage Klimapi/public/partials
 */

if (!wc()->session) {
    return;
}

$order = wc_get_order($order_id);

if (!$order) {
    return;
}

$order = $order->get_meta('klimapi_order');

if (!$order) {
    return;
}

?>
<div class="klimapi-wrapper-thankyou">
    <div class="klimapi-project">
        <div
            class="klimapi-project-header klimapi-project-selected klimapi-project-disabled"
            style="background-image: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 70%, rgba(0,0,0,1) 100%), url(<?php echo $order->project->images[0] ?>)"
        >
            <div class="klimapi-project-header-title">
                <?php echo $order->project->title ?>
            </div>
            <div class="klimapi-project-header-co2">
                <span class="klimapi-project-header-co2-kg"><?php echo number_format($order->kg_amount, 0, ",", ".") ?></span> kg CO<sub>2</sub>e • <span class="klimapi-project-header-co2-price"><?php echo number_format($order->price, 2, ",", ".") ?></span> €
            </div>
        </div>
    </div>
    <div class="klimapi-thankyou">
        <h2 class="klimapi-thankyou-title"><?php echo __('The earth thanks you!', 'klimapi'); ?></h2>
        <p class="klimapi-thankyou-text">
            <?php printf(wp_kses(__('Thank you for your contribution to a more sustainable planet. Your compensation of <b>%1$s kg CO<sub>2</sub>e</b> will benefit the <b>%2$s</b> project. ', 'klimapi'), array(  'b' => array( ), 'sub' => array( ) )), number_format($order->kg_amount, 0, ",", "."), $order->project->title); ?><br />
            <a class="klimapi-thankyou-link" href="<?php echo KLIMAPI_CERTIFICATES_ENDPOINT ?>/<?php echo strtolower(substr(get_bloginfo('language'), 0, 2)) ?>/<?php echo $order->order_id ?>" target="_blank"><?php echo __('Open Certificate', 'klimapi'); ?> ➜</a>
        </p>

        <div data-klimapi-hint>
            Powered by <a href="https://klimapi.com/" target="_blank">KlimAPI</a>
        </div>
    </div>
</div>