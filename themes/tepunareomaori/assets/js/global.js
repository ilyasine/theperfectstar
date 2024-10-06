jQuery(document).ready(function ($) {
    // Listen for the beforeunload event (page refresh/close)
    window.addEventListener('beforeunload', function () {
        NProgress.start();
        NProgress.set(0.4);
    });

    // Listen for the pageshow event (back/forward button and page load)
    window.addEventListener('pageshow', function (event) {
        // Check if the event is not a result of a page cache
        if (event.persisted) {
            NProgress.start();
            NProgress.set(0.4);

            $(document).ready(function () {
                NProgress.done();
            });
        }
    });

    $('#school-select-button').on('click', function () {
        var $select = $('#school-select');
        if ($select.is(':hidden')) {
            $select.show();
        } else {
            $select.hide();
        }
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }
        var avatarUrl = $(option.element).data('avatar');
        var optionWithImage = $(
            '<span><img src="' + avatarUrl + '" class="img-avatar" /> ' + option.text + '</span>'
        );
        return optionWithImage;
    }

    function formatSelection(option) {
        if (!option.id) {
            return option.text;
        }
        var avatarUrl = $(option.element).data('avatar');
        var optionText = option.text;
        //return  '<img src="' + avatarUrl + '" class="img-avatar" /> ' + optionText ;
        return optionText;
    }

    $('#school-select').select2({
        templateResult: formatOption,
        templateSelection: formatSelection,
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumResultsForSearch: Infinity,
        containerCssClass: "school-select",
        dropdownCssClass: "school-select"
    });

    $('#school-select').on('change', function () {
        var security = $(this).data('security');
        var selectedOption = $(this).find('option:selected');
        var selectedUrl = selectedOption.val();
        var selectedText = selectedOption.text().trim(); // Trim the selected text
        var avatarUrl = selectedOption.data('avatar');

        /* console.log(selectedUrl);
        console.log(selectedText);
        console.log(avatarUrl);
        console.log(security); */

        NProgress.start();
        NProgress.set(0.4);

        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);
        clearInterval(interval);

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'update_last_user_school',
                security: security,
                payload: 'update_last_user_school_payload',
                selectedUrl: selectedUrl,
                selectedText: selectedText,
                avatarUrl: avatarUrl
            },
            type: 'post',
            dataType: 'json',
            success: function (result, textstatus) {
                if (result) {
                    console.log(result);

                    window.location.href = selectedUrl;

                    // Update the selected school info
                    $('#selected-school-avatar').attr('src', avatarUrl);
                    $('#selected-school-text').text(selectedText);
                    $('#selected-school-info').show();

                    $('#school-select').val(selectedUrl);

                    NProgress.done();

                }
            },
            error: function (result) {
                console.log(result);
            },
        });

    });


    $('#school-select').one('select2:open', function (e) {
        $('input.select2-search__field').prop('placeholder', TPRM_data.select_school_i18);
    });


    $('.cb-value').click(function () {
        var mainParent = $(this).parent('.toggle-btn');
        if ($(mainParent).find('input.cb-value').is(':checked')) {
            $(mainParent).addClass('active');
        } else {
            $(mainParent).removeClass('active');
        }
    })

    // Use event delegation to handle click events on all 'a' elements
    $(document).on('click', 'a, button.single_add_to_cart_button', function (e) {

        // Check if the link has a submenu, href="#", or is an internal link //notification-link
        if (
            $(this).attr('href') !== '#' &&
            !$(this).is('[href^="#"]') &&
            !$(this).is('[target="_blank"]') &&
            !$(this).is('a.user-link') &&
            !$(this).is('a.notification-link') &&
            !$(this).is('a.action-close') &&
            !$(this).is('a.expand-course') &&
            !$(this).is('a.bbp-reply-to-link') &&
            !$(this).is('a.bbp-reply-form') &&
            !$(this).is('button#bbp_reply_submit') &&
            !$(this).is('.learndash-pager-course_list .page-numbers') &&
            !$(this).is('button#bb_reply_discard_draft')
        ) {
            NProgress.start();
            NProgress.set(0.4);
        }
    });

    // Start and finish NProgress when the document is ready
    NProgress.start();
    NProgress.set(0.4);

    // Increment 
    var interval = setInterval(function () {
        NProgress.inc();
    }, 1000);

    $(document).ready(function () {
        NProgress.done();
        clearInterval(interval);
    });

    // hard reload
    $('#kwf-refresh').on("click", function () {

        /*  $('head').append('<meta http-equiv="cache-control" content="no-cache">');
         $('head').append('<meta http-equiv="expires" content="0">');
         $('head').append('<meta http-equiv="pragma" content="no-cache">')
 
         window.location.reload(true);
         localStorage.clear(); */

        $('.TPRM_refresh').magnificPopup({
            type: 'inline',
            fixedContentPos: true,
            fixedBgPos: true,
            closeBtnInside: true,
            closeOnBgClick: false,
            closeOnContentClick: false,
            removalDelay: 300,
            mainClass: 'mfp-move-horizontal',
        }).click();

        //alert('clicked')



    });

    if ($('.popup-troubleshooting-dismiss').length) {
        $('.popup-troubleshooting-dismiss').click(
            function (e) {
                e.preventDefault();
                $.magnificPopup.close();
            }
        );
    }



});
