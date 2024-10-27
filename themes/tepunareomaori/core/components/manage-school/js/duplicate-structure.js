jQuery(document).ready(function ($) {

    // Manage teacher classrooms
    $(document).on('click', '#groups-dir-list #duplicate-structure', function (e) {
        e.preventDefault();
        var $this = $(this);

        // Open confirmation popup
        $.magnificPopup.open({
            items: {
                src: $this.attr('href'),
                type: 'inline'
            },
            fixedContentPos: true,
            fixedBgPos: true,
            closeBtnInside: true,
            closeOnBgClick: false,
            closeOnContentClick: false,
            removalDelay: 300,
            mainClass: 'mfp-fade',
            callbacks: {
                open: function () {

                    // Confirm duplicate structure
                    $('.confirm_duplicate_structure').on('click', function (e) {
                        e.preventDefault();

                        var security = $(this).data('security');
                        var school_id = $(this).data('school-id');
                        var title_content = $('.duplicate-structure-content-title_text');
                        var footer_content = $('.duplicate-structure-content-footer');
                        var preloader = $('.tprm-preloader');
                        var body_content = $('.duplicate-structure-content-body');

                        if (school_id) {

                            $(footer_content).hide();
                            $(body_content).hide();
                            $(preloader).show();
                            $(title_content).text(MCL_data.duplicating_structure_in_progress);

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'duplicate_structure',
                                    security: security,
                                    payload: 'duplicate_structure_payload',
                                    school_id: school_id,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result, textstatus) {
                                    console.log(result);
                                    $(title_content).text(MCL_data.duplicating_structure_success);
                                    $(body_content).find('.change_after_complete').text(MCL_data.duplicating_structure_success);
                                    $(body_content).find('.hide_after_complete').hide();
                                    $(body_content).show();
                                    $(preloader).hide();
                                    $('#close_duplicate_structure').show();

                                    $('#close_duplicate_structure').on('click', function (e) {
                                        $.magnificPopup.close();

                                        setTimeout(() => {
                                            bp.Nouveau.objectRequest({
                                                object: 'groups',
                                                scope: 'personal',
                                                filter: 'active',
                                                page: 1,
                                                extras: false,
                                                //group_year: selectedYear
                                            }).done(function (response) {
                                                var count = response.data.scopes.all;
                                                $('#subgroups-groups-li').find('span.count').text(count)
                                            });
                                            $(document).trigger(
                                                'bb_trigger_toast_message',
                                                [
                                                    '',
                                                    '<div>' + MCL_data.duplicating_structure_success + '</div>',
                                                    'success',
                                                    null,
                                                    true,
                                                ]
                                            );
                                        }, 2000);
                                    })

                                },
                                error: function (result) {
                                    console.log(result);
                                    $(title_content).text(result);
                                    $(body_content).find('.change_after_complete').text(result);
                                    $(body_content).find('.hide_after_complete').hide();
                                    $(body_content).show();
                                    $(preloader).hide();
                                    $('#close_duplicate_structure').show();
                                    $(document).trigger(
                                        'bb_trigger_toast_message',
                                        [
                                            '',
                                            '<div>' + result + '</div>',
                                            'error',
                                            null,
                                            true,
                                        ]
                                    );
                                },
                            });
                        } else {
                            $(document).trigger(
                                'bb_trigger_toast_message',
                                [
                                    '',
                                    '<div>' + result + '</div>',
                                    'error',
                                    null,
                                    true,
                                ]
                            );
                        }
                    });

                    $('#cancel_duplicate_structure').off('click').on('click', function (e) {
                        $.magnificPopup.close();
                        NProgress.done();
                    });
                },
            }
        });
    });

});


