jQuery(document).ready(function ($) {

    var $submenu = $('#toplevel_page_TPRM_importer .wp-submenu li'); //toplevel_page_TPRM_importer

    var $tab_hash = window.location.search.split('?page=TPRM_importer&tab=')[1];
    $('#toplevel_page_TPRM_importer .wp-submenu li.current').removeClass('current');
    $(window).on("hashchange", function () {

        switch ($tab_hash) {
            case 'import-groups':
                $active = 2
                break;

            case 'import-users':
                $active = 3
                break;

            case 'export':
                $active = 4
                break;

            case 'frontend':
                $active = 5
                break;

            case 'mail-options':
                $active = 6
                break;

            case 'doc':
                $active = 7
                break;

            default:
                $active = 1
                break;

        }


        $submenu[$active].classList.add('current');

    }).trigger("hashchange")

});
