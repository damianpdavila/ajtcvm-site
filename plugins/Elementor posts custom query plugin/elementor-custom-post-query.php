<?php
/*
Plugin Name: Elementor Custom Post Query
Description: This plugin creates custom posts queries that can be used by the Elementor posts widget
Author: Damian Davila
Version: 1.0.0
*/

// Query for EDD Downloads with price = 0
add_action( 'elementor/query/downloads_no_price', function( $query ) {

	$query->set( 'post_type', 'download' );
	$query->set( 'post_status', 'publish' );
	$query->set( 'ignore_sticky_posts', true);

	// Get current meta Query
	$meta_query = $query->get( 'meta_query' );

	// If there is no meta query when this filter runs, it should be initialized as an empty array.
	if ( ! $meta_query ) {
		$meta_query = [];
	}

	// Append our meta query
	$meta_query[] = [
		'key' => 'edd_price',
		'value' => 0.00,
		'compare' => '<=',
		'type' => 'DECIMAL',
	];
	$query->set( 'meta_query', $meta_query );

} );

// Query for WP Events Manager events ordered by event start date instead of post create date
add_action( 'elementor/query/events_by_start_date', function( $query ) {

	$query->set( 'post_type', 'event_listing' );
	$query->set( 'post_status', 'publish' );  // Note: expired events have post status of 'expired'
	$query->set( 'ignore_sticky_posts', true);
	$query->set( 'meta_key', '_event_start_date');
    $query->set( 'orderby' , 'meta_value');
    $query->set( 'order'   , 'ASC');

} );
