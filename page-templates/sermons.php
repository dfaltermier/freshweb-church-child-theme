<?php
/**
 * Template Name: Sermons
 */
// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *
 */
function fw_child_sermon_query() {

    return new WP_Query( array(
        'post_type' => 'sermon',
        'paged'     => FW_Child_Common_Functions::get_current_page_number()
    ) );

}

/**
 *
 */
function fw_child_sermon_header_banner_data( $data ) {

    $data['subtitle'] = 'Browse by date';
    return $data;

}

// Make query available via filter
add_filter( 'fw_child_sermon_query', 'fw_child_sermon_query' );

// Make header banner data available via filter
add_filter( 'fw_child_sermon_header_banner_data', 'fw_child_sermon_header_banner_data' );

// Load main template to show the page
locate_template( FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons.php', true );
