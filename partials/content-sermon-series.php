<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

$sermon_series_data = FW_Child_Sermon_Functions::get_sermon_series_with_pagination();

$sermon_series_objects = array_values( $sermon_series_data['series'] );
$pagination = $sermon_series_data['pagination'];

$date_format = get_option( 'date_format' );

?>

<div class="fw-child-sermon-series fw-child-clearfix">

    <?php if ( ! empty( $sermon_series_objects ) ) : ?>

        <?php foreach ( $sermon_series_objects as $sermon_series_object ) : ?>

            <article class="fw-child-sermon-series-entry">

                <?php if ( isset( $sermon_series_object->image_attachment_id ) ) : ?>

                    <header>

                        <div class="fw-child-sermon-series-entry-image">
                            <a href="<?php echo esc_url( get_term_link( (int) $sermon_series_object->term_id ) ); ?>"><?php echo wp_get_attachment_image( $sermon_series_object->image_attachment_id, 'large' ); ?></a>
                        </div>

                    </header>

                <?php endif; ?>

                <section class="fw-child-sermon-series-entry-body">

                    <h2 class="fw-child-sermon-series-entry-title">
                        <a href="<?php echo esc_url( get_term_link( (int) $sermon_series_object->term_id ) ); ?>"><?php echo esc_html( $sermon_series_object->name ); ?></a>
                    </h2>

                    <div class="fw-child-sermon-series-entry-date">
                        <time datetime="<?php echo esc_attr( the_time( 'c' ) ); ?>">
                        <?php
                            echo FW_Child_Common_Functions::create_date_range_string(
                                $sermon_series_object->sermon_earliest_date,
                                $sermon_series_object->sermon_latest_date,
                                $date_format
                            );
                         ?>
                         </time>
                    </div>

                    <div class="fw-child-sermon-series-entry-count">
                        <?php printf( _n('%s Sermon', '%s Sermons', $sermon_series_object->count ), $sermon_series_object->count ); ?>
                    </div>

                    <div class="fw-child-sermon-series-entry-separator"></div>

                    <?php if ( isset( $sermon_series_object->description ) ) : ?>

                        <div class="fw-child-sermon-series-entry-excerpt"><?php echo FW_Child_Common_Functions::get_trimmed_excerpt( $sermon_series_object->description ); ?></div>

                    <?php endif; ?>

                    <div class="fw-child-sermon-series-entry-footer">
                        <a itemprop="url" href="<?php echo esc_url( get_term_link( (int) $sermon_series_object->term_id ) ); ?>" 
                           class="qbutton center default" style="">View Series</a>
                    </div>

                </section>

            </article>

        <?php endforeach; ?>

        <?php if ( ! empty( $pagination) ) : ?>

             <section class="fw-child-sermon-entry-pagination">
                 <?php echo $pagination; ?>
             </section>

         <?php endif; ?>
 
    <?php else: ?>

        <div class="fw-child-sermons-none">There are no sermon series to display.</div>

    <?php endif; ?>

</div>
