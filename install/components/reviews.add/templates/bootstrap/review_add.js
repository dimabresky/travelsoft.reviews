(function (window) {
    
    var $ = window.jQuery;
    
    var document = window.document;
    
    $(document).ready(function () {
        
        var rsp = window.review_storage_parameters;
        
        $(document).on('click', '#toggle-ar',function () {
            
            var $this = $(this);
            
            var action = $this.data('action');
            
            var cp = $('input[name="confirm_password"]');
            
            var cpBox = cp.parent();         
            
            switch (action) {
                
                case "registration":
                    
                    $this.text(rsp.messages.authorize);
                    
                    $this.data('action', 'autorize');
                    
                    cpBox.removeClass('hidden');
                    
                    cp.prop('disabled', false);
                    
                    break;
                    
                case "autorize":
                    
                    $this.text(rsp.messages.registration);
                    
                    $this.data('action', 'registration');
                    
                    cpBox.addClass('hidden');
                    
                    cp.prop('disabled', true);
                    
                    break;
            }
        });
        
        if (rsp.raty.init) {
            
            $('#raty-ar').raty({
                scoreName: 'rating',
                number: rsp.raty.number,
                score: rsp.raty.score,
                path: '/local/modules/travelsoft.reviews/plugins/raty/img'
            });
        }
        
        if (rsp.triggerReviewModal) {
            $('#add-review-modal').modal('show');
        }
        
        if (rsp.triggerSuccessModal) {
            $('#success-add-review-message-modal').modal('show');
        }
        
    });
    
})(window);
