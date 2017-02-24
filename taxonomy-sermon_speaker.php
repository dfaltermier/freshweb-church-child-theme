<?php
/**
 * Displays the archived (and paginated) page of sermons based on a given sermon speaker
 *
 * WordPress loads this file with a url similar to:
 *     http://your-church-domain/sermons/speaker/david-mckinney/
 * where 'david-mckinney' is the selected speaker by the user.
 *
 * @package    FreshWeb_Church
 * @subpackage Page
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 *
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

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

    $query_results = get_queried_object(); // Retrieve the currently queried WP object.
    $data['subtitle'] = 'by ' . $query_results->name;
    return $data;

}
// Make method above available via apply_filter().
add_filter( 'fw_child_sermon_header_banner_data', 'fw_child_sermon_header_banner_data' );

?>

<?php get_header(); ?>

<div class="full_width">

    <div class="full_width_inner">

        <section class="fw-child-sermon-section">

            <?php

            // Display header banner with page title.
            get_template_part( FW_CHILD_THEME_PARTIALS_DIR . '/content-header-banner' );

            // Display sermon navigation icons.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-navigation'
            );

            // Display sermons.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-loop'
            );

            // Display pagination.
            FW_Child_Common_Functions::wrap_template_part(
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-pagination'
            );

            ?>

        </section>

    </div>

</div>

<?php get_footer(); ?>


