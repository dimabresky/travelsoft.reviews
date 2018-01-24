
/**
 * reviews.list component
 * 
 * @author dimabresky 
 * @copyright 2018, travelsoft
 */

(function (window) {

    var document = window.document;
    var $ = window.jQuery;

    /**
     * @param {$} elements
     * @returns {undefined}
     */
    function __initFancybox(elements) {
        elements.fancybox({
            buttons: ['thumbs', 'close']
        });
    }
    
    /**
     * @param {$} elements
     * @returns {undefined}
     */
    function __initReadmore(elements) {
        elements.readmore({
            speed: 75,
            moreLink: '<a href="#">Подробнее</a>',
            lessLink: '<a href="#">Скрыть</a>'
        });
    }

    /**
     * @param {$} elements
     * @param {Number} total_stars_count
     * @returns {undefined}
     */
    function __initRaty(elements, total_stars_count) {

        elements.each(function () {
            $(this).raty({
                readOnly: true,
                number: total_stars_count,
                halfShow: true,
                score: $(this).data("stars"),
                path: '/local/modules/travelsoft.reviews/plugins/raty/img'
            });
        });
    }

    $(document).ready(function () {

        var __parameters = window.reviewsListJsParameters;

        __initRaty($(".review-raty"), __parameters.total_stars_count);

        __initFancybox($('[data-fancybox^="gallery-"]'));
        
        __initReadmore($(".review-text"));
    });

})(window);
