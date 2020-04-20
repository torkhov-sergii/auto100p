function cycle_conversion(IDS, flag) {
    jQuery.ajax({
        method: "POST",
        url: media_webp_object.ajaxurl,
        data: "&action=media_webp_callback&callback_action=webp_manage&id=" + IDS[IDS.length - 1] + "&security=" + media_webp_object.ajax_nonce + "&flag=" + flag,
        cache: false,
        beforeSend: function (xhr) {
            if (IDS.length == 1) {
                var new_data = { details: 'tool' };
                this.data += '&' + jQuery.param(new_data);
            }
        }
    })
        .done(function (json) {
            IDS.pop();
            var mark = parseInt(jQuery('#mark').val());
            var percentage = Math.round((100 / parseInt(jQuery('#total').text())) * mark);
            jQuery('#info').text('');
            jQuery('#total').hide();
            jQuery('#progress').text(percentage + "%");
            jQuery('#mark').val((mark + 1));
            if (IDS.length > 0) {
                cycle_conversion(IDS, flag);
            } else {
                if (flag == 1) {
                    alert(media_webp_object.media_webp_alert_1);
                } else {
                    alert(media_webp_object.media_webp_alert_2);
                }
                jQuery('#convert_spinner').removeClass('is-active');
                jQuery('.button-primary').prop("disabled", false);
                jQuery('#progress').text('');
                jQuery('.info_block_attachments').hide();
                jQuery.each(json['data'], function(i, item){
                    jQuery("#" + i).text(item);
                })
            }
        });
}

function get_IDS(flag) {
    jQuery.ajax({
        method: "POST",
        url: media_webp_object.ajaxurl,
        data: jQuery('#m_webp_form').serialize() + "&action=media_webp_callback&callback_action=webp_ids&security=" + media_webp_object.ajax_nonce + "&flag=" + flag,
        cache: false,
    })
        .done(function (json) {
            if ( json['data']['id_s'].length > 0 ) {
                jQuery('#total').text( json['data']['total'] );
                if (flag == 1) {
                    var conf_message = media_webp_object.media_webp_alert_13;
                } else {
                    var conf_message = media_webp_object.media_webp_alert_14;
                }
                if ( confirm( conf_message ) ) {
                    jQuery('#convert_spinner').addClass('is-active');
                    jQuery('.button-primary').prop("disabled", true);
                    jQuery('#info').text(media_webp_object.media_webp_alert_3);
                    jQuery('#mark').val('1');
                    jQuery('#progress').text('');
                    jQuery('.info_block_attachments').show();
                    cycle_conversion(json['data']['id_s'], flag);
                } else {
                    jQuery('#total').text('');
                    jQuery('#info').text('');
                    jQuery('#convert_spinner').removeClass('is-active');
                    jQuery('.button-primary').prop("disabled", false);
                }
            } else {
                jQuery('#info').text('');
                jQuery('#convert_spinner').removeClass('is-active');
                jQuery('.button-primary').prop("disabled", false);
                jQuery('.info_block_attachments').hide();
                if ( flag == 1 ) {
                    alert(media_webp_object.media_webp_alert_7);
                } else {
                    alert(media_webp_object.media_webp_alert_6);
                }
            }
        });
}

function theme_action(flag) {
    var conf_message;
    if (flag == 1) {
        conf_message = media_webp_object.media_webp_alert_11;
    }else{
        conf_message = media_webp_object.media_webp_alert_12;
    }
    if(confirm(conf_message)){
        jQuery.ajax({
            method: "POST",
            url: media_webp_object.ajaxurl,
            data: "&action=media_webp_callback&callback_action=webp_theme&security=" + media_webp_object.ajax_nonce + "&flag=" + flag,
            cache: false,
            beforeSend: function (xhr) {
                jQuery('.info_block_theme').show();
                jQuery('#convert_spinner_t').addClass('is-active');
                if(flag == 1){
                    msg = media_webp_object.media_webp_alert_4;
                } else {
                    msg = media_webp_object.media_webp_alert_5;
                }
                jQuery('.info_theme').text(msg);
                jQuery('.button-primary').prop("disabled", true);
            }
        })
            .success(function (json) {
                if (json['result'] > 0) {
                    if (json['result'] == 2) {
                        if (flag == 1) {
                            alert(media_webp_object.media_webp_alert_7);
                        } else {
                            alert(media_webp_object.media_webp_alert_6);
                        }
                    } else {
                          if (flag == 1) {
                            jQuery('#theme_webps_size').text(json['total_webps_size']);
                            jQuery('#theme_webp').text(json['total_images']);
                            alert(media_webp_object.media_webp_alert_8);
                        } else {
                            jQuery('#theme_webps_size').text(" 0.00 B");
                            jQuery('#theme_webp').text("0");
                            alert(media_webp_object.media_webp_alert_9);
                        }
                    }
                } else if  (json['result'] == -1) {
                    alert(media_webp_object.media_webp_alert_10);
                } else if  (json['result'] == -2) {
                    alert(media_webp_object.media_webp_alert_17);
                }
            })
            .done(function(){
                jQuery('.info_block_theme').hide();
                jQuery('.info_theme').text('');
                jQuery('#convert_spinner_t').removeClass('is-active');
                jQuery('.button-primary').prop("disabled", false);
            });
    }
}

jQuery('#button_convert').bind('click', function () {
    get_IDS(1);
});

jQuery('#button_delete').bind('click', function () {
    get_IDS(0);
});

jQuery('#button_convert_theme').bind('click', function () {
    theme_action(1);
});

jQuery('#button_delete_theme').bind('click', function () {
    theme_action(0);
});
