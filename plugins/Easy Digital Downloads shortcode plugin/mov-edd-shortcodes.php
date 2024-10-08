<?php

/*
Plugin Name:  Moventis EDD Shortcodes
Version: 1.2
Description: Simple WP access to Easy Digital Downloads functions not available out of the box.
Author: Damian Davila
Author URI: https://www.moventisusa.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moventis
*/

/**
 * Shortcode [mov_edd_cart_count] returns count of items in Easy Digital Downloads cart.
 *
 * @return string count of items in cart
 * 
 * Parameters to be passed in on shortcode attributes:
 * 
 * $tag_name:       the HTML tag name used to return the count.  Default is <span>
 * 
 * An additional class "edd-cart-loaded" is added to the returned element if the cart has any items.
 * 
 * API details at https://docs.easydigitaldownloads.com/article/275-showing-the-cart-quantity-in-your-templates
 * 
 */

add_shortcode( 'mov_edd_cart_count', 'mov_edd_get_cart_count' );

// Forces updating the cookie when an item is added.  Necessary when subsequent page is cached and the function won't otherwise be called.
// Refer to https://spinupwp.com/page-caching-personalized-dynamic-content/
add_action( 'edd_post_add_to_cart', 'mov_edd_get_cart_count' );

function mov_edd_get_cart_count($atts) {

    if ( headers_sent() || ! function_exists( 'edd_get_cart_quantity' ) ) {
        return;
    }

    $cart_qty = (int)edd_get_cart_quantity();

    // Set cart quantity in cookie to be referenced on cached pages so that the visible cart count is always accurate.
    $cart = array(
        'quantity' => $cart_qty,
    );
    setcookie( 'edd_cart', json_encode( $cart ), time() + 30 * 60, '/', $_SERVER['HTTP_HOST'] );

    // Do the actual shortcode processing
    $params = shortcode_atts( array(
        'tagname' => 'span'
        ), $atts );

    $tag_name = esc_attr($params['tagname']);

    if ( $cart_qty > 0 ) {
        $tag_class = 'edd-cart-quantity edd-cart-loaded';
    } else {
        $tag_class = 'edd-cart-quantity';
    }
    
    return "<" . $tag_name . " class='" . $tag_class . "'>" . strval($cart_qty) . "</" . $tag_name . ">";    
    
};


/**
 * Shortcode [mov_edd_login_text] returns user login status as a string to display as needed on front end
 *
 * @return string login status text
 * 
 * Parameters to be passed in on shortcode attributes:
 * 
 * $logged_in_text:       the text to return if the user is logged in.  Default is 'Logged In'
 * $logged_out_text:      the text to return if the user is logged out.  Default is 'Logged Out'
 * 
 */

add_shortcode( 'mov_edd_login_text', 'mov_edd_get_login_text' );

function mov_edd_get_login_text($atts) {

    $params = shortcode_atts( array(
        'logged_in_text' => 'Logged In',
        'logged_out_text' => 'Logged Out'
        ), $atts );

    $logged_in_text = esc_attr($params['logged_in_text']);
    $logged_out_text = esc_attr($params['logged_out_text']);
    
    if ( is_user_logged_in() ) {
        $return_text = $logged_in_text;
        $return_class = 'ajtcvm-logged-in';

        if ( is_logged_in_using_ipbl() ) {
            $return_class .= ' ajtcvm-logged-in-by-IP';
        }

    } else {
        $return_text = $logged_out_text;
        $return_class = 'ajtcvm-logged-out';
    }

    return '<span class="' . $return_class . '">' . $return_text . '</span>';
    
};

?>