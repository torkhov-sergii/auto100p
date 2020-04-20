jQuery(document).ready(function () {

    jQuery('#info p').each(function() {
        var text = jQuery(this).html();
        jQuery(this).html(text.replace(/(Google Chrome)/g, '&#39;Google Chrome&#39;&trade;'));
        text = jQuery(this).html();
        jQuery(this).html(text.replace(/( Google)/g, ' Google&trade;'));
        text = jQuery(this).html();
        jQuery(this).html(text.replace(/(Apache)/g, 'Apache&trade;'));
        text = jQuery(this).html();
        jQuery(this).html(text.replace(/(Nginx)/g, 'Nginx&trade;'));
     });
     jQuery('#info h4').each(function() {
        var text = jQuery(this).html();
        jQuery(this).html(text.replace(/(Apache)/g, 'Apache&trade;'));
        text = jQuery(this).html();
        jQuery(this).html(text.replace(/(Nginx)/g, 'Nginx&trade;'));
     });
});