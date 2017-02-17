/**
 * This file is dependent on the jquery.fitvids.js file. fitvids.js makes our videos responsive
 * for the following video sources:
 *
 * YouTube
 * Vimeo
 * Blip.tv
 * Viddler
 * Kickstarter
 *
 * Our implementation below is based on instructions at https://github.com/davatron5000/FitVids.js.
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
