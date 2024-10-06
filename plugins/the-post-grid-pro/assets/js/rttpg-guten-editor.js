(function ($) {
    'use strict';

    window.initTpg = function () {

        tpgBottomScriptLoader();

        $(document).find('.tpg-el-main-wrapper').each(function () {

            var container = $(this),
                str = $(this).attr("data-layout"),
                el_query = container.data("el-query");


            $('.filter-left-wrapper.swiper').each(function () {
                var filter_slider = $(this).get(0);
                var slider_per_item = $(this).data('per-page');
                var slider_per_item_mobile = $(this).data('per-page-mobile');
                var slider_per_item_tablet = $(this).data('per-page-tablet');
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

            var html_loading = '<div class="rt-loading-overlay"></div><div class="rt-loading rt-ball-clip-rotate"><div></div></div>',
                preLoader = container.find('.tpg-pre-loader'),
                isCarousel = $('.rt-swiper-holder', container);


            if (str) {

                if (preLoader.find('.rt-loading-overlay').length == 0) {
                    preLoader.append(html_loading);
                }
                if (isCarousel.length) {


                    isCarousel.imagesLoaded(function () {

                        var sliderWrapper = container.find('.rt-swiper-holder');
                        var sliderLayout = container.attr('data-layout');
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
                                grabCursor: false,
                                allowTouchMove: false,
                                loop: false,
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

                            if (slider_thumb.swiper) {
                                slider_thumb.swiper.destroy(false, true);
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

                        if (rtSwiperSlider.swiper) {
                            rtSwiperSlider.swiper.destroy(false, true);
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

                    });

                }
            }

        });
    };


    function tpgBottomScriptLoader() {
        $(".bottom-script-loader").fadeOut(500, function () {
            // fadeOut complete. Remove the loading div
            $(".bottom-script-loader").remove(); //makes page more lightweight
        });
    }


})(jQuery);