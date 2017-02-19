<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

$speaker_terms = FW_Child_Sermon_Functions::get_sermon_speaker_terms();
$speaker_term_counter = 0;

?>

<div class="fw-child-sermon-speakers fw-child-clearfix">

    <?php if ( ! empty( $speaker_terms ) ) : ?>

        <ul class="fw-child-sermon-speaker-list">

            <?php foreach ( $speaker_terms as $term ) : ?>

                <?php 
                    if ( $term->speaker_image_attachment_id ) {
                        $image_html = wp_get_attachment_image( 
                            $term->speaker_image_attachment_id,
                            'full', 
                            false, 
                            array( 'class' => 'fw-child-sermon-speaker-entry-image' ) 
                        );
                    }
                ?>

                <?php
                // We must not leave any whitespace between the closing 'li' tag and the
                // beginning of the next in order for the page flow to work with the set widths.
                ?>
                <?php if ( $speaker_term_counter > 0 ) { echo '</li>'; } ?><li class="fw-child-sermon-speaker-entry">

                    <?php $speaker_term_counter++; ?>

                    <?php if ( ! empty( $image_html ) ) : ?>

                        <div class="fw-child-sermon-speaker-entry-image-container">
                            <a href="<?php echo esc_url( $term->link ); ?>"><?php echo $image_html; ?></a>
                        </div>

                    <?php endif; ?>

                    <div class="fw-child-sermon-speaker-entry-name">
                        <a href="<?php echo esc_url( $term->link ); ?>"><?php echo esc_html( $term->name ); ?></a>
                        <span class="fw-child-sermon-speaker-count">( <?php echo $term->count; ?> )</span>
                    </div>

                    <?php if ( ! empty( $image_html ) ) : ?>

                        <div class="fw-child-sermon-speaker-entry-description">
                            <?php echo esc_html( $term->description ); ?>
                            <div class="fw-child-sermon-speaker-entry-speaker-link">
                                [ <a href="<?php echo esc_url( $term->speaker_url ); ?>">Speaker Details</a> ]
                            </div>
                        </div>

                    <?php endif; ?>

            <?php endforeach; ?>

            </li>
        </ul>
 
    <?php else: ?>

        <div class="fw-child-sermons-none">There are no sermon speakers to display.</div>

    <?php endif; ?>

</div>
