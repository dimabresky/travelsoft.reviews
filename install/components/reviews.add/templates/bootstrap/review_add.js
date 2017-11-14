(function (window) {
    
    var $ = window.jQuery;
    
    var document = window.document;
    
    $(document).ready(function () {
        
        var arp = window.arp;
        
        $(document).on('click', '#toggle-ar',function () {
            
            var $this = $(this);
            
            var action = $this.data('action');
            
            var cp = $('input[name="confirm_password"]');
            
            var cpBox = cp.parent();         
            
            switch (action) {
                
                case "registration":
                    
                    $this.text(arp.messages.authorize);
                    
                    $this.data('action', 'autorize');
                    
                    cpBox.removeClass('hidden');
                    
                    cp.prop('disabled', false);
                    
                    break;
                    
                case "autorize":
                    
                    $this.text(arp.messages.registration);
                    
                    $this.data('action', 'registration');
                    
                    cpBox.addClass('hidden');
                    
                    cp.prop('disabled', true);
                    
                    break;
            }
        });
        
        if (arp.raty.init) {
            
            $('#raty-ar').raty({
                scoreName: 'rating',
                number: arp.raty.number,
                score: arp.raty.score,
                path: '/local/modules/travelsoft.reviews/plugins/raty/img'
            });
        }
        
        
    });
    
})(window);
