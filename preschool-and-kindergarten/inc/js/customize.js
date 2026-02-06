
jQuery(document).ready(function($) {
    $('body').on('click', '.flush-it', function(event) {
        $.ajax ({
            url     : preschool_and_kindergarten_cdata.ajax_url,  
            type    : 'post',
            data    : {
                action: 'flush_local_google_fonts',
                nonce: preschool_and_kindergarten_cdata.nonce
            },
            success : function(results){
                //results can be appended in needed
                $( '.flush-it' ).val(preschool_and_kindergarten_cdata.flushit);
            },
        });
    });
});