<?php
/**
 * Sermon Series Preamble consisting of the series description, etc.
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

$description = term_description(); // Includes <p> tags

?>
<?php if ( ! empty( $description ) ) : ?>
    <div class="fw-child-sermon-series-preamble">
        <?php echo $description; ?>
    </div>
<?php endif; ?>
