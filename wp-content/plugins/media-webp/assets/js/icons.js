function refresh_icons() {
    jQuery(".webp_found").each(function(){
        var filename_span = jQuery(this).closest('tr').find('td .has-media-icon');
        jQuery("<div class='webp'></div>").insertBefore(filename_span);
    });
}

function webp_icon() {
    if (typeof wp.media !== 'undefined') {
        var models = wp.media.model.Attachments.all.models;
        jQuery(models).each(function (index) {
            if (models[index].attributes.webp == 'true') {
                if (jQuery('.check-column').length > 0) {
                    jQuery("<div class='webp'/>").insertBefore("tr[id=post-" + models[index].id + "] > div:first");
                } else {
                    if (jQuery("li[data-id=" + models[index].id + "] > .webp").length) {
                        jQuery("li[data-id=" + models[index].id + "] > .webp").remove(".webp");
                    }
                    jQuery("<div class='webp'/>").insertBefore("li[data-id=" + models[index].id + "] > div:first");
                }
            }
        });
    }
}

jQuery(document).ready(function () {
    if (typeof wp.media !== 'undefined' && typeof wp.media.view.Modal.prototype !== 'undefined') {
        wp.media.view.Attachment.prototype.on('ready', function () {
            var id = jQuery(".attachment-details").data('id');
            var models = wp.media.model.Attachments.all.models;
            if (jQuery('.attachments-browser').is(':visible')) {
                webp_icon();
            }
            if (!jQuery('.media-frame-title').is(':visible')) {
                webp_icon();
            } else {
                if(jQuery('.details').size() == 1 ){
                    jQuery(models).each(function (index) {
                        if (models[index].id == id && models[index].attributes.webp == 'true') {
                            jQuery('<div class="webp tooltip"><span class="tooltiptext"><b>File size: </b>' + models[index].attributes.webp_size + '</span></div>').insertAfter(".details");
                        }
                    });
                }
            }

        });
    }
    if (typeof wp.media !== 'undefined' && typeof wp.media.view.Modal.prototype !== 'undefined') {
        wp.media.view.Modal.prototype.on('close', function () {
            webp_icon();
        });
    }
    if (jQuery('.check-column').length > 0) {
        refresh_icons();
    }
});
