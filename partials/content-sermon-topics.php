<?php
/**
 * Displays a list of sermon topics
 *
 * WordPress loads this partial file with a url similar to:
 *     http://your-church-domain/sermons/topics/
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

$topics = get_categories( array(
    'taxonomy'         => 'sermon_topic',
    'hide_empty'       => true,
    'hierarchical'     => true,
    'pad_counts'       => false   // Was true
) );

// Manually replace the parentheses around the count.
$topics = str_replace( '(', '<div class="fw-child-sermon-topic-count-wrapper"><div class="fw-child-sermon-topic-count">', $topics );
$topics = str_replace( ')', '</div></div>', $topics );

?>

<div class="fw-child-sermon-topics fw-child-clearfix">

    <?php if ( ! empty( $topics ) ) : ?>

        <ul class="fw-child-sermon-topics-list">

            <?php foreach ( $topics as $topic ) : ?>
                <?php $category_count = get_category_link( $topic->term_id ); ?>

                <li>
                    <a href="<?php echo esc_url( $category_count ); ?>" >
                        <span class="fw-child-sermon-topics-name"><?php echo $topic->name; ?></span>
                        <div class="fw-child-sermon-topic-count-wrapper">
                            <span class="fw-child-sermon-topic-count"><?php echo esc_html( $topic->count ); ?></span>
                        </div>
                    </a>
                </li>

            <?php endforeach; ?>
        </ul>
 
    <?php else: ?>

        <div class="fw-child-sermons-none">There are no sermon topics to display.</div>

    <?php endif; ?>

</div>
