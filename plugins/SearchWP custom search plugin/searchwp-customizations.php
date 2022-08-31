<?php
/*
Plugin Name: Moventis SearchWP Customizations
Description: Customizations for SearchWP
Author: Damian Davila
Author URI: https://moventisusa.com
Version: 1.0.0
*/

//This will extract the downloads files content and save it as a custom field called searchwp_edd_file_content
//The content in the custom field will be used for phrase searches
//Add this field to the Download attributes in the engine
add_filter( 'searchwp\entry\data', function( $data, $entry ) {

	$entry = $entry->native();

	// We only want to consider WP_Post objects.
	if ( ! $entry instanceof \WP_Post ) {
		return $data;
	}

	if( 'download' == $entry->post_type ){

		if( ! \SearchWP\Settings::get( 'document_content_reset', 'boolean' ) ){

			$extracted_file_content = get_post_meta( $entry->ID, 'searchwp_edd_file_content', true );
			if( ! empty( $extracted_file_content ) ){
				$data['meta'][ 'searchwp_edd_file_content' ] = $extracted_file_content;
				return $data;
			}
		}

		$download = new EDD_Download( $entry->ID );
		$files = $download->get_files();

		if( ! empty( $files ) ){
			$extracted_file_content = array_map(
				function( $file ){
					$file_content = \SearchWP\Parser::extract_text( get_attached_file( $file['attachment_id'] ) );
					// Clean up the contents to avoid issues with the parser
					$file_content = iconv( "UTF-8","UTF-8//IGNORE", $file_content );
					$file_content = str_replace( array( '<', '>' ), '', html_entity_decode( $file_content ) );
					$file_content = preg_replace('!\s+!', ' ', $file_content );
					return $file_content;
				}, $files
			);

			update_post_meta( $entry->ID, 'searchwp_edd_file_content', implode( ' ', $extracted_file_content ) );
			$data['meta'][ 'searchwp_edd_file_content' ] = $extracted_file_content;
		}
		else{
			delete_post_meta( $entry->ID, 'searchwp_edd_file_content' );
		}
	}

	return $data;
}, 20, 2 );


// Add "Attached EDD Content" as available option to SearchWP Source Attributes.
add_filter( 'searchwp\source\attribute\options', function( $keys, $args ) {

	if ( $args['attribute'] !== 'meta' ) {
		return $keys;
	}

	// This key is the same as the one used in the searchwp\entry\data hook above, they must be the same.
	$pdf_content_key = 'searchwp_edd_file_content';

	// Add "Attached PDF Content" Option if it does not exist already.
	if ( ! in_array(
		$pdf_content_key,
		array_map( function( $option ) { return $option->get_value(); }, $keys )
	) ) {
		$keys[] = new \SearchWP\Option( $pdf_content_key, 'Attached EDD Content' );
	}

	return $keys;
}, 20, 2 );

// Adjust searching to improve ability to recognize partial matches like "cat" and return "cats"
// Provided by SearchWP support in response to support ticket #fe01066873
//add_filter( 'searchwp\query\partial_matches\force', '__return_true' );
//add_filter( 'searchwp\query\partial_matches\adaptive', '__return_false' );
add_filter( 'searchwp\source\post\global_excerpt\use_original_search_string', '__return_false' );

/** 
 * Enable SearchWP click tracking using a shortcode since cannot access the search template directly with Elementor.
 * https://searchwp.com/extensions/metrics/#tracking
 */
function mov_search_click_tracking($atts) {

    $params = shortcode_atts( array(
        'switch' => ''
        ), $atts );

    $start_stop = strtolower(esc_attr($params['switch']));

	$msg = '';
	$display = 'none';

	if ($start_stop == 'start') {
		do_action( 'searchwp_metrics_click_tracking_start' );
		$msg = 'Started metrics.';
	} elseif ($start_stop == 'stop') {
		do_action( 'searchwp_metrics_click_tracking_stop' );
		$msg = 'Stopped metrics.';
	} elseif ($start_stop == '') {
		$msg = 'ERROR: switch value must be "start" or "stop".';
		$display = 'block';
	}

    return '<span style="display: ' . $display . '">' . $msg . '</span>';
    
};

