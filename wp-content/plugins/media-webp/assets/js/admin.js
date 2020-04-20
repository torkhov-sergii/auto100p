function convert_to_webp(ID) {
    if (jQuery('.webp-meta-box').is(':visible')) {
        jQuery('.webp-meta-box').find('.spinner').addClass('is-active');
    } else {
        jQuery('.spinner').addClass('is-active');
    }
    jQuery.ajax({
        method: "POST",
        url: media_webp_object.ajaxurl,
        data: "&action=media_webp_callback&flag=1&from=media&details=post&callback_action=webp_manage&id=" + ID + "&security=" + media_webp_object.ajax_nonce,
        cache: false
    })
        .done(function (json) {
            if ( json['success'] ) {
                jQuery("#webp_button").blur().remove();
                jQuery(".webp").remove();
                if (jQuery('.webp-meta-box').is(':visible')) {
                    jQuery('.webp-meta-box').html(json['data']['meta_html']);
                } else {
                    var models = wp.media.model.Attachments.all.models;
                    jQuery(models).each(function (index) {
                        if (models[index].id == ID) {
                            models[index].attributes.webp = 'true';
                            models[index].attributes.webp_size = json['data']['size'];
                            jQuery('<div class="webp tooltip"><span class="tooltiptext"><b>File size: </b>' + models[index].attributes.webp_size + '</span></div>').insertAfter(".details");
                        }
                    });
                    webp_icon();
                }
                alert(media_webp_object.media_webp_alert_15);
            } else {
                alert(media_webp_object.media_webp_alert_2);
            }
            if (jQuery('.webp-meta-box').is(':visible')) {
                jQuery('.webp-meta-box').find('.spinner').removeClass('is-active');
            } else {
                jQuery('.spinner').removeClass('is-active');
            }
        });
}
jQuery(document).ready(function () {
    if (typeof wp.media !== 'undefined' && typeof wp.media.view.Modal.prototype !== 'undefined') {
        wp.media.view.Attachment.prototype.on('ready', function () {
            var id = jQuery(".attachment-details").data('id');
            var models = wp.media.model.Attachments.all.models;
            if (jQuery('.media-frame-title').is(':visible') && jQuery('.details').size() == 1) {
                jQuery(models).each(function (index) {
                    if (models[index].id == id && models[index].attributes.webp == 'convert') {
                        var html = '<button class="button create-webp" style="margin-left:10px" type="button" id="webp_button">' + media_webp_object.media_webp_alert_1 + '</button><span class="webp"></span>';
                        html = html + '<script type="text/javascript">jQuery("#webp_button").bind("click", function(){convert_to_webp(' + models[index].id + ');});</script>';
                        jQuery(html).insertAfter(".edit-attachment");
                    }
                });
            }
        });
    }
});