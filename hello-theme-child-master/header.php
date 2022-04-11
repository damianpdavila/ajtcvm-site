<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php $viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' ); ?>
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php
	/** Add Google Scholar tags to article downloads (only) */
	if ( get_post_type() == 'download' && ! is_archive() && ! is_search() && has_term('articles', 'download_category') ) {

		echo '<meta name="citation_title" content="' . get_the_title() . '">';
		$authors = explode(";", get_field('article_author'));
		foreach ( $authors as $author ) {
			echo '<meta name="citation_author" content="' . trim($author) . '">';
		}
		echo '<meta name="citation_journal_title" content="American Journal of Traditional Chinese Veterinary Medicine">';
		// Get publication year; stored as child category under the 'publication-year' category (82)
		$term_id_pub_year = 82;  
		$taxonomy_name = 'download_category';
		$post = get_post();
		$terms = get_the_terms( $post->ID, $taxonomy_name );
		$pub_year = [];

		foreach ( $terms as $term ) {

			if( $term->parent == $term_id_pub_year ) {
				// should only be 1 "year" sub-cat per article, but just in case...
				$pub_year[] = $term->name;
			}			
		}
		echo '<meta name="citation_publication_date" content="' . join(",", $pub_year) . '">';
		// Journals started in 2006 so volume = pub year - 2005;  again, note that there should only be 1 pub year child category selected, but logic uses first one just in case
		$volume = is_numeric($pub_year[0]) ? (int)$pub_year[0] - 2005 : 0;
		echo '<meta name="citation_volume" content="' . (string)$volume . '">';

		// Formal full citations are stored in the download.  Use them to get the issue # and first page/last page.
		// Can technically get volume there as well, but will continue to use the previous calculation just in case the citation is not entered.
		$citation = get_field('article_citation');
		if ( preg_match('/\d+\s*\(\s*(\d+)\s*\)\s*:\s*(\d+)\s*-\s*(\d+)/sm', $citation, $output_array) === 1) {
			echo '<meta name="citation_issue" content="' . $output_array[1] . '">';
			echo '<meta name="citation_firstpage" content="' . $output_array[2] . '">';
			echo '<meta name="citation_lastpage" content="' . $output_array[3] . '">';
		}
		

	}
	/** End Google Scholar */
	?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
hello_elementor_body_open();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
		get_template_part( 'template-parts/dynamic-header' );
	} else {
		get_template_part( 'template-parts/header' );
	}
}
