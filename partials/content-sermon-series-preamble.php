<?php
/**
 * Sermon Series Preamble consisting of the series description, etc.
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

$description = term_description(); // Includes <p> tags

?>
<?php if ( ! empty( $description ) ) : ?>
    <div class="fw-child-sermon-series-preamble">
        <?php echo $description; ?>
    </div>
<?php endif; ?>
