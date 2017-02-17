<?php
/**
 * Template Name: Sermon Dates
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *
 */
function fw_child_sermon_header_banner_data( $data ) {

    $data['subtitle'] = 'Browse by date';
    return $data;

}

// Make query available via filter
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
                 FW_CHILD_THEME_PARTIALS_DIR . '/content-sermon-dates'
            );

            ?>

        </section>

    </div>

</div>

<?php get_footer(); ?>


