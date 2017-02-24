<?php
/**
 * Displays the navigation buttons under the header image
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
?>

<nav class="fw-child-sermon-navigation">
    <ul>
        <li class="fw-child-sermon-tab fw-child-sermons"><a href="/sermons/"></a></li>
        <li class="fw-child-sermon-tab fw-child-sermon-series"><a href="/sermons/series/"></a></li>
        <li class="fw-child-sermon-tab fw-child-sermon-speakers"><a href="/sermons/speakers/"></a></li>
        <li class="fw-child-sermon-tab fw-child-sermon-topics"><a href="/sermons/topics/"></a></li>
        <li class="fw-child-sermon-tab fw-child-sermon-books"><a href="/sermons/books/"></a></li>
        <li class="fw-child-sermon-tab fw-child-sermon-dates"><a href="/sermons/dates/"></a></li>
    </ul>
</nav>
<?php
/* CSS Trick: Preload background images for navigation hover-state backgrounds.
   This will prevent background images from flashing while loading when the
   user hovers over the navigation tabs. This <div> element will be moved
   off-screen out of view. See stylesheet. */
?>
<div class="fw-child-sermon-navigation-preload-image-depot">
        <ul>
        <li class="fw-child-sermon-tab fw-child-sermons"></li>
        <li class="fw-child-sermon-tab fw-child-sermon-series"></li>
        <li class="fw-child-sermon-tab fw-child-sermon-speakers"></li>
        <li class="fw-child-sermon-tab fw-child-sermon-topics"></li>
        <li class="fw-child-sermon-tab fw-child-sermon-books"></li>
        <li class="fw-child-sermon-tab fw-child-sermon-dates"></li>
    </ul>
</div>
