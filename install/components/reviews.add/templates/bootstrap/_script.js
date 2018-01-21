
/**
 * reviews.add component
 * 
 * @author dimabresky 
 * @copyright 2018, travelsoft
 */


(function (window) {

    var document = window.document;
    var $ = window.jQuery;
    
    $(document).ready(function () {
        
        var __parameters = window.reviewsAddJsParameters;
        
        $(document).on('click', '#toggle-ar', function () {

            var $this = $(this);

            var action = $this.data('action');

            var cp = $('input[name="confirm_password"]');

            var cpBox = cp.parent();

            switch (action) {

                case "registration":

                    $this.text(__parameters.messages.authorize);

                    $this.data('action', 'autorize');

                    cpBox.removeClass('hidden');

                    cp.prop('disabled', false);

                    break;

                case "autorize":

                    $this.text(__parameters.messages.registration);

                    $this.data('action', 'registration');

                    cpBox.addClass('hidden');

                    cp.prop('disabled', true);

                    break;
            }
        });

        if (__parameters.raty.init) {

            $('#raty-ar').raty({
                scoreName: 'rating',
                number: __parameters.raty.number,
                score: __parameters.raty.score,
                path: '/local/modules/travelsoft.reviews/plugins/raty/img'
            });
        }

        if (__parameters.triggerReviewModal) {
            $('#add-review-modal').modal('show');
        }

        if (__parameters.scrollToSuccessMessage) {
            $('html, body').animate({
                scrollTop: $("#add-review-success-message").offset().top
            }, 1000);
        }

    });

})(window);
