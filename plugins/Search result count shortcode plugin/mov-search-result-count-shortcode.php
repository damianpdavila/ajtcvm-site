<?php

/*
Plugin Name:  Moventis Search Results Count Shortcode
Version: 1.0
Description: This plugin publishes a shortcode to return the search result count from a search.
Author: Damian Davila
Author URI: https://www.moventisusa.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moventis
*/

/**
 * Shortcode [mov_search_result_count] returns count of search results from a search.
 *
 * @return string count of search results
 * 
 * Parameters to be passed in on shortcode attributes:
 * 
 * $tag_name:       the HTML tag name used to return the count.  Default is <span>
 * 
 */

add_shortcode( 'mov_search_result_count', 'mov_get_search_result_count' );

function mov_get_search_result_count($atts) {

    $params = shortcode_atts( array(
        'tagname' => 'span'
        ), $atts );

    $tag_name = esc_attr($params['tagname']);

    $found_count = $GLOBALS['wp_query']->found_posts;

    $tag_class = 'mov-search-result-count';
    
    return "<" . $tag_name . " class='" . $tag_class . "'>" . $found_count . "</" . $tag_name . ">";    
    
};

?>