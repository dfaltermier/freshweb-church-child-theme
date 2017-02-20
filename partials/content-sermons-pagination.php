<?php
/**
 * 
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

$query = FW_Child_Sermon_Functions::get_sermon_query();

if ( ! $query ) {
    $query = $wp_query;
}

?>

<?php if ( $query->max_num_pages > 1 ) : // show only if more than 1 page ?>

    <section class="fw-child-sermon-entry-pagination">

        <?php
        // To Do: Replace with the_posts_pagination(), new as of WP 4.1 (how to use with CPT?)
        echo paginate_links( array(
            'current'   => max( 1, FW_Child_Common_Functions::get_current_page_number() ),
            'total'     => $query->max_num_pages,
            'type'      => 'list',
            'prev_text' => 'Previous',
            'next_text' => 'Next'
        ) );
        ?>

    </section>

<?php endif; ?>