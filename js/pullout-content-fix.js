/**
 * This file fixes an image flashing from the pullout widget when the page loads.
 * This file is only useful when the pullout widget is installed.
 *
 * @package    FreshWeb_Church
 * @subpackage Page_Template
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */
(function ($) {

    $(window).load(function() {

        // Delauy 2 seconds before making the pullout image visible.
        // See stylesheet for initial setting of display:none.
        setTimeout(function() {
            var image = $('img', '#pullout-1').css( {'display': 'block'} );
        },
        2000); // Two seconds seems an okay delay.

    });

})(jQuery);
