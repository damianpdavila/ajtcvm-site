<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

/**
 * Enable ability to set ACF fields to read-only and/or disabled through UI
 */
function add_readonly_and_disabled_to_text_field($field) {
    acf_render_field_setting( $field, array(
      'label'      => __('Read Only?','acf'),
      'instructions'  => '',
      'type'      => 'true_false',
      'name'      => 'readonly',
	  'ui'			=> 1,
	  'class'	  => 'acf-field-object-true-false-ui'
    ));
	  
    acf_render_field_setting( $field, array(
      'label'      => __('Disabled?','acf'),
      'instructions'  => '',
      'type'      => 'true_false',
      'name'      => 'disabled',
	  'ui'			=> 1,
	  'class'	  => 'acf-field-object-true-false-ui',
    ));
		
 }
 add_action('acf/render_field_settings/type=text', 'add_readonly_and_disabled_to_text_field');

/*
 * Filter out any styling-oriented html tags pasted in to the ACF WYSIWYG (TinyMCE) fields.
 * Primarily doing for the "article_abstract" field, but probably ok to leave for all wysiwyg's.
*/
function mov_acf_wysiwyg_remove_span() {
   ?>
     <script>
       (function() {
         // (filter called before the tinyMCE instance is created)
         acf.add_filter('wysiwyg_tinymce_settings', function(mceInit, id, field) {

            mceInit.paste_preprocess = function(plugin, args) {
               //console.log("ORIG:" + args.content);
               args.content = args.content.replace( /(<span( [^>]*)>)|(<span>)|(<\/span>)|(<font( [^>]*)>)|(<font>)|(<\/font>)|(<style( [^>]*)>)|(<style>)|(<\/style>)/ig, '');
               args.content = args.content.replace( /(<br( ?)\/>)|(<br>)/ig, ' ');
               //console.log("MOD:" + args.content);
            }

            return mceInit;
         });
       })();
     </script>
   <?php
}
add_action('acf/input/admin_footer', 'mov_acf_wysiwyg_remove_span');


/**
 * Hide admin bar if not administrator or editor
 */
function remove_admin_bar() {
   if (!current_user_can('administrator') && !current_user_can('editor')) {
      show_admin_bar(false);
   }
}
add_action('after_setup_theme', 'remove_admin_bar');

/**
 * Only allow admins and editors into the dashboard
 */
function blockusers_init() { 
    if ( is_admin() && ! (current_user_can( 'administrator' ) || current_user_can('editor') || current_user_can('shop_manager')) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) { 
        wp_redirect( home_url() ); 
        exit; 
    } 
} 
add_action( 'init', 'blockusers_init' ); 

/**
 * Load various scripts for the admin panels
 */
function ajtcvm_enqueue_custom_admin_js($hook) {

   if( $hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php' ) 
		return;

   wp_enqueue_script('ajtcvm-custom', get_stylesheet_directory_uri().'/scripts/ajtcvm-admin-scripts.js');
}
add_action('admin_enqueue_scripts', 'ajtcvm_enqueue_custom_admin_js');

/**
 * Style the login form
 */
function my_login_styles() {
   wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/style-login.css' );
   //wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/style-login.js' );
}
add_action( 'login_enqueue_scripts', 'my_login_styles' );

/**
 * Redirect back to profile/login page after logout (instead of the default wp-admin)
 */
function ajtcvm_logout_redirect( $redirect_to, $requested_redirect_to, $user ) {
 
   return home_url('/profile');
   
}         
add_filter( 'logout_redirect', 'ajtcvm_logout_redirect', 9999, 3 );

/**
 * Enable the Widgets menu item in the WordPress admin menu; disabled in Hello theme by default
*/
if (function_exists("register_sidebar")) {
  register_sidebar();
}
