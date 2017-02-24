<?php
/**
 * Template Name: Sermons
 *
 * WP page template to display paginated list of sermons.
 *
 * WordPress loads this file with a url similar to:
 *     http://your-church-domain/sermons/
 *
 * @package    FreshWeb_Church
 * @subpackage Page_Template
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns a new query object with a paginated list of sermons sorted by date.
 * This method is called via an apply_filter() from the method referenced by 'see' below.
 *
 * @since 1.1.0
 * @see   FW_Child_Sermon_Functions::get_sermon_query()
 *
 * @return WP_Query  New query.
 */
function fw_child_sermon_query() {

    return new WP_Query( array(
        'post_type' => 'sermon',
        'paged'     => FW_Child_Common_Functions::get_current_page_number()
    ) );

}
// Make method above available via apply_filter().
add_filter( 'fw_child_sermon_query', 'fw_child_sermon_query' );

/**
 * Returns the text used in the header of this page. This text overlays the background
 * header image. This method is called via an apply_filter() from the method referenced
 * by 'see' below.
 *
 * @since 1.1.0
 * @see   FW_Child_Sermon_Functions::get_sermon_header_banner_data()
 *
 * @param  array $data Header text components.
 * @return array       Modified text.
 */
function fw_child_sermon_header_banner_data( $data ) {

    $data['subtitle'] = 'Browse by date';
    return $data;

}
// Make method above available via apply_filter().
add_filter( 'fw_child_sermon_header_banner_data', 'fw_child_sermon_header_banner_data' );


// Load main template to show the page.
locate_template( FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons.php', true );
