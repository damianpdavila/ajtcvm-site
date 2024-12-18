<?php

/*
Plugin Name:  Moventis EDD Shipping Address
Version: 1.1
Description: Adds shipping address to EDD downloads which contain a specific category.
Author: Damian Davila
Author URI: https://www.moventisusa.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moventis
Based on EDD documentation:
https://docs.easydigitaldownloads.com/article/953-custom-checkout-fields
https://library.easydigitaldownloads.com/checkout/force-account-creation-by-category-or-tag.html
*/

/**
 * Adding a address field to the checkout screen
 *
 * Covers:
 * 
 * Adding a  field to the checkout
 * Making the field required
 * Setting an error when the field is not filled out
 * Storing the field into the payment meta
 * Adding the field to the "view order details" screen
 * Adding a new {ship_address} email tag so you can display the field in the email notifications (standard purchase receipt or admin notification)
 */

/**
 * Check for any physically shipped products in cart.
 *
 * @return bool
 */
function mov_edd_shipping_address_required() {
    // Download categories defined for physical shipping -- TODO: add plugin settings panel in dashboard to define instead of hardcoding here.
	$categories_to_search = array( 'print-journal' );

	$cart = edd_get_cart_contents();

	if ( $cart ) {
		// create an array with all our download IDs
		$download_ids = wp_list_pluck( $cart, 'id' );

		if ( $download_ids ) {
			// loop through IDs and check if they belong to any of the categories
			foreach ( $download_ids as $id ) {
				if ( has_term( $categories_to_search, 'download_category', $id ) ) {
					$ret = (bool) true;
				}
			}
		}
	}

	return $ret;
}

/**
 * Display shipping address field at checkout
 */
function mov_edd_display_checkout_fields() {
    if ( mov_edd_shipping_address_required()) {
?>
    <p id="edd-ship-addr-wrap">
        <label class="edd-label" for="edd-ship-addr">Shipping Address
        <?php if ( edd_field_is_required( 'edd_ship_addr' ) ) : ?>
			<span class="edd-required-indicator">*</span>
		<?php endif; ?>
        </label>
        <span class="edd-description"> Enter your full shipping address where we should ship the printed journal. </span>
        <textarea class="edd-input" type="text" name="edd_ship_addr" id="edd-ship-addr" rows="5"></textarea>
    </p>
    
    <?php
    }
}
add_action( 'edd_purchase_form_user_info_fields', 'mov_edd_display_checkout_fields' );

/**
 * Make shipping address required
 */
function mov_edd_required_checkout_fields( $required_fields ) {
    if ( mov_edd_shipping_address_required()) {

        $required_fields['edd_ship_addr'] = array(
            'error_id' => 'invalid_ship_addr',
            'error_message' => 'Please enter a valid Shipping address'
        );
    }

    return $required_fields;
}
add_filter( 'edd_purchase_form_required_fields', 'mov_edd_required_checkout_fields' );

/**
 * Set error if shipping address field is empty
 */
function mov_edd_validate_checkout_fields( $valid_data, $data ) {
    if ( mov_edd_shipping_address_required()) {

        if ( empty( $data['edd_ship_addr'] ) ) {
            edd_set_error( 'invalid_ship_addr', 'Please enter your shipping address.' );
        }
    }
}
add_action( 'edd_checkout_error_checks', 'mov_edd_validate_checkout_fields', 10, 2 );

/**
 * Store the custom field data into EDD's order mtea
 */
function mov_edd_store_custom_fields( $order_id, $order_data ) {

	if ( 0 !== did_action('edd_pre_process_purchase') ) {
		$ship_addr = isset( $_POST['edd_ship_addr'] ) ? sanitize_textarea_field( $_POST['edd_ship_addr'] ) : '';
		edd_add_order_meta( $order_id, 'ship_addr', $ship_addr );
	}

}
add_action( 'edd_built_order', 'mov_edd_store_custom_fields', 10, 2 );



/**
 * Add the shipping address to the "View Order Details" page, new approach stored in order meta
 */
function mov_edd_view_order_details( $order_id ) {
	$ship_addr = edd_get_order_meta( $order_id, 'ship_addr', true );
    if ( isset($ship_addr) && strlen(trim($ship_addr)) > 0 ) {
?>
    <div class="column-container">    
        <div class="column" style="border: 2px solid red; padding-left: 1em;"> 
            <p><strong>NOTE:  This order contains a printed journal and must be shipped:</strong><br/>
            </p>
        </div>

        <div class="column"> 
            <p><strong>Shipping Address: </strong><br/>
                <?php echo $ship_addr; ?> 
            </p>
        </div>
    
    </div>
<?php
    }
}
add_action( 'edd_payment_view_details', 'mov_edd_view_order_details', 10, 1 );

/**
 * Add the shipping address to the "View Order Details" page, old approach stored in payment meta
 */
function mov_edd_view_order_details_old( $payment_meta, $user_info ) {
    if ( isset($payment_meta['ship_addr']) && strlen(trim($payment_meta['ship_addr'])) > 0 ) {
        $ship_addr = $payment_meta['ship_addr'];
?>
    
    <div class="column-container">    
        <div class="column" style="border: 2px solid red; padding-left: 1em;"> 
            <p><strong>NOTE:  This order contains a printed journal and must be shipped:</strong><br/>
            </p>
        </div>

        <div class="column"> 
            <p><strong>Shipping Address: </strong><br/>
                <?php echo $ship_addr; ?> 
            </p>
        </div>
    
    </div>

    <?php 
    }
}
add_action( 'edd_payment_personal_details_list', 'mov_edd_view_order_details_old', 10, 2 );

/**
 * Add a {ship_addr} tag for use in either the purchase receipt email or admin notification emails
 */
function mov_edd_add_email_tag() {

    edd_add_email_tag( 'ship_addr', 'Customer\'s shipping address', 'mov_edd_email_tag_ship_addr' );
}
add_action( 'edd_add_email_tags', 'mov_edd_add_email_tag' );

/**
 * The {ship_addr} email tag
 */
// function mov_edd_email_tag_ship_addr( $payment_id ) {
//     $payment_meta = edd_get_payment_meta( $payment_id );
//     return $payment_meta['ship_addr'];
//     //$edd_shipping_info = "Meta from EDD: " . print_r($payment_meta, true);
//     //$payment = new EDD_Payment( $payment_id );
//     //$payment_info = print_r($payment, true);
//     //$wp_meta = $payment->get_meta( '_edd_payment_meta', true );
//     //$wp_shipping_info = "Meta from WP: " . print_r($wp_meta, true);
//     //return "Payment id: " . print_r($payment_id, true) . "<br>\n" . $payment_info . "<br>\n" . $edd_shipping_info . "<br>\n" . $wp_shipping_info;
// }

/**
 * The {ship_addr} email tag
 */
function mov_edd_email_tag_ship_addr( $payment_id ) {
	$ship_addr = edd_get_order_meta( $payment_id, 'ship_addr', true );
	return $ship_addr;
}

?>