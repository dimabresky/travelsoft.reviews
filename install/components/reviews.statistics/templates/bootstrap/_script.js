
/**
 * reviews.statistics component
 * 
 * @author dimabresky 
 * @copyright 2018, travelsoft
 */

(function (window) {

    var document = window.document;
    var $ = window.jQuery;
    
    $(document).ready(function () {
        
        var __parameters = window.reviewsStatisticsJsParameters;
        
        $(".stars").each(function () {
            $(this).raty({
                readOnly: true,
                number: __parameters.total_stars_count,
                halfShow: true,
                score: $(this).data("stars"),
                path: '/local/modules/travelsoft.reviews/plugins/raty/img'
            });
        });
    });
    
})(window)