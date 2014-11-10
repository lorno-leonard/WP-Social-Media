jQuery(document).ready(function(){
    jQuery('.socialbar-handler').click(function(){
        var i = jQuery('.social-bar-inner').is(':visible') ? 1 : 0;
        jQuery('.social-bar-inner').slideToggle('slow');
        //jQuery.cookie("socialbar_closed", i, { expires: 1, path: '/' });
    });
});