(function ($) {
    'use strict';

    window.tpgFixLazyLoad = function () {
        $('.tpg-el-main-wrapper').each(function () {
            // jetpack Lazy load
            $(this).find('img.jetpack-lazy-image:not(.jetpack-lazy-image--handled)').each(function () {
                $(this).addClass('jetpack-lazy-image--handled').removeAttr('srcset').removeAttr('data-lazy-src').attr('data-lazy-loaded', 1);
            });
            //
            $(this).find('img.lazyload').each(function () {
                var src = $(this).attr('data-src') || '';
                if (src) {
                    $(this).attr('src', src).removeClass('lazyload').addClass('lazyloaded');
                }
            });
        });
    };

    window.initTpg = function () {

        $('.tpg-el-main-wrapper').each(function () {
            var container = $(this),
                str = $(this).attr("data-layout"),
                id = $.trim(container.attr('id')),
                scID = $.trim(container.attr("data-sc-id")),
                containerOffsetTop = container.offset().top,
                el_load_count = 0;


            //Get Elementor data
            var el_settings = container.data("el-settings"),
                el_query = container.data("el-query"),
                el_authors = el_query ? (el_query.hasOwnProperty('author__in') ? el_query.author__in : null) : null,
                el_path = container.data("el-path");

            $('.filter-left-wrapper.swiper').each(function () {
                var filter_slider = $(this).get(0);
                var slider_per_item = $(this).data('per-page');
                var slider_per_item_mobile = $(this).data('per-page-mobile');
                var slider_per_item_tablet = $(this).data('per-page-tablet');
                if (typeof Swiper == 'undefined') {
                    return;
                }
                var filterSwiper = new Swiper('.swiper', {

                    spaceBetween: 0,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: slider_per_item_mobile || 'auto'
                        },
                        768: {
                            slidesPerView: slider_per_item_tablet || 'auto'
                        },
                        1024: {
                            slidesPerView: slider_per_item || 'auto'
                        }

                    }
                });
            })

            //Get TPG Elementor block data

            var $default_order_by = $('.rt-order-by-action .order-by-default', container),
                $default_order = $('.rt-sort-order-action .rt-sort-order-action-arrow', container),
                $taxonomy_filter = $('.rt-filter-item-wrap.rt-tax-filter', container),
                author_filter = $('.rt-filter-item-wrap.rt-author-filter', container),
                $pagination_wrap = $('.rt-pagination-wrap', container),
                $loadmore = $('.rt-loadmore-action', container),
                $infinite = $('.rt-infinite-action', container),
                $page_prev_next = $('.rt-cb-page-prev-next', container),
                $page_numbers = $('.rt-page-numbers', container),
                html_loading = '<div class="rt-loading-overlay"></div><div class="rt-loading rt-ball-clip-rotate"><div></div></div>',
                preLoader = container.find('.tpg-pre-loader'),
                loader = container.find(".rt-content-loader"),
                filter_container = $(".rt-layout-filter-container", container),
                contentLoader = container.children(".rt-row.rt-content-loader"),
                search_wrap = container.find(".rt-search-filter-wrap"),
                tpg_order = '',
                tpg_order_by = '',
                tpg_taxonomy = '',
                tpg_author = '',
                tpg_term = '',
                tpg_search = '',
                tpg_paged = 1,
                temp_total_pages = parseInt($pagination_wrap.attr('data-total-pages'), 10),
                tpg_total_pages = typeof (temp_total_pages) != 'undefined' && temp_total_pages != '' ? temp_total_pages : 1,
                temp_posts_per_page = parseInt($pagination_wrap.attr('data-posts-per-page'), 10),
                pagination_type = $pagination_wrap.attr('data-type'),
                tpg_posta_per_page = typeof (temp_posts_per_page) != 'undefined' && temp_posts_per_page != '' ? temp_posts_per_page : 3,
                infinite_status = 0,
                paramsRequest = {},
                mIsotopWrap = '',
                IsotopeWrap = '',
                isMasonary = $('.rt-row.rt-content-loader.tpg-masonry', container),
                isIsotop = $(".rt-tpg-isotope", container),
                IsoButton = $(".rt-tpg-isotope-buttons", container),
                IsoDropdownFilter = $("select.isotope-dropdown-filter", container),
                isCarousel = $('.rt-swiper-holder', container),
                placeholder_loading = function () {
                    if (loader.find('.rt-loading-overlay').length == 0) {
                        loader.addClass('tpg-pre-loader');
                        loader.append(html_loading);
                    }
                },
                remove_placeholder_loading = function () {
                    loader.find('.rt-loading-overlay, .rt-loading').remove();
                    loader.removeClass('tpg-pre-loader');
                    $loadmore.removeClass('rt-lm-loading');
                    $page_numbers.removeClass('rt-lm-loading');
                    $infinite.removeClass('rt-active-elm');
                    search_wrap.find('input').prop("disabled", false);
                },
                check_query = function (queryArgs = null) {
                    if ($taxonomy_filter.length > 0) {
                        tpg_taxonomy = $taxonomy_filter.attr('data-taxonomy');
                        var term;
                        if ($taxonomy_filter.hasClass('rt-filter-button-wrap')) {
                            term = $("> .sub-button-group .rt-filter-button-item.selected", filter_container).attr('data-term');
                            if (term === undefined) {
                                term = $taxonomy_filter.find('.rt-filter-button-item.selected').attr('data-term');
                            }
                        } else {
                            term = $(".rt-filter-wrap > .sub-dropdown-wrap.rt-filter-dropdown-wrap .term-default", filter_container).attr('data-term');
                            if (term === undefined) {
                                term = $(".parent-dropdown-wrap.rt-filter-dropdown-wrap .term-default", filter_container).attr('data-term');
                            }
                        }
                        if (typeof (term) != 'undefined' && term != '') {
                            tpg_term = term;
                        }
                    }
                    if (author_filter.length > 0) {
                        var author = '';
                        if (author_filter.hasClass('rt-filter-button-wrap')) {
                            author = author_filter.find('.rt-filter-button-item.selected').attr('data-author') || '';
                        } else {
                            author = $(".rt-filter-dropdown-default.term-default", author_filter).attr('data-term') || '';
                        }
                        if (author && author !== '') {
                            tpg_author = author;
                        }
                    }

                    if ($default_order_by.length > 0) {
                        var order_by_param = $default_order_by.attr('data-order-by');
                        if (typeof (order_by_param) != 'undefined' && order_by_param != '' && (order_by_param.toLowerCase())) {
                            tpg_order_by = order_by_param;
                        }
                    }

                    if ($default_order.length > 0) {
                        var order_param = $default_order.attr('data-sort-order');
                        if (typeof (order_param) != 'undefined' && order_param != '' && (order_param == 'DESC' || order_param == 'ASC')) {
                            tpg_order = order_param;
                        }
                    }
                    if (search_wrap.length > 0) {
                        tpg_search = $.trim(search_wrap.find('input').val());
                    }
                    var archive = container.data('archive') || '',
                        archive_value = container.data('archive-value') || '';

                    paramsRequest = {
                        'scID': scID,
                        'order': tpg_order,
                        'order_by': tpg_order_by,
                        'taxonomy': tpg_taxonomy,
                        'author': tpg_author,
                        'term': tpg_term,
                        'paged': tpg_paged,
                        'action': 'tpgElLayoutAjaxAction',
                        'search': tpg_search,
                        'archive': archive,
                        'archive_value': archive_value,
                        'rttpg_nonce': rttpg.nonce,
                        'el_settings': el_settings,
                        'el_query': el_query,
                        'el_path': el_path
                    };


                    if (null !== queryArgs && queryArgs.hasOwnProperty('key')) {
                        paramsRequest.el_is_click = queryArgs.key;

                        if ('taxonomy' === queryArgs.key) {
                            var elQueryRef = jQuery.parseJSON(container.attr("data-el-query"));
                            if (tpg_term === 'all') {
                                paramsRequest.el_query.tax_query = elQueryRef.tax_query;
                            } else {
                                paramsRequest.el_query.tax_query = [
                                    {
                                        "taxonomy": queryArgs.tax,
                                        "field": "term_id",
                                        "terms": [queryArgs.value]
                                    }
                                ];
                            }
                        }

                        if ('author' === queryArgs.key) {

                            if (queryArgs.value !== 'all') {
                                paramsRequest.el_query.author__in = [queryArgs.value];
                            } else {
                                if (el_authors !== null) {
                                    paramsRequest.el_query.author__in = [el_authors];
                                } else {
                                    delete paramsRequest.el_query.author__in;
                                }
                            }
                        }

                        if ('orderby' === queryArgs.key) {
                            paramsRequest.el_query.orderby = queryArgs.value;
                        }

                        if ('order' === queryArgs.key) {
                            paramsRequest.el_query.order = queryArgs.value;
                        }

                        if ('search' === queryArgs.key) {
                            paramsRequest.el_query.s = queryArgs.value;
                        }

                    }

                },

                infinite_scroll = function () {
                    if (infinite_status == 1 || $infinite.hasClass('rt-hidden-elm') || $pagination_wrap.length == 0) {
                        return;
                    }
                    var ajaxVisible = $pagination_wrap.offset().top,
                        ajaxScrollTop = $(window).scrollTop() + $(window).height();

                    if (ajaxVisible <= (ajaxScrollTop) && (ajaxVisible + $(window).height()) > ajaxScrollTop) {
                        infinite_status = 1; //stop inifite scroll
                        tpg_paged = tpg_paged + 1;
                        $infinite.addClass('rt-active-elm');
                        var queryArgs = {
                            'key': 'load_on_scroll',
                            'value': tpg_paged
                        };
                        ajax_action(true, true, tpg_paged, queryArgs);
                    }
                },

                generateData = function (number) {
                    var result = [];
                    for (var i = 1; i < number + 1; i++) {
                        result.push(i);
                    }
                    return result;
                },

                change_icon = function (fontawesome, flaticon) {
                    if (rttpg.iconFont === 'fontawesome') {
                        fontawesome = (fontawesome === 'fab fa-twitter' ? 'fab fa-x-twitter' : fontawesome);
                        return fontawesome;
                    } else {
                        flaticon = (flaticon === 'twitter' ? 'twitter-x' : flaticon);
                        return 'flaticon-' + flaticon;
                    }
                },

                createPagination = function () {
                    if ($page_numbers.length > 0) {

                        $page_numbers.pagination({
                            dataSource: generateData(tpg_total_pages * parseFloat(tpg_posta_per_page)),
                            pageSize: parseFloat(tpg_posta_per_page),
                            autoHidePrevious: true,
                            autoHideNext: true,
                            prevText: '<i class="' + change_icon("fa fa-angle-double-left", "left-arrow") + '" aria-hidden="true"></i>',
                            nextText: '<i class="' + change_icon("fa fa-angle-double-right", "right-arrow") + '" aria-hidden="true"></i>'
                        });
                        $page_numbers.addHook('beforePaging', function (pagination) {
                            infinite_status = 1;
                            tpg_paged = pagination;
                            $page_numbers.addClass('rt-lm-loading');
                            $page_numbers.pagination('disable');
                            var queryArgs = {
                                'key': 'ajax_pagination',
                                'value': pagination
                            };

                            ajax_action(true, false, pagination, queryArgs);

                        });
                        if (tpg_total_pages <= 1) {
                            $page_numbers.addClass('rt-hidden-elm');
                        } else {
                            $page_numbers.removeClass('rt-hidden-elm');
                        }
                    }
                },

                load_gallery_image_popup = function () {
                    container.each(function () {
                        var self = $(this);
                        if ($.fn.magnificPopup) {
                            self.magnificPopup({
                                delegate: 'a.tpg-zoom',
                                type: 'image',
                                gallery: {
                                    enabled: true
                                },
                                mainClass: 'mfp-fade'
                            });
                        }
                    });
                },

                ajax_action = function (page_request = false, append = false, step = null, queryArgs = null) {

                    if (!page_request) {
                        tpg_paged = 1;
                    }

                    check_query(queryArgs);

                    if (page_request === true && tpg_total_pages > 1 && paramsRequest.paged > tpg_total_pages) {
                        remove_placeholder_loading();
                        return;
                    }

                    if (null !== queryArgs && 'taxonomy' === queryArgs.key) {
                        //Post offset for ajax pagination:
                        paramsRequest.el_query.offset = 0;

                    }

                    if (null !== queryArgs && 'ajax_pagination' === queryArgs.key) {
                        //Post offset for ajax pagination:
                        if (el_settings.pagination_type === 'pagination_ajax') {
                            paramsRequest.el_query.offset = parseInt(el_settings.posts_per_page) * (step - 1);
                        }
                    }

                    if (null !== queryArgs && 'load_on_scroll' === queryArgs.key) {
                        //Post offset for ajax pagination:
                        if (el_settings.pagination_type === 'load_on_scroll') {
                            paramsRequest.el_query.offset = parseInt(el_settings.posts_per_page) * (step - 1);
                        }
                    }

                    if (null !== queryArgs && 'search' === queryArgs.key) {
                        //Post offset for ajax pagination:
                        paramsRequest.el_query.offset = 0;
                    }

                    if (null !== queryArgs && ('load_more' === queryArgs.key || 'pagination' === queryArgs.key)) {
                        el_load_count += 1;
                        if ('load_more' === el_settings.pagination_type || 'load_on_scroll' === el_settings.pagination_type) {
                            paramsRequest.el_query.offset = paramsRequest.el_query.posts_per_page * el_load_count;
                        }
                    }

                    $.ajax({
                        url: rttpg.ajaxurl,
                        type: 'POST',
                        data: paramsRequest,
                        cache: false,
                        beforeSend: function () {
                            placeholder_loading();
                        },
                        success: function (data) {
                            if (!data.error) {
                                tpg_paged = data.paged;

                                tpg_total_pages = data.total_pages;
                                if (data.paged >= tpg_total_pages) {
                                    if ($loadmore.length) {
                                        $loadmore.addClass('rt-hidden-elm');
                                    }
                                    if ($infinite.length) {
                                        infinite_status = 1;
                                        $infinite.addClass('rt-hidden-elm');
                                    }
                                    if ($page_prev_next.length) {
                                        if (!page_request) {
                                            $page_prev_next.addClass('rt-hidden-elm');
                                        } else {
                                            $page_prev_next.find('.rt-cb-prev-btn').removeClass('rt-disabled');
                                            $page_prev_next.find('.rt-cb-next-btn').addClass('rt-disabled');
                                        }
                                    }
                                } else {
                                    if ($loadmore.length) {
                                        $loadmore.removeClass('rt-hidden-elm');
                                    }
                                    if ($infinite.length) {
                                        infinite_status = 0;
                                        $infinite.removeClass('rt-hidden-elm');
                                    }
                                    if ($page_prev_next.length) {
                                        if (!page_request) {
                                            $page_prev_next.removeClass('rt-hidden-elm');
                                        } else {
                                            if (data.paged == 1) {
                                                $page_prev_next.find('.rt-cb-prev-btn').addClass('rt-disabled');
                                                $page_prev_next.find('.rt-cb-next-btn').removeClass('rt-disabled');
                                            } else {
                                                $page_prev_next.find('.rt-cb-prev-btn').removeClass('rt-disabled');
                                                $page_prev_next.find('.rt-cb-next-btn').removeClass('rt-disabled');
                                            }
                                        }
                                    }
                                }

                                if (data.data) {

                                    paramsRequest.el_query.offset = data.el_query.offset;

                                    if (append) {
                                        if (isIsotop.length) {
                                            IsotopeWrap.append(data.data)
                                                .isotope('appended', data.data)
                                                .isotope('reloadItems')
                                                .isotope('updateSortData')
                                                .isotope();
                                            container.trigger('tpg_item_before_load');
                                            tpgFixLazyLoad();
                                            IsotopeWrap.imagesLoaded(function () {
                                                preFunction();
                                                IsotopeWrap.isotope();
                                                container.trigger('tpg_item_after_load');
                                            });
                                            if (IsoButton.attr('data-count')) {
                                                isoFilterCounter(container, IsotopeWrap);
                                            }
                                        } else if (isMasonary.length) {
                                            container.trigger('tpg_item_before_load');
                                            tpgFixLazyLoad();
                                            mIsotopWrap.append(data.data).isotope('appended', data.data).isotope('updateSortData').isotope('reloadItems');
                                            mIsotopWrap.imagesLoaded(function () {
                                                mIsotopWrap.isotope();
                                                container.trigger('tpg_item_after_load');
                                            });
                                        } else {
                                            contentLoader.append(data.data);
                                            container.trigger('tpg_item_before_load');
                                            container.trigger('tpg_item_after_load');
                                        }
                                    } else {
                                        if (isMasonary.length) {
                                            mIsotopWrap.html(data.data).isotope('appended', data.data).isotope('reloadItems');
                                            container.trigger('tpg_item_before_load');
                                            tpgFixLazyLoad();
                                            mIsotopWrap.imagesLoaded(function () {
                                                mIsotopWrap.isotope();
                                                container.trigger('tpg_item_after_load');
                                            });
                                        } else {
                                            contentLoader.html(data.data);
                                            container.trigger('tpg_item_before_load');
                                            container.trigger('tpg_item_after_load');
                                        }

                                        $('html, body').animate({scrollTop: containerOffsetTop - 130}, 800);

                                    }

                                }

                                contentLoader.imagesLoaded(function () {
                                    preFunction();
                                    remove_placeholder_loading();
                                });
                                if (!page_request) {
                                    createPagination();
                                    load_gallery_image_popup();
                                }
                            } else {
                                remove_placeholder_loading();
                            }

                        },
                        error: function (error) {
                            remove_placeholder_loading();
                        }
                    });
                    if ($('.paginationjs-pages .paginationjs-page', $page_numbers).length > 0) {
                        $page_numbers.pagination('enable');
                    }
                },

                subTax = function (self) {
                    var subList = $(".rt-filter-sub-tax", self).clone();

                    var filterWrapData = self.parents('.rt-filter-button-wrap'),
                        filterData = filterWrapData.data('filter');

                    subList.on('click', '.rt-filter-button-item', function () {
                        $(this).parents('.rt-filter-sub-tax').find('.rt-filter-button-item').removeClass('selected');
                        $(this).addClass('selected');

                        var queryArgs = {
                            'key': filterData,
                            'value': $(this).data('term'),
                            'offset': 0
                        };

                        if (filterData == 'taxonomy') {
                            queryArgs.tax = filterWrapData.data('taxonomy');
                        }

                        ajax_action(false, false, null, queryArgs);
                        // ajax_action();
                    });
                    if (subList !== undefined) {
                        filter_container.append(subList);
                    }
                };

            switch (pagination_type) {
                case 'load_more':
                    $loadmore.on('click', function () {
                        $(this).addClass('rt-lm-loading');
                        tpg_paged = tpg_paged + 1;
                        var queryArgs = {
                            'key': 'load_more',
                            'value': tpg_paged
                        };
                        ajax_action(true, true, null, queryArgs);
                    });
                    break;
                case 'pagination_ajax':
                    createPagination();
                    load_gallery_image_popup();
                    break;
                case 'pagination':
                    break;
                case 'load_on_scroll':
                    $(window).on('scroll load', function () {
                        infinite_scroll();
                    });
                    break;
                case 'page_prev_next':
                    if (tpg_paged == 1) {
                        $page_prev_next.find('.rt-cb-prev-btn').addClass('rt-disabled');
                    }
                    if (tpg_paged == tpg_total_pages) {
                        $page_prev_next.find('.rt-cb-next-btn').addClass('rt-disabled');
                    }
                    if (tpg_total_pages == 1) {
                        $page_prev_next.addClass('rt-hidden-elm');
                    }
                    break;
            }

            if (str) {
                var qsRegex,
                    buttonFilter;
                if (preLoader.find('.rt-loading-overlay').length == 0) {
                    preLoader.append(html_loading);
                }
                if (isCarousel.length) {
                    isCarousel.imagesLoaded(function () {

                        if (typeof Swiper == 'undefined') {
                            return;
                        }
                        var sliderWrapper = container.find('.rt-swiper-holder');
                        var sliderLayout = container.data('layout');

                        container.removeClass('loading');
                        setTimeout(function () {
                            sliderWrapper.parents('.slider-main-wrapper').animate({opacity: "1"});
                        }, 100)

                        var rtSwiperSlider = sliderWrapper.get(0),
                            sliderItem = sliderWrapper,
                            prevButton = sliderWrapper.parent().children().find(".swiper-button-prev").get(0),
                            nextButton = sliderWrapper.parent().children().find(".swiper-button-next").get(0),
                            dotPagination = sliderWrapper.parent().find(".swiper-pagination").get(0),
                            dItem = parseInt(container.attr('data-desktop-col'), 10),
                            tItem = parseInt(container.attr('data-tab-col'), 10),
                            mItem = parseInt(container.attr('data-mobile-col'), 10),
                            options = sliderItem.data('rtowl-options'),
                            rtSwiperData = {
                                slidesPerView: dItem ? dItem : 3,
                                spaceBetween: 0,
                                grabCursor: options.grabCursor,
                                allowTouchMove: options.grabCursor,
                                loop: options.loop,
                                speed: options.speed,
                                autoHeight: options.autoHeight,
                                lazy: options.lazyLoad,
                                observer: true,
                                observeParents: true,
                                breakpoints: {
                                    0: {
                                        slidesPerView: mItem ? mItem : 1,
                                        slidesPerGroup: options.slider_per_group ? (mItem ? mItem : 1) : 1
                                    },
                                    768: {
                                        slidesPerView: tItem ? tItem : 2,
                                        slidesPerGroup: options.slider_per_group ? (tItem ? tItem : 2) : 1
                                    },
                                    992: {
                                        slidesPerView: dItem ? dItem : 3,
                                        slidesPerGroup: options.slider_per_group ? (dItem ? dItem : 3) : 1
                                    }
                                }
                            };


                        if (options.nav) {
                            Object.assign(rtSwiperData, {
                                navigation: {
                                    nextEl: nextButton,
                                    prevEl: prevButton
                                },
                            });
                        }


                        if (options.dots) {
                            Object.assign(rtSwiperData, {
                                pagination: {
                                    el: dotPagination,
                                    clickable: true,
                                    dynamicBullets: options.dynamic_dots
                                }
                            });
                        }


                        // Slider Thumbnail Enable
                        if (sliderLayout === 'slider-layout11' || sliderLayout === 'slider-layout12') {
                            rtSwiperData = {
                                slidesPerView: 1,
                                centeredSlides: true,
                                loop: options.loop,
                                lazy: options.lazyLoad,
                                loopedSlides: 10,
                                speed: options.speed,
                                grabCursor: options.grabCursor,
                                allowTouchMove: options.grabCursor,
                                slidesPerGroup: 1
                            }
                            var slider_thumb = container.find('.swiper-thumb-wrapper').get(0);

                            var rtSwiperThumbData = {
                                loopedSlides: 10,
                                slidesPerView: 3,
                                spaceBetween: 20,
                                centeredSlides: true,
                                loop: options.loop,
                                slideToClickedSlide: true,
                                speed: options.speed,
                                breakpoints: {
                                    0: {
                                        slidesPerView: 3,
                                        slidesPerGroup: 1
                                    },
                                    768: {
                                        slidesPerView: 2,
                                        centeredSlides: false,
                                        slidesPerGroup: 1
                                    },
                                    992: {
                                        slidesPerView: 3,
                                        centeredSlides: true,
                                        slidesPerGroup: 1
                                    }
                                }
                            }

                            if (sliderLayout === 'slider-layout11') {
                                Object.assign(rtSwiperThumbData, {
                                    slidesPerView: 3,
                                    direction: "vertical",
                                    pagination: {
                                        el: ".swiper-thumb-pagination",
                                        type: "progressbar"
                                    },
                                    breakpoints: {
                                        0: {
                                            direction: "horizontal",
                                            centeredSlides: true,
                                            slidesPerGroup: 1
                                        },
                                        768: {
                                            direction: "vertical",
                                            centeredSlides: true,
                                            slidesPerGroup: 1
                                        }
                                    }
                                });
                            }

                            var swiperThumb = new Swiper(slider_thumb, rtSwiperThumbData);
                        }
                        //End Slider Thumbnail Enable

                        //Main Slider Start
                        if (options.autoPlay) {
                            Object.assign(rtSwiperData, {
                                autoplay: {
                                    delay: options.autoPlayTimeOut,
                                    disableOnInteraction: false,
                                    pauseOnMouseEnter: true
                                }
                            });
                        }

                        var swiper = new Swiper(rtSwiperSlider, rtSwiperData);

                        if (sliderLayout === 'slider-layout11' || sliderLayout === 'slider-layout12') {
                            swiper.controller.control = swiperThumb;
                            swiperThumb.controller.control = swiper;
                        }

                        if (options.autoPlay && options.stopOnHover) {
                            container.hover(function () {
                                swiper.autoplay.stop();
                            }, function () {
                                swiper.autoplay.start();
                            });
                        }
                        remove_placeholder_loading();
                    });

                } else if (isIsotop.length) {
                    var IsoURL = IsoButton.attr('data-url'),
                        IsoCount = IsoButton.attr('data-count');
                    if (!buttonFilter) {
                        if (IsoButton.length) {
                            buttonFilter = IsoButton.find('button.selected').data('filter');
                        } else if (IsoDropdownFilter.length) {
                            buttonFilter = IsoDropdownFilter.val();
                        }
                    }
                    container.trigger('tpg_item_before_load');
                    tpgFixLazyLoad();
                    IsotopeWrap = isIsotop.imagesLoaded(function () {
                        preFunction();
                        IsotopeWrap.isotope({
                            itemSelector: '.isotope-item',
                            masonry: {columnWidth: '.isotope-item'},
                            layoutMode: gridStyle === 'even' ? 'fitRows' : 'masonry',
                            filter: function () {
                                var $this = $(this);
                                var searchResult = qsRegex ? $this.text().match(qsRegex) : true;
                                var buttonResult = buttonFilter ? $this.is(buttonFilter) : true;
                                return searchResult && buttonResult;
                            }
                        });
                        setTimeout(function () {
                            IsotopeWrap.isotope();
                            container.trigger('tpg_item_after_load');
                            remove_placeholder_loading();
                        }, 100);
                    });
                    // use value of search field to filter
                    var $quicksearch = container.find('.iso-search-input').keyup(debounce(function () {
                        qsRegex = new RegExp($quicksearch.val(), 'gi');
                        IsotopeWrap.isotope();
                    }));

                    IsoButton.on('click touchstart', 'button', function (e) {
                        e.preventDefault();
                        buttonFilter = $(this).attr('data-filter');
                        if (IsoURL) {
                            location.hash = "filter=" + encodeURIComponent(buttonFilter);
                        } else {
                            IsotopeWrap.isotope();
                            $(this).parent().find('.selected').removeClass('selected');
                            $(this).addClass('selected');
                        }
                    });
                    if (IsoURL) {
                        windowHashChange(IsotopeWrap, IsoButton);
                        $(window).on("hashchange", function () {
                            windowHashChange(IsotopeWrap, IsoButton);
                        });
                    }
                    if (IsoCount) {
                        isoFilterCounter(container, IsotopeWrap);
                    }
                    IsoDropdownFilter.on('change', function (e) {
                        e.preventDefault();
                        buttonFilter = $(this).val();
                        IsotopeWrap.isotope();
                    });
                } else if (container.find('.rt-row.rt-content-loader.tpg-masonry').length) {

                    var masonryTarget = $('.rt-row.rt-content-loader.tpg-masonry', container);

                    container.trigger('tpg_item_before_load');
                    tpgFixLazyLoad();
                    mIsotopWrap = masonryTarget.imagesLoaded(function () {
                        preFunction();
                        mIsotopWrap.isotope({
                            itemSelector: '.masonry-grid-item',
                            masonry: {columnWidth: '.masonry-grid-item'}
                        });
                        container.trigger('tpg_item_after_load');
                        remove_placeholder_loading();
                    });
                }
            }

            $('#' + id).on('click', '.rt-search-filter-wrap .rt-action', function (e) {
                search_wrap.find('input').prop("disabled", true);
                ajax_action();
            });
            $('#' + id).on('keypress', '.rt-search-filter-wrap .rt-search-input', function (e) {
                if (e.which == 13) {
                    var filterWrapData = $(this).parents('.rt-filter-item-wrap'),
                        filterData = filterWrapData.data('filter');
                    search_wrap.find('input').prop("disabled", true);
                    var $inputVal = search_wrap.find('input').val();
                    var queryArgs = {
                        'key': 'search',
                        'value': $inputVal
                    };

                    ajax_action(false, false, null, queryArgs);
                }
            });
            $('#' + id).on('click', '.rt-filter-dropdown-wrap', function (event) {
                var self = $(this);
                self.toggleClass('active-dropdown');
            });// Dropdown click

            //TODO: Filter Taxonomy on click ajax

            $('#' + id).on('click', '.term-dropdown-item', function (event) {
                $loadmore.addClass('rt-lm-loading');
                var $this_item = $(this),
                    dropDownWrap = $this_item.parents('.rt-filter-dropdown-wrap'),
                    filterWrapData = $this_item.parents('.rt-filter-item-wrap'),
                    filterData = filterWrapData.data('filter'),
                    dropDown = $this_item.parent('.rt-filter-dropdown'),
                    default_target = dropDownWrap.find('>.rt-filter-dropdown-default'),
                    subTerms = $(".sub-dropdown-wrap", $this_item).clone();

                dropDownWrap.removeClass('active-dropdown');
                dropDownWrap.toggleClass('active-dropdown');
                default_target.attr('data-term', $this_item.attr('data-term'));
                default_target.find('>.rt-text').html($this_item.find('>.rt-text').html());
                dropDown.find('.rt-filter-dropdown-item').removeClass('selected');
                $this_item.addClass('selected');
                if (dropDownWrap.data('taxonomy')) {
                    $this_item.parents('.rt-filter-wrap').find('>.sub-dropdown-wrap').remove();
                }

                if (subTerms.length) {
                    subTerms.insertAfter(dropDownWrap);
                }
                var queryArgs = {
                    'key': filterData,
                    'value': $(this).data('term')
                };
                if (filterData == 'taxonomy') {
                    queryArgs.tax = filterWrapData.data('taxonomy');
                }
                ajax_action(false, false, null, queryArgs);
            });//term

            //TODO: Filter Order By
            $('#' + id).on('click', '.order-by-dropdown-item', function (event) {
                $loadmore.addClass('rt-lm-loading');
                var $this_item = $(this),
                    old_param = $default_order_by.attr('data-order-by'),
                    filterWrapData = $this_item.parents('.rt-filter-item-wrap'),
                    filterData = filterWrapData.data('filter'),
                    old_text = $default_order_by.find('.rt-text-order-by').html(),
                    orderByQueyr = $this_item.data('order-by');

                $this_item.parents('.rt-order-by-action').removeClass('active-dropdown');
                $this_item.parents('.rt-order-by-action').toggleClass('active-dropdown');
                $default_order_by.attr('data-order-by', $this_item.attr('data-order-by'));
                $default_order_by.find('.rt-text-order-by').html($this_item.html());
                $this_item.attr('data-order-by', old_param);
                $this_item.html(old_text);

                var queryArgs = {
                    'key': filterData,
                    'value': $this_item.data('order-by')
                };

                ajax_action(false, false, null, queryArgs);
            });//Order By

            //TODO: Filter Sort Order
            $('#' + id).on('click', '.rt-sort-order-action', function (event) {
                $loadmore.addClass('rt-lm-loading');
                var $this_item = $(this),
                    filterData = $this_item.data('filter'),
                    $sort_order_elm = $('.rt-sort-order-action-arrow', $this_item),
                    sort_order_param = $sort_order_elm.attr('data-sort-order');
                if (typeof (sort_order_param) != 'undefined' && sort_order_param.toLowerCase() == 'desc') {
                    $default_order.attr('data-sort-order', 'ASC');
                } else {
                    $default_order.attr('data-sort-order', 'DESC');
                }
                var queryArgs = {
                    'key': filterData,
                    'value': sort_order_param == "DESC" ? "ASC" : "DESC"
                };

                ajax_action(false, false, null, queryArgs);
            });//Sort Order

            //TODO: Filter
            $taxonomy_filter.on('click', '.rt-filter-button-item', function () {
                var self = $(this),
                    filterWrapData = self.parents('.rt-filter-button-wrap'),
                    filterData = filterWrapData.data('filter');
                self.parents('.rt-filter-button-wrap').find('.rt-filter-button-item').removeClass('selected');
                self.addClass('selected');
                $("> .rt-filter-sub-tax", filter_container).remove();
                subTax(self);
                var queryArgs = {
                    'key': filterData,
                    'value': $(this).data('term'),
                    'offset': 0
                };
                if (filterData == 'taxonomy') {
                    queryArgs.tax = filterWrapData.data('taxonomy');
                }
                ajax_action(false, false, null, queryArgs);
            });

            //TODO: Filter Author
            author_filter.on('click', '.rt-filter-button-item', function () {
                var self = $(this);
                self.parents('.rt-filter-button-wrap').find('.rt-filter-button-item').removeClass('selected');
                self.addClass('selected');
                var queryArgs = {
                    'key': 'author',
                    'value': $(this).data('term')
                };
                ajax_action(false, false, null, queryArgs);
            });

            $page_prev_next.on('click', '.rt-cb-prev-btn', function (event) {
                if (tpg_paged <= 1) {
                    return;
                }
                tpg_paged = tpg_paged - 1;
                ajax_action(true, false);
            });

            $page_prev_next.on('click', '.rt-cb-next-btn', function (event) {
                if (tpg_paged >= tpg_total_pages) {
                    return;
                }
                tpg_paged = tpg_paged + 1;
                ajax_action(true, false);
            });

            load_gallery_image_popup();

            container.trigger("tpg_loaded");
        });
    };

    // initTpg();
    setTimeout(initTpg, 500);

    $(window).on('load resize', function () {
        setTimeout(function () {
            //tpgPostElementDynamicHeight();
            tpgBottomScriptLoader();
        }, 400);

        var win = $(this);
        if (win.width() >= 992) {
            overlayIconResizeTpg();
            mdPopUpResize();
            rtAlertPosition();
        }
    });

    function tpgPostElementDynamicHeight() {
        // $('.tomove').appendTo('#menu-item');
        $('.post-meta-tags').each(function () {
            var pMeta = $(this).get(0);
            $(this).attr('style', '--tpg-meta-height:' + pMeta.scrollHeight + 'px');
        });

        $('.tpg-el-excerpt').each(function () {
            var pExcerpt = $(this).get(0);
            $(this).attr('style', '--tpg-excerpt-height:' + pExcerpt.scrollHeight + 'px');
        });

        $('.rt-tpg-social-share').each(function () {
            var pShare = $(this).get(0);
            $(this).attr('style', '--tpg-share-height:' + pShare.scrollHeight + 'px');
        });
        $('.post-footer').each(function () {
            var pFooter = $(this).get(0);
            $(this).attr('style', '--tpg-footer-height:' + pFooter.scrollHeight + 'px');
        });
    }

    function tpgBottomScriptLoader() {
        $(".bottom-script-loader").fadeOut(500, function () {
            // fadeOut complete. Remove the loading div
            $(".bottom-script-loader").remove(); //makes page more lightweight
        });
    }

    $(document).on({
        mouseenter: function () {
            if ($('.rt-tpg-container').length < 1) {
                return;
            }
            var $this = $(this);
            var $parent = $(this).parents('.tpg-el-main-wrapper');

            var id = $parent.attr('id').replace("rt-tpg-container-", "");
            var $title = $this.attr('title');
            $tooltip = '<div class="rt-tooltip" id="rt-tooltip-' + id + '">' +
                '<div class="rt-tooltip-content">' + $title + '</div>' +
                '<div class="rt-tooltip-bottom"></div>' +
                '</div>';
            $('body').append($tooltip);
            var $tooltip = $('body > .rt-tooltip');
            var tHeight = $tooltip.outerHeight();
            var tBottomHeight = $tooltip.find('.rt-tooltip-bottom').outerHeight();
            var tWidth = $tooltip.outerWidth();
            var tHolderWidth = $this.outerWidth();
            var top = $this.offset().top - (tHeight + tBottomHeight) + 14;
            var left = $this.offset().left;
            $tooltip.css('top', top + 'px');
            $tooltip.css('left', left + 'px');
            $tooltip.css('opacity', 1);
            $tooltip.show();
            if (tWidth <= tHolderWidth) {
                var itemLeft = (tHolderWidth - tWidth) / 2;
                left = left + itemLeft;
                $tooltip.css('left', left + 'px');
            } else {
                var itemLeft = (tWidth - tHolderWidth) / 2;
                left = left - itemLeft;
                if (left < 0) {
                    left = 0;
                }
                $tooltip.css('left', left + 'px');
            }
        },
        mouseleave: function () {
            $('body > .rt-tooltip').remove();
        }
    }, '.rt-tpg-social-share a');

    $('.rt-wc-add-to-cart').on('click', function (e) {
        e.preventDefault();
        var $pType = $(this).data('type'),
            $pID = $(this).data('id'),
            self = $(this);
        if (rttpg.woocommerce_enable_ajax_add_to_cart == "no") {
            window.location = self.attr('href');
        } else {
            if ($pType == 'simple') {
                if ($pID) {
                    var data = "id=" + $pID,
                        cart_text = "<div class='rt-woo-view-cart'><a href='" + wc_add_to_cart_params.cart_url + "'>" + wc_add_to_cart_params.i18n_view_cart + "</a></div>";
                    AjaxCall($(this), 'addToCartWc', data, function (data) {
                        if (!data.error) {
                            $('body').append("<div class='rt-response-alert'><div class='rt-alert'>" + data.msg + cart_text + "<span class='cross'>X</span></div></div>");
                            rtAlertPosition();
                            if (rttpg.woocommerce_cart_redirect_after_add == "yes") {
                                window.location.href = wc_add_to_cart_params.cart_url
                            }
                        }
                    });
                }
            } else {
                window.location = self.attr('href');
            }
        }
        return false;
    });

    $(document).on('click', '.wc-product-holder .reset_variations', function (e) {
        e.preventDefault();
        $(".variations").find('select').val("").trigger("change").find('option:first-child').attr("selected", "selected");
    });

    $(document).on('click', '.rt-response-alert span.cross', function () {
        $(this).parents('.rt-response-alert').fadeOut(1500, function () {
            $(this).remove();
        });
    });

    function rtAlertPosition() {
        var target = $('.rt-alert');
        target.css({
            left: ($(window).width() - target.outerWidth()) / 2,
            top: ($(window).height() - target.outerHeight()) / 2
        });
    }

    function windowHashChange(isotope, IsoButton) {
        var $hashFilter = decodeHash() || '';
        if (!$hashFilter) {
            $hashFilter = IsoButton.find('button.selected').attr('data-filter') || '';
            $hashFilter = $hashFilter ? $hashFilter : '*';
        }
        $hashFilter = $hashFilter || '*';
        isotope.isotope({
            filter: $hashFilter
        });
        IsoButton.find("button").removeClass("selected");
        IsoButton.find('button[data-filter="' + $hashFilter + '"]').addClass("selected");
    }

    function decodeHash() {
        var $matches = location.hash.match(/filter=([^&]+)/i);
        var $hashFilter = $matches && $matches[1];
        return $hashFilter && decodeURIComponent($hashFilter);
    }

    function isoFilterCounter(container, isotope) {
        var total = 0;
        container.find('.rt-tpg-isotope-buttons button').each(function () {
            var self = $(this),
                filter = self.attr("data-filter"),
                itemTotal = isotope.find(filter).length;
            if (filter != "*") {
                self.find('span').remove();
                self.append("<span> (" + itemTotal + ") </span>");
                total = total + itemTotal;
            }
        });
        container.find('.rt-tpg-isotope-buttons button[data-filter="*"]').find('span').remove();
        container.find('.rt-tpg-isotope-buttons button[data-filter="*"]').append("<span> (" + total + ") </span>");
    }

    // debounce so filtering doesn't happen every millisecond
    function debounce(fn, threshold) {
        var timeout;
        return function debounced() {
            if (timeout) {
                clearTimeout(timeout);
            }

            function delayed() {
                fn();
                timeout = null;
            }

            setTimeout(delayed, threshold || 100);
        };
    }

    function preFunction() {
        overlayIconResizeTpg();
    }

    $(".tpg-el-main-wrapper a.disabled").each(function () {
        $(this).prop("disabled", true);
        $(this).removeAttr("href");
    });

    function animation() {
        var $pHolder = $('#rt-popup-wrap');
        if (parseInt($pHolder.css('marginLeft')) === 0) {
            $('body, html').removeClass('rt-model-open');
        }
        $pHolder.animate({
            marginLeft: parseInt($pHolder.css('marginLeft'), 10) == 0 ?
                $pHolder.outerWidth() : 0
        }).promise().done(function () {
            if (parseInt($pHolder.css('marginLeft')) > 0) {
                $pHolder.remove();
            } else {
                $('body, html').addClass('rt-model-open');
            }
        });
    }

    $(document).on('click', '.tpg-el-main-wrapper .tpg-single-popup', function (e) {
        e.preventDefault();

        var id = $(this).attr("data-id"),
            wrap_id = $(this).parents('.tpg-el-main-wrapper').attr('id').replace("rt-tpg-container-", "");
        var data = "action=tgpSinglePopUp&id=" + id;

        $.ajax({
            type: "post",
            url: rttpg.ajaxurl,
            data: data,
            beforeSend: function () {
                $('body, html').addClass('rt-model-open');
                $("#rt-modal").addClass('md-show rt-modal-' + wrap_id);
                $("#rt-modal .rt-md-content-holder").html('<div class="rt-md-loading">Loading...</div>');
            },
            success: function (data) {
                $("#rt-modal .rt-md-content-holder").html(data.data);
                tpgMdScriptLoad()
            },
            error: function () {
                $('body, html').removeClass('rt-model-open');
                console.log('Error');
            }
        });
    });
    $('.md-close, .md-overlay').on('click', function (e) {
        e.preventDefault();
        $('body, html').removeClass('rt-model-open');
        $("#rt-modal").removeClass('md-show');
        $("#rt-modal .rt-md-content-holder").html('');
    });
    $(window).bind('keydown', function (event) {
        if (event.keyCode === 27) { // Esc
            $('body, html').removeClass('rt-model-open');
            $("#rt-modal").removeClass('md-show');
            $("#rt-modal .rt-md-content-holder").html('');
        }
    });

    $(document).on('click', '.tpg-el-main-wrapper .tpg-multi-popup', function (e) {
        e.preventDefault();

        var current;
        var id = $(this).data("id");
        current = id;
        var itemArray;
        var item_count_holder = $(this).parents('.tpg-el-main-wrapper').find('.rt-content-loader .rt-grid-item'),
            wrap_id = $(this).parents('.tpg-el-main-wrapper').attr('id').replace("rt-tpg-container-", "");
        if (item_count_holder.length) {
            itemArray = item_count_holder.map(function () {
                return $(this).data("id");
            }).get();
        }
        var data = "action=tgpMultiPagePopUp&id=" + id;
        $.ajax({
            type: "post",
            url: rttpg.ajaxurl,
            data: data,
            beforeSend: function () {
                initPopupTeamPro(wrap_id);
                setLevelTgpPro(current, itemArray);
            },
            success: function (data) {
                $("#rt-popup-wrap .rt-popup-content").html(data.data);
                tpgMdScriptLoad();
            },
            error: function () {
                $('body, html').removeClass('rt-model-open');
                $("#rt-popup-wrap .rt-popup-content").html("<p>Loading error!!!</p>");
            }
        });

        $('.rt-popup-next').on('click', function () {
            rightClick();
        });
        $('.rt-popup-prev').on('click', function () {
            leftClick();
        });
        $('.rt-popup-close').on('click', function () {
            animation();
        });

        $(window).bind('keydown', function (event) {
            if (event.keyCode === 27) { // Esc
                animation();
            } else if (event.keyCode === 37) { // left arrow
                leftClick();
            } else if (event.keyCode === 39) { // right arrow
                rightClick();
            }
        });

        function rightClick() {
            var nextId = nextItem(current, itemArray);
            current = nextId;
            var data = "action=tgpMultiPagePopUp&id=" + current;
            $.ajax({
                type: "post",
                url: rttpg.ajaxurl,
                data: data,
                beforeSend: function () {
                    setLevelTgpPro(current, itemArray);
                    $('#rt-popup-wrap .rt-popup-content').html('<div class="rt-popup-loading"></div>');
                },
                success: function (data) {
                    $('#rt-popup-wrap .rt-popup-content').html(data.data);
                }
            });
        }

        function leftClick() {
            var prevId = prevItem(current, itemArray);
            current = prevId;
            var data = "action=tgpMultiPagePopUp&id=" + current;
            $.ajax({
                type: "post",
                url: rttpg.ajaxurl,
                data: data,
                beforeSend: function () {
                    setLevelTgpPro(current, itemArray);
                    $('#rt-popup-wrap .rt-popup-content').html('<div class="rt-popup-loading"></div>');
                },
                success: function (data) {
                    $('#rt-popup-wrap .rt-popup-content').html(data.data);
                }
            });
        }

        return false;
    });

    function initPopupTeamPro(wrap_id) {
        var html = '<div id="rt-popup-wrap" class="rt-popup-wrap rt-popup-singlePage-sticky rt-popup-singlePage rt-popup-wrap-' + wrap_id + '">' +
            '<div class="rt-popup-content">' +
            '<div class="rt-popup-loading"></div>' +
            '</div>' +
            '<div class="rt-popup-navigation-wrap">' +
            '<div class="rt-popup-navigation">' +
            '<div class="rt-popup-prev" title="Previous (Left arrow key)" data-action="prev"></div>' +
            '<div class="rt-popup-close" title="Close (Esc arrow key)" data-action="close"></div>' +
            '<div class="rt-popup-next" title="Next (Right arrow key)" data-action="next"></div>' +
            '<div class="rt-popup-singlePage-counter"><span class="ccurrent"></span> of <span class="ctotal"></span></div>' +
            '</div>' +
            '</div>' +
            '</div>';
        $("body").append(html);
        var $pHolder = $('#rt-popup-wrap');
        $pHolder.css('display', 'block');
        var navHeight = $pHolder.find('.rt-popup-navigation-wrap').height();
        $pHolder.find('.rt-popup-content').css('padding-top', navHeight + "px");
        animation();
    }

    function nextItem(current, list) {
        var index = list.indexOf(current);
        index++;
        if (index >= list.length)
            index = 0;
        return list[index];
    }

    function prevItem(current, list) {
        var index = list.indexOf(current);
        index--;
        if (index < 0)
            index = list.length - 1;
        return list[index];
    }

    function setLevelTgpPro(current, list) {
        var index = list.indexOf(current) + 1;
        var count = list.length;
        $(".ccurrent").text(index);
        $(".ctotal").text(count);
    }

    function navResize() {
        var $pHolder = jQuery('#rt-popup-wrap');
        $pHolder.css('display', 'block');
    }

    $(document).on('click', ".wc-product-holder .wc-tabs li a", function (e) {
        e.preventDefault();
        var container = $(this).parents('.wc-tabs-wrapper');
        var nav = container.children('.wc-tabs');
        var content = container.children(".panel.entry-content");
        var $this, $id;
        $this = $(this);
        $id = $this.attr('href');
        content.hide();
        nav.find('li').removeClass('active');
        $this.parent().addClass('active');
        container.find($id).show();
    });
    $(document).on('click', "a.woocommerce-main-image.zoom", function (e) {
        e.preventDefault();
        return false;
    });

    $(document).on('click', '.wc-product-holder .single_add_to_cart_button', function (e) {
        e.preventDefault();
        var $this = $(this),
            msgTarget = $this.parents(".wc-add-to-cart"),
            $pID = msgTarget.find('form button[name="add-to-cart"]').val(),
            qtn = msgTarget.find('form input[name="quantity"]').val();
        //var data = rttpg.nonceID + "=" + rttpg.nonce + "&action=addToCartWc&id=" + $pID;
        if (rttpg.woocommerce_enable_ajax_add_to_cart === "no") {
            $this.parents('form').submit();
        } else {
            var data = "id=" + $pID + "&qtn=" + qtn,
                cart_text = "<div class='rt-woo-view-cart'><a href='" + wc_add_to_cart_params.cart_url + "'>" + wc_add_to_cart_params.i18n_view_cart + "</a></div>";
            AjaxCall($(this), 'addToCartWc', data, function (data) {
                if (!data.error) {
                    $('body').append("<div class='rt-response-alert'><div class='rt-alert'>" + data.msg + cart_text + "<span class='cross'>X</span></div></div>");
                    rtAlertPosition();
                    if (rttpg.woocommerce_cart_redirect_after_add === "yes") {
                        window.location.href = wc_add_to_cart_params.cart_url
                    }
                } else {
                    $('body').append("<div class='rt-response-alert'><div class='rt-alert'>" + data.msg + "</div></div>");
                    rtAlertPosition();
                    $("body").children('.rt-response-alert').fadeOut(2000, function () {
                        $(this).remove();
                    });
                }
            });
        }
        return false;
    });

    function AjaxCall(element, action, arg, handle) {
        var data = '';
        if (action) data = "action=" + action;
        if (arg) data = arg + "&action=" + action;
        if (arg && !action) data = arg;

        var n = data.search(rttpg.nonceID);
        if (n < 0) {
            data = data + "&" + rttpg.nonceID + "=" + rttpg.nonce;
        }

        $.ajax({
            type: "POST",
            url: rttpg.ajaxurl,
            data: data,
            beforeSend: function () {
                element.append("<span class='rt-spine-loading'></span>");
            },
            success: function (data) {
                element.find(".rt-spine-loading").remove();
                handle(data);
            }
        });
    }

    function overlayIconResizeTpg() {
        $('.overlay').each(function () {
            var holder_height = jQuery(this).height();
            var target = $(this).children('.link-holder');
            var targetd = $(this).children('.view-details');
            var a_height = target.height();
            var ad_height = targetd.height();
            var h = (holder_height - a_height) / 2;
            var hd = (holder_height - ad_height) / 2;
            target.css('top', h + 'px');
            targetd.css('margin-top', hd + 'px');
        });
    }

    function mdPopUpResize() {
        var target = jQuery("#rt-modal .rt-md-content-holder"),
            targetHeight = target.outerHeight(),
            title = target.find(".md-header").outerHeight(),
            contentHeight = targetHeight - title - 25;
        target.find(".rt-md-content").height(contentHeight + "px");
    }

    window.tpgMdScriptLoad = function () {
        mdPopUpResize();
        jQuery('.rt-md-content, .rt-popup-content').niceScroll({
            background: 'rgba(0, 0, 0, 0.3)',
            cursorcolor: rttpg.primaryColor || "#06f",
            cursorborder: 'none',
            cursorwidth: '3px'
        })
    };

    window.tpgWcFunctionRun = function () {
        jQuery(".wc-product-holder .tabs.wc-tabs li:first-child a").trigger('click');
        jQuery(document).ready(function () {
            jQuery('a.woocommerce-main-image.zoom').zoom();
        });

        new Swiper("#rt-product-gallery.hasImg", {
            slidesPerView: 1,
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            }
        });
    };

    function tpg_page_builder_show() {
        if($('.builder-content').length > 0){
            $('.builder-content').removeClass('content-invisible');
        }
    }

    $(window).load(function () {
        tpg_page_builder_show();
    });

    // Elementor Frontend Load
    $(window).on('elementor/frontend/init', function () {
        if (elementorFrontend.isEditMode()) {
            elementorFrontend.hooks.addAction('frontend/element_ready/widget', function () {
                initTpg();
                setTimeout(function () {
                    tpgPostElementDynamicHeight();
                    tpgBottomScriptLoader();
                }, 400);
            });
        }
    });


})(jQuery);



