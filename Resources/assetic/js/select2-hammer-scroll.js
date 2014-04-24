/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global jQuery*/
/*global window*/
/*global Select2HammerScroll*/

/**
 * @param {jQuery} $
 *
 * @typedef {Select2HammerScroll} Select2HammerScroll
 */
(function ($) {
    'use strict';

    /**
     * Action on opened select2 dropdown.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {Select2HammerScroll} Event.data The select2 hammer scroll instance
     *
     * @private
     */
    function onOpen(event) {
        var self = event.data,
            select2 = self.$element.data('select2'),
            $dropdown = $(select2.dropdown),
            $results = $('.select2-results', $dropdown).eq(0),
            scrollTop = $results.scrollTop();

        $dropdown.off('touchstart touchend touchmove mousemove-filtered');

        if (self.options.nativeScroll) {
            $results.addClass('select2-hammer-scroll-native');

        } else {
            $results.addClass('select2-hammer-scroll');
        }

        self.$content = $results.hammerScroll($.extend(self.options, {'scrollTop': scrollTop}));
        self.$content.hammerScroll('resizeScrollbar');
    }

    /**
     * Action on closed select2 dropdown.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {Select2HammerScroll} Event.data The select2 hammer scroll instance
     *
     * @private
     */
    function onClose(event) {
        var self = event.data,
            select2 = self.$element.data('select2'),
            $dropdown = $(select2.dropdown),
            $results = $('.select2-results', $dropdown);

        $results.removeClass('select2-hammer-scroll');
        $results.removeClass('select2-hammer-scroll-native');
        self.$content.hammerScroll('destroy');
        self.$content = undefined;
    }

    /**
     * Action on loaded data of select2 dropdown.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {Select2HammerScroll} Event.data The select2 hammer scroll instance
     *
     * @private
     */
    function onLoaded(event) {
        var self = event.data;

        if (undefined !== self.$content) {
            self.$content.hammerScroll('resizeScrollbar');
        }
    }

    // SELECT2 HAMMER SCROLL CLASS DEFINITION
    // ======================================

    /**
     * @constructor
     *
     * @param {string|elements|object|jQuery} element
     * @param {object}                        options
     *
     * @this Select2HammerScroll
     */
    var Select2HammerScroll = function (element, options) {
        this.guid     = jQuery.guid;
        this.options  = $.extend({}, $.fn.hammerScroll.Constructor.DEFAULTS, Select2HammerScroll.DEFAULTS, options);
        this.$element = $(element);
        this.dropdown = undefined;
        this.$content = undefined;

        this.$element.on('select2-open.st.select2hammerscroll', null, this, onOpen);
        this.$element.on('select2-close.st.select2hammerscroll', null, this, onClose);
        this.$element.on('select2-loaded.st.select2hammerscroll', null, this, onLoaded);
    },
        old;

    /**
     * Defaults options.
     *
     * @type {object}
     */
    Select2HammerScroll.DEFAULTS = {
        contentWrapperClass: 'select2-hammer-scroll-content',
        useScroll:           true,
        nativeScroll:        true
    };

    /**
     * Destroy instance.
     *
     * @this Select2HammerScroll
     */
    Select2HammerScroll.prototype.destroy = function () {
        var select2 = this.$element.data('select2');

        this.$element.off('select2-open.st.select2hammerscroll', onOpen);
        this.$element.off('select2-close.st.select2hammerscroll', onClose);
        this.$element.off('select2-loaded.st.select2hammerscroll', onLoaded);

        if (undefined !== this.$content) {
            this.$content.hammerScroll('destroy');
        }

        this.$element.removeData('st.select2hammerscroll');
    };


    // SELECT2 HAMMER SCROLL PLUGIN DEFINITION
    // =======================================

    old = $.fn.select2HammerScroll;

    $.fn.select2HammerScroll = function (option, value) {
        return this.each(function () {
            var $this   = $(this),
                data    = $this.data('st.select2hammerscroll'),
                options = typeof option === 'object' && option;

            if (!data && option === 'destroy') {
                return;
            }

            if (!data) {
                $this.data('st.select2hammerscroll', (data = new Select2HammerScroll(this, options)));
            }

            if (typeof option === 'string') {
                data[option](value);
            }
        });
    };

    $.fn.select2HammerScroll.Constructor = Select2HammerScroll;


    // SELECT2 HAMMER SCROLL NO CONFLICT
    // =========================

    $.fn.select2HammerScroll.noConflict = function () {
        $.fn.select2HammerScroll = old;

        return this;
    };


    // SELECT2 HAMMER SCROLL DATA-API
    // ==============================

    $(window).on('load', function () {
        $('[data-select2-hammer-scroll="true"]').each(function () {
            var $this = $(this);
            $this.select2HammerScroll($this.data());
        });
    });

}(jQuery));
