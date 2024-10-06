// window.toastr = require('toastr');
// toastr.options.closeButton = true;

(function ($, window) {
    'use strict';

    $.fn.tpgpBlock = function (settings) {
        const defaults = {
            overlayCSS: {
                zIndex: 1000,
                border: 'none',
                margin: 0,
                padding: 0,
                width: '100%',
                height: '100%',
                top: 0,
                left: 0,
                background: 'rgb(255, 255, 255)',
                opacity: 0.6,
                cursor: 'wait',
                position: 'absolute',
                color: "#556b2f",
                backgroundColor: "white"
            }
        };
        const options = $.extend({}, defaults, settings || {});

        const overlayCSS = $.extend({}, defaults.overlayCSS, options.overlayCSS || {});
        return this.each(function () {
            const $element = $(this);

            if ($element.css('position') === 'static') {
                this.style.position = 'relative';
                $element.data('tpgp-block.static', true);
            }

            this.style.zoom = 1;
            const overlay = $('<div class="tpgp-loading-overlay" />').css(overlayCSS);
            $element.find('> .tpgp-loading-overlay').remove();
            $element.addClass('tpgp-loading').append(overlay);
        });
    };
    $.fn.tpgpUnblock = function () {
        return this.each(function () {
            const $element = $(this);

            if ($element.data('tpgp-block', 'static')) {
                $element.css('position', 'static'); // #22
            }

            $element.removeClass('tpgp-loading').find('> .tpgp-loading-overlay').remove();
        });
    };
    window.TpgpModal = function (options) {
        // Default options
        this.settings = $.extend({
            wrapClass: '',
            footer: true,
            header: true,
            maxWidth: 500
        }, options);
        this.modal_wrapper_element = $("<div class='tpgp-ui-modal'><div class='tpgp-modal-wrapper'>" +
            "<div class='tpgp-modal-content'>" +
            "<div class='tpgp-modal-header'><div class='tpgp-modal-title'></div> <button class='tpgp-modal-close'><i class='notice-dismiss' aria-hidden='true'></i></button></div>" +
            "<div class='tpgp-modal-body'></div>" +
            "<div class='tpgp-modal-footer'></div>" +
            "</div></div><div class='tpgp-mask-wrapper'></div></div>");
        this.show = function () {
            $(document).trigger('tpgp.TpgpModal.show');
            this.addModal();
        }
        this.addModal = function () {
            const modal = this;
            $('body')
                .append(this.modal_wrapper_element);
            this.wrapper = $('.tpgp-modal-wrapper', this.modal_wrapper_element);
            this.container = $('.tpgp-modal-content', this.modal_wrapper_element);
            this.header = $('.tpgp-modal-header', this.modal_wrapper_element);
            this.header_title = $('.tpgp-modal-title', this.header);
            this.body = $('.tpgp-modal-body', this.modal_wrapper_element);
            this.footer = $('.tpgp-modal-footer', this.modal_wrapper_element);
            if (this.settings.wrapClass) {
                this.wrapper.addClass(this.settings.wrapClass)
            }
            if (this.settings.header === false) {
                this.header.remove();
            }
            if (this.settings.footer === false) {
                this.footer.remove();
            }
            if (this.settings.maxWidth !== 500) {
                this.wrapper.css({
                    maxWidth: parseInt(this.settings.maxWidth, 10) + 'px'
                })
            }
            $('body').addClass('tpgp-modal-open');
            $('.tpgp-mask-wrapper, .tpgp-modal-close', this.modal_wrapper_element).on('click', function () {
                modal.removeModel();
            });

            return this;
        };

        this.addLoading = function () {
            this.body.tpgpBlock();
            return this;
        };
        this.addTitle = function (html) {
            this.header_title.html(html);
        };
        this.removeLoading = function () {
            this.body.tpgpUnblock();
            return this;
        };
        this.removeModel = function () {
            $( document ).trigger('tpgp.TpgpModal.close', this.modal_wrapper_element );
            $('body > .tpgp-ui-modal').remove();
            $('body').removeClass('tpgp-modal-open');
            return this;
        };
        this.close = function () {
            this.removeModel();
            return this;
        };
        this.content = function (html) {
            this.body.html(html);
            return this;
        };
        this.appendContent = function (html) {
            this.body.append(html);
            return this;
        }
        this.prependContent = function (html) {
            this.body.prepend(html);
            return this;
        }
        this.addFooterContent = function (html) {
            this.footer.html(html);
            return this;
        }

    };

    window.tpgpCipher = function (saltKey, deCipher) {
        let salt = saltKey;
        const textToChars = text => text.split('').map(c => c.charCodeAt(0));
        const byteHex = n => ("0" + Number(n).toString(16)).substr(-2);
        const applySaltToChar = code => textToChars(salt).reduce((a, b) => a ^ b, code);
        if (deCipher) {
            return encoded => encoded.match(/.{1,2}/g)
                .map(hex => parseInt(hex, 16))
                .map(applySaltToChar)
                .map(charCode => String.fromCharCode(charCode))
                .join('');
        }
        return text => text.split('')
            .map(textToChars)
            .map(applySaltToChar)
            .map(byteHex)
            .join('');
    }

})(jQuery, window);