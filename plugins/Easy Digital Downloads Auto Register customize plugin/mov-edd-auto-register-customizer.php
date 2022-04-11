<?php
/*
Plugin Name:  Moventis Easy Digital Downloads Auto Register customization Plugin
Version: 1.0
Description: This plugin customizes the EDD Auto Register plugin functionality.  Creates a custom email template for EDD emails.
Author: Damian Davila based on code from Easy Digital Downloads
Author URI: https://www.moventisusa.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moventis
*/

/* 
Replace the default WordPress login link in Auto Register email
*/
function custom_edd_auto_register_login_link( $default_email_body ) {

    $url = site_url( '/profile/', 'https' );

	$default_email_body = str_replace(
		wp_login_url(),
		$url,
		$default_email_body
	);

	return $default_email_body;
}
add_filter( 'edd_auto_register_email_body', 'custom_edd_auto_register_login_link' );

/* 
Replace the default subject line in Auto Register email
*/
function custom_edd_auto_register_email_subject( $subject ) {

	// enter your new subject below
	$subject = 'Your account access info for the American Journal of TCVM website';

	return $subject;

}
add_filter( 'edd_auto_register_email_subject', 'custom_edd_auto_register_email_subject' );

/* 
Replace the default body copy in Auto Register email
*/
function custom_edd_auto_register_email_body( $default_email_body, $first_name, $username, $password ) {

	$default_email_body = __( "Dear", "edd-auto-register" ) . ' ' . $first_name . ",\n\n";

	$default_email_body .= __( "Thank you for your purchase!  You will receive your receipt and direct download link in a separate email.", "edd-auto-register" ) . "\n\n";

	$default_email_body .= __( "Did you know you can log in to the AJTCVM.org site and re-download your file(s) at any time?", "edd-auto-register" ) . "\n";	
	$default_email_body .= __( "Simply browse to the link below, and use the userid and password to log in.", "edd-auto-register" ) . "\n";		
	$default_email_body .= __( "Below are your login details:", "edd-auto-register", "edd-auto-register"  ) . "\n\n";
	
	$default_email_body .= __( "Login link:", "edd-auto-register" ) . ' ' . site_url( '/profile/', 'https' ) . "\r\n";
	$default_email_body .= __( "Your Username:", "edd-auto-register" ) . ' ' . $username . "\r\n";
	$default_email_body .= __( "Your Password:", "edd-auto-register" ) . ' ' . $password . "\r\n\n";

	$default_email_body .= __( "FYI:  you can change your password after you log in to your profile page.", "edd-auto-register" ) . "\r\n\n";

	$default_email_body .= "<strong>" . __( "REMINDER", "edd-auto-register" ) . '</strong> ' . __( "If you purchased an All Access Pass, you must log in to have free access to all the journals and articles on the site.", "edd-auto-register" ) . "\r\n\n";

	return $default_email_body;

}
add_filter( 'edd_auto_register_email_body', 'custom_edd_auto_register_email_body', 10, 4 );

/** 
 * Make last name a required field during checkout (and auto registration process)
 */
function mov_edd_purchase_form_required_fields( $required_fields ) {

    $required_fields['edd_last'] = array(   
        'error_id' => 'invalid_last_name',
        'error_message' => __( 'Please enter your last name.', 'edd' )
    );

    return $required_fields;
}
add_filter( 'edd_purchase_form_required_fields', 'mov_edd_purchase_form_required_fields' );