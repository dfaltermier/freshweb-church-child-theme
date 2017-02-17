<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 *  $archives = array(
 *     '2016' => array(
 *          'month  => 'November',
 *          'count' => 2,
 *          'url'   => 'http...'
 *     ),
 *      ...
 *  );
 */
$archives = FW_Child_Sermon_Functions::get_sermon_archives();
$number_of_years         = count( $archives ); // Total number of years in our archive list
$number_of_years_counter = 0;
$number_of_years_per_row = 2;

?>

<div class="fw-child-sermon-archives fw-child-clearfix">

    <?php 
    if ( ! empty( $archives ) ) : ?>

        <?php foreach ( $archives as $year => $month_list ) : ?>

            <?php $number_of_years_counter++; ?>

            <?php if ( ! empty( $month_list ) ) : ?>

                <?php
                // Start a new row every $number_of_years_per_row.
                ?>
                <?php if ( $number_of_years_counter % $number_of_years_per_row !== 0 ) : ?>
                    <div class="fw-child-sermon-archives-row">
                <?php endif; ?>

                    <article class="fw-child-sermon-archives-container">

                        <section class="fw-child-sermon-archives-header">
                            <h2><?php echo $year; ?></h2>
                        </section>

                        <section class="fw-child-sermon-archives-body">
                        
                            <ul class="fw-child-sermon-archives-list">
     
                                <?php foreach ( $month_list as $month_data ) : ?>

                                    <li class="fw-child-sermon-archives-item">
                                        <div class="fw-child-sermon-archives-name">
                                            <a href="<?php echo esc_url( $month_data['url'] ); ?>"><?php echo esc_html( $month_data['month'] ); ?></a>
                                            <span class="fw-child-sermon-archives-count"><?php echo $month_data['count']; ?></span>
                                        </div>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </section>

                    </article>

                <?php
                // Close the row if:
                // 1. we have our number_of_years_per_row, or
                // 2. we are at the end of our list of years (where the length may be odd)
                ?>
                <?php if ( ( $number_of_years_counter % $number_of_years_per_row === 0 ) ||
                           ( $number_of_years_counter >= $number_of_years ) ): ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="fw-child-sermons-none">There are no sermon archives to display.</div>

    <?php endif; ?>

</div>
