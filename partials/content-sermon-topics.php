<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Get topics
$topics = wp_list_categories( array(
    'taxonomy'         => 'sermon_topic',
    'hierarchical'     => true,
    'show_option_none' => '', // Don't return the default 'No Categories' message when empty.
    'pad_counts'       => true,
    'show_count'       => true,
    'title_li'         => '',
    'echo'             => false
) );

// Manually replace the parentheses around the count.
$topics = str_replace( '(', '<span class="fw-child-sermon-topic-count">', $topics );
$topics = str_replace( ')', '</span>', $topics );

?>

<div class="fw-child-sermon-topics fw-child-clearfix">

    <?php if ( ! empty( $topics ) ) : ?>

        <ul class="fw-child-sermon-topics-list">
            <?php echo $topics; ?>
        </ul>
 
    <?php else: ?>

        <div class="fw-child-sermons-none">There are no sermon topics to display.</div>

    <?php endif; ?>

</div>