add_shortcode( 'mov_search_click_tracking', 'mov_search_click_tracking' );

/**
 * Set the number of search results.
 * Necessary to minimize the time it takes to return certain searches.  
 * Issue exists where pulling the contextual snippet to highlight in the search results can take too long because the PDFs can be so huge.
 * So far, SearchWP has no way to improve on it, so best solution to date is just to limit the number of search results returned.
 * 
 */

function mov_search_posts_per_page( $query) {

	if ( $query->is_search() && $query->is_main_query() && ! is_admin() ) {
        $query->set( 'posts_per_page', '5' );
    }
}
add_filter( 'pre_get_posts', 'mov_search_posts_per_page' );

/**
 * Suppress PDF excerpts in search results for Journal downloads.
 * Necessary to minimize the time it takes to return certain searches.  
 * Issue exists where pulling the contextual snippet to highlight in the search results can take too long because the PDFs can be so huge.
 * So far, SearchWP has no way to improve on it, so best solution to date is just to limit the number of search results returned (previous filter) and
 * suppress creating an excerpt from the PDF for journals.
 * 
 * NOTE:  this filter is not documented yet, but per the source, it interrupts creation of excerpt just before meta fields are searched, which includes PDF contents.
 * It *will* show excerpts for post content
 */
// Provided by SearchWP support in response to support ticket #fc16524a5d
function mov_search_excerpt_suppress( $break, $args ) {	

	if( has_term( 'journals', 'download_category', $args['post'] ) ) {
		$break = true;
	}
	return $break;
}
add_filter( 'searchwp\source\post\global_excerpt_break', 'mov_search_excerpt_suppress' , 10, 4 );

/** 
 * Doing a quoted search for an exact article title is not returning the journal, only the actual article download.
 * Per SearchWP support, this may help.
 */
// Disable SearchWP AND logic token threshold, allowing AND logic for all searches.
// @link https://searchwp.com/documentation/hooks/searchwp-query-logic-and-token_threshold/
// Provided by SearchWP support in response to support ticket #fc16524a5d

// 29Aug2022: Long-running queries were overloading database server and killing the site.  
// Per Elio from SearchWP support: "If the server has issues even with this then you should lower the value. The default value is 5 and you should not go lower than that."
// Provided by SearchWP support in response to support ticket #7748a517f1

//add_filter( 'searchwp\query\logic\and\token_threshold', '__return_false' );
add_filter( 'searchwp\query\logic\and\token_threshold', function( $threshold ) {
	return 10;
} );

/**
 * Modify search results order to bubble up article results above all others (principally the journals).
 * The primary reason is that the journals are not highlighting excerpts correctly.  Reason as yet unknown but it is confusing.
 * Secondarily it is probably the right thing to do anyway since articles are the primary target of searchers, not the journal.
 */

// Add Weight to Entries (posts) within a Specific Category (taxonomy term) in SearchWP.
// @link https://searchwp.com/documentation/knowledge-base/add-weight-category-tag-term/

add_filter( 'searchwp\query\mods', function( $mods ) {
	
	global $wpdb;

	// Taxonomy bonus weight Mods.
	$bonuses = [ [
		'term_id' => 16,  // Term ID to receive extra weight == articles.
		'weight'  => 9999, // How much extra weight for this term.
	] ];

	$term_mods = [];

	foreach ( $bonuses as $bonus ) {
		$mod = new \SearchWP\Mod();
		$index_alias = $mod->get_foreign_alias();
		$mod->relevance( "IF((
			SELECT {$wpdb->prefix}posts.ID
			FROM {$wpdb->prefix}posts
			LEFT JOIN {$wpdb->prefix}term_relationships ON (
				{$wpdb->prefix}posts.ID = {$wpdb->prefix}term_relationships.object_id
			)
			WHERE {$wpdb->prefix}posts.ID = {$index_alias}.id
				AND {$wpdb->prefix}term_relationships.term_taxonomy_id = {$bonus['term_id']}
			LIMIT 1
		) > 0, {$bonus['weight']}, 0)" );

		$term_mods[] = $mod;
	}

	$mods = array_merge( $mods, $term_mods );

	return $mods;
} );
