(function ($) {
    // Elementor template builder
    $('body.post-type-tpg_builder #wpcontent').on('click', '.page-title-action, .row-title, .row-actions [class="edit"] a', function (e) {
        e.preventDefault();
        var _self = $(e.target);
        var href = _self.attr("href");
        var post_id = 0;
        var saved_template = '';

        if (href) {
            var url = href.slice(href.indexOf('?') + 1).split('&');
            if (url && url[0].split('=')[1]) {
                post_id = parseInt(url[0].split('=')[1]);
            }
        }

        if (post_id) {
            saved_template = 'saved-template';
        }

        var modal = new TpgpModal({
            footer: false,
            wrapClass: 'heading templeate-builder-popups ' + saved_template
        });

        var data = {
            action: 'tpgp_el_templeate_builder',
            post_id: post_id ? post_id : null,
            rttpg_nonce: tpgp_el_tb.rttpg_nonce
        };

        $.ajax({
            url: tpgp_el_tb.ajaxurl,
            data: data,
            type: "POST",
            beforeSend: function () {
                modal.addModal().addLoading();
            },
            success: function (response) {
                modal.removeLoading();
                //console.log( response )
                modal.addTitle(response.title);
                if (response.success) {
                    modal.content(response.content);
                }
            },
            error: function (e) {

            }
        });
    });

    $('body.post-type-tpg_builder').on('click', '#tpgp_tb_button', function (e) {
        e.preventDefault();
        const _self = $(e.target);
        var page_name_field = _self.parents('.templeate-builder-popups').find('#tpgp_tb_template_name');
        var page_name = page_name_field.val();
        var page_type = _self.parents('.templeate-builder-popups').find('#tpgp_tb_template_type').val();
        var default_template = _self.parents('.templeate-builder-popups').find('#default_template:checked').val();
        var page_id = _self.parents('.templeate-builder-popups').find('#page_id').val();
        var template_edit_with = _self.parents('.templeate-builder-popups').find('#tpgp_tb_template_edit_with').val();
        var data = {
            action: 'tpgp_el_create_templeate',
            page_id: page_id ? page_id : null,
            page_name: page_name ? page_name : null,
            page_type: page_type ? page_type : null,
            default_template: default_template ? default_template : null,
            template_edit_with: template_edit_with ? template_edit_with : null,
            rttpg_nonce: tpgp_el_tb.rttpg_nonce
        };
        // console.log( data );
        if (!page_name) {
            page_name_field.next('.message').show();
        } else {
            page_name_field.next('.message').hide();
            $.ajax({
                url: tpgp_el_tb.ajaxurl,
                data: data,
                type: "POST",
                beforeSend: function () {
                    var loader_html = '<div class="tpgp-tb-loader"> <svg class="tpgp-tb-spinner" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg></div>';
                    _self.parents('.tpgp-tb-button-wrapper').append(loader_html);
                },
                success: function (response) {
                    var page_type = '';
                    _self.parents('.tpgp-tb-button-wrapper').find('.tpgp-tb-loader').remove();
                    _self.parents('.templeate-builder-popups').find('#page_id').attr("value", response.post_id);
                    _self.parents('.templeate-builder-popups').addClass('saved-template');
                    _self.parents('.templeate-builder-popups').find('.tpgp-tb-edit-button-wrapper a').attr("href", response.post_edit_url);
                    _self.parents('.templeate-builder-popups').find('.tpgp-tb-edit-button-wrapper a').text(response.editor_btn_text);

                     _self.parents('.templeate-builder-popups').find('#tpgp_tb_button').attr("disabled", 'disabled');
                    _self.parents('.templeate-builder-popups').find('.tpgp-modal-close').attr("data-save", 'saved');

                },
                error: function (e) {
                    console.log(e);
                }
            });
        }

    });

    // Switch
    $('body').on('click', 'td.column-set_default .tpgp-switch-wrapper', function (e) {
        e.preventDefault();
        const _self = $(this);
        var is_checked = _self.find('.set_default:checked').val();
        var page_id = _self.find('.set_default').val();
        var type = _self.find('.template_type').val();
        var selector_name = '.page-type-' + type;
        $('body').find(selector_name).each(function () {
            $(this).find('.set_default').prop("checked", false);
        });

        var data = {
            action: 'tpgp_el_default_template',
            page_id: !is_checked ? page_id : 0,
            template_type: type ? type : null,
            rttpg_nonce: tpgp_el_tb.rttpg_nonce
        };

        $.ajax({
            url: tpgp_el_tb.ajaxurl,
            data: data,
            type: "POST",
            success: function (response) {
                if (response.success && parseInt(response.post_id)) {
                    _self.find('.set_default').prop("checked", true);
                }
            },
            error: function (e) {
                console.log(e)
            }
        });

    });

    // Disabled Edit Button.
    // $('body').on('change input', '.templeate-builder-popups .tpgp-field', function () {
    //     $('body').find('#tpgp_tb_button').removeAttr('disabled');
    //     $('body').find('.templeate-builder-popups').removeClass('saved-template');
    // });

    // Pupups Close Event.
    $(document).on('tpgp.TpgpModal.close', function (event, wrapper) {
        var close_button = $(wrapper).find('.templeate-builder-popups .tpgp-modal-close');
        var page_data = close_button.attr("data-save");
        if ('saved' == page_data) {
            location.reload();
        }
    });

    //Remove disable attribute from save button when change others input on the builder page popup
    $('body').on('change keypress', '.tpgp-field', function(){
            $(this).closest('.tpgp-modal-body').find('#tpgp_tb_button').removeAttr("disabled");
    })

    // End elementor template builder

})(jQuery);
