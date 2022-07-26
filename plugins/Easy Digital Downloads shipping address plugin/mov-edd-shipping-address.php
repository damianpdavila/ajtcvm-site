<?php

/*
Plugin Name:  Moventis EDD Shipping Address
Version: 1.0
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

	// get our cart contents
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
        <label class="edd-label" for="edd-ship-addr">Shipping Address</label>
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
 * Store the custom field data into EDD's payment meta
 */
function mov_edd_store_custom_fields( $payment_meta ) {

    if ( 0 !== did_action('edd_pre_process_purchase') ) {
        $payment_meta['ship_addr'] = isset( $_POST['edd_ship_addr'] ) ? sanitize_textarea_field( $_POST['edd_ship_addr'] ) : '';
    }

    return $payment_meta;
}
add_filter( 'edd_payment_meta', 'mov_edd_store_custom_fields');


/**
 * Add the shipping address to the "View Order Details" page
 */
function mov_edd_view_order_details( $payment_meta, $user_info ) {
    $ship_addr = isset( $payment_meta['ship_addr'] ) ? $payment_meta['ship_addr'] : 'none';
?>
    
    <div class="column-container">    
        <div class="column" style="border: 2px solid red;"> 
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
add_action( 'edd_payment_personal_details_list', 'mov_edd_view_order_details', 10, 2 );

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
function mov_edd_email_tag_ship_addr( $payment_id ) {
    $payment_data = edd_get_payment_meta( $payment_id );
    return $payment_data['ship_addr'];
}

?>