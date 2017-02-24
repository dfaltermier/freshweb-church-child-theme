<?php
/**
 * Provides the WP loop through which we display each sermon entry.
 *
 * WordPress loads this partial file with a url similar to:
 *     http://your-church-domain/sermons/series/the-case-for-believing/
 * where 'the-case-for-believing' is the selected series by the user.
 *
 * @package    FreshWeb_Church
 * @subpackage Partial
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
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
