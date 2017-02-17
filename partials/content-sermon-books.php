<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

$sermon_books_list = FW_Child_Sermon_Functions::get_sermon_books();

$number_of_blocks         = 3; // Number of blocks: Old Testament, New Testament, and Books
$number_of_blocks_counter = 0;
$number_of_blocks_per_row = 2;
?>

<div class="fw-child-sermon-books fw-child-clearfix">

    <?php 
    // Process only if we have some books
    if (( ! empty( $sermon_books_list[0]['book_terms'] ) ) ||
        ( ! empty( $sermon_books_list[1]['book_terms'] ) ) ||
        ( ! empty( $sermon_books_list[2]['book_terms'] ) ) ) : ?>

        <?php foreach ( $sermon_books_list as $sermon_books ) : ?>

            <?php $number_of_blocks_counter++; ?>

            <?php
            if ( ! empty( $sermon_books['book_terms'] ) ) : ?>

                <?php
                // Start a new row every $number_of_blocks_per_row.
                ?>
                <?php if ( $number_of_blocks_counter % $number_of_blocks_per_row !== 0 ) : ?>
                    <div class="fw-child-sermon-books-row">
                <?php endif; ?>

                    <article class="fw-child-sermon-books-container">

                        <section class="fw-child-sermon-books-header">
                            <h2><?php echo $sermon_books['title']; ?></h2>
                        </section>

                        <section class="fw-child-sermon-books-body">
                        
                            <ul class="fw-child-sermon-books-list">
     
                                <?php foreach ( $sermon_books['book_terms'] as $book_term ) : ?>

                                    <li class="fw-child-sermon-book-item">
                                        <div class="fw-child-sermon-book-name">
                                            <a href="<?php echo esc_url( $book_term->link ); ?>"><?php echo esc_html( $book_term->name ); ?></a>
                                            <span class="fw-child-sermon-book-count"><?php echo $book_term->count; ?></span>
                                        </div>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </section>

                    </article>

                <?php
                // Close the row if:
                // 1. we have our number_of_blocks_per_row, or
                // 2. we are at the end of our list of books (where the length may be odd)
                ?>
                <?php if ( ( $number_of_blocks_counter % $number_of_blocks_per_row === 0 ) ||
                           ( $number_of_blocks_counter >= $number_of_blocks ) ): ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="fw-child-sermons-none">There are no sermon books to display.</div>

    <?php endif; ?>

</div>
