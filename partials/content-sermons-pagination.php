<?php
/**
 * Display the pagination buttons at the bottom of any page where the number
 * of sermon entries exceed the page limit.
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

$query = FW_Child_Sermon_Functions::get_sermon_query();

if ( ! $query ) {
    $query = $wp_query;
}

?>

<?php if ( $query->max_num_pages > 1 ) : // show only if more than 1 page ?>

    <section class="fw-child-sermon-entry-pagination">

        <?php
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