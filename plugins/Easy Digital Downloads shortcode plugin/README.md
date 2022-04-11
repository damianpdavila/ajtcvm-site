# Moventis Easy Digital Downloads Shortcode Plugin

This plugin publishes shortcodes to expose EDD functionality in Wordpress.

1. Shortcode to display current count of items in the EDD cart.

## Usage

Simply insert the shortcode [ mov_edd_cart_count ] without spaces and using the following parameters:

 * tagname:       the HTML tag to wrap the count in.  Default is "span".

e.g., [ mov_edd_cart_count tagname="the tag name" ]


2. Shortcode to display login status text for the user.

## Usage

Simply insert the shortcode [ mov_edd_login_text ] without spaces and using the following parameters:

 * logged_in_text:       the text to return if the user is logged in.  Default is 'My Account'
 * logged_out_text:      the text to return if the user is logged out.  Default is 'Login'

e.g., [ mov_edd_login_text logged_in_text="the text to return when logged in" logged_out_text="the text to return when logged out" ]

