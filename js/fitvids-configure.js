/**
 * This file is dependent on the jquery.fitvids.js file. fitvids.js makes our videos iframes responsive
 * for the following video sources:
 *
 *   YouTube
 *   Vimeo
 *   Blip.tv
 *   Viddler
 *   Kickstarter
 *
 * Our implementation below is based on instructions at https://github.com/davatron5000/FitVids.js.
 *
 * @package    FreshWeb_Church
 * @subpackage Page_Template
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */
jQuery( function( $ ) {

    customSelectorList = [
        "object",
        "embed"
    ];

    customSelectorString = customSelectorList.join(', ');

    // For right now, we're only attaching to sermon videos.
    $('.single-sermon').fitVids( {
        customSelector: customSelectorString
    } );

} );
