/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

+function ($) {
    'use strict';

    // SELECT2 HAMMER SCROLL CLASS DEFINITION
    // ======================================

    /**
     * @constructor
     *
     * @param htmlString|Element|Array|jQuery element
     * @param Array                           options
     *
     * @this
     */
    var Select2HammerScroll = function (element, options) {
        this.guid     = jQuery.guid;
        this.options  = $.extend({}, $.fn.hammerScroll.Constructor.DEFAULTS, Select2HammerScroll.DEFAULTS, options);
        this.$element = $(element);
        this.dropdown = undefined;
        this.$content = undefined;

        this.$element.on('select2-open.st.select2hammerscroll', $.proxy(onOpen, this));
        this.$element.on('select2-close.st.select2hammerscroll', $.proxy(onClose, this));
        this.$element.on('select2-loaded.st.select2hammerscroll', $.proxy(onLoaded, this));
    };

    /**
     * Defaults options.
     *
     * @type Array
     */
    Select2HammerScroll.DEFAULTS = {
        contentWrapperClass: 'select2-hammer-scroll-content',
        useScroll:           true
    };

    /**
     * Destroy instance.
     *
     * @this
     */
    Select2HammerScroll.prototype.destroy = function () {
        var select2 = $(event.delegateTarget).data('select2');
        var $dropdown = $(select2.dropdown);

        $dropdown.off('DOMMouseScroll.st.select2hammerscroll mousewheel.st.select2hammerscroll touchmove.st.select2hammerscroll', $.proxy(blockEvent, this));
        this.$element.off('select2-open.st.select2hammerscroll', $.proxy(onOpen, this));
        this.$element.off('select2-close.st.select2hammerscroll', $.proxy(onClose, this));
        this.$element.off('select2-loaded.st.select2hammerscroll', $.proxy(onLoaded, this));

        if (undefined != this.$content) {
            this.$content.hammerScroll('destroy');
        }

        this.$element.removeData('st.select2hammerscroll');
    };

    /**
     * Action on opened select2 dropdown.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onOpen (event) {
        var select2 = $(event.delegateTarget).data('select2');
        var $dropdown = $(select2.dropdown);
        var $results = $('.select2-results', $dropdown);

        $dropdown.on('DOMMouseScroll.st.select2hammerscroll mousewheel.st.select2hammerscroll touchmove.st.select2hammerscroll', $.proxy(blockEvent, this));
        this.$content = $results.hammerScroll(this.options);
        this.$content.hammerScroll('resizeScrollbar');
    }

    /**
     * Action on closed select2 dropdown.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onClose (event) {
        var select2 = $(event.delegateTarget).data('select2');
        var $dropdown = $(select2.dropdown);

        $dropdown.off('DOMMouseScroll.st.select2hammerscroll mousewheel.st.select2hammerscroll touchmove.st.select2hammerscroll', $.proxy(blockEvent, this));
        this.$content.hammerScroll('destroy');
        this.$content = undefined;
    }

    /**
     * Action on loaded data of select2 dropdown.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onLoaded (event) {
        if (undefined != this.$content) {
            this.$content.hammerScroll('resizeScrollbar');
        }
    }

    /**
     * Prevents the default event.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function blockEvent (event) {
        event.preventDefault();
    }


    // SELECT2 HAMMER SCROLL PLUGIN DEFINITION
    // =======================================

    var old = $.fn.select2HammerScroll;

    $.fn.select2HammerScroll = function (option, _relatedTarget) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('st.select2hammerscroll');
            var options = typeof option == 'object' && option;

            if (!data && option == 'destroy') {
                return;
            }

            if (!data) {
                $this.data('st.select2hammerscroll', (data = new Select2HammerScroll(this, options)));
            }

            if (typeof option == 'string') {
                data[option]();
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

}(jQuery);
