<?php
/**
 * Short Sermon Content (Archive)
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="fw-child-sermon-list fw-child-clearfix">

    <?php
    global $wp_query;

    $query = FW_Child_Sermon_Functions::get_sermon_query();

    if ( $query ) {
        $original_query = $wp_query;
        $wp_query = $query;
    }

    if ( have_posts() ) {

        while ( have_posts() ) {

            the_post();

            get_template_part( FW_CHILD_THEME_PARTIALS_DIR . '/content-sermons-entry' );

        }

    }
    else {

        echo '<div class="fw-child-sermons-none">There are no sermons to display.</div>';

    }

    if ( $query ) {
        // Restore original query
        $wp_query = $original_query;

        // Restore original post data.
        wp_reset_postdata();
    }

    ?>

</div>
