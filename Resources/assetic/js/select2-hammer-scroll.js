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

        var select2 = this.$element.data('select2');

        if (!select2.opts.multiple) {
            select2.selection.off('mousedown touchstart');
            select2.selection.on("mousedown touchend", 'abbr', $.proxy(onSelect2Clear, select2));
            select2.selection.on("mousedown touchend", $.proxy(onSelect2Open, select2));
        }

        // force use scroll
        this.options.useScroll = true;
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

        if (!select2.opts.multiple) {
            select2.selection.off("mousedown touchend", 'abbr', $.proxy(onSelect2Clear, select2));
            select2.selection.off("mousedown touchend", $.proxy(onSelect2Open, select2));
        }

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
     * Action on clear select2 value.
     *
     * @param jQuery.Event event
     *
     * @this (is select2 instance)
     * @private
     */
    function onSelect2Clear (event) {
        if (!this.isInterfaceEnabled()) return;
        this.clear();
        event.preventDefault();
        event.stopImmediatePropagation();
        this.close();
        this.selection.focus();
    }

    /**
     * Action on open select2 dropdown.
     *
     * @param jQuery.Event event
     *
     * @this (is select2 instance)
     * @private
     */
    function onSelect2Open (event) {
        // Prevent IE from generating a click event on the body
        var placeholder = $(document.createTextNode(''));

        this.selection.before(placeholder);
        placeholder.before(this.selection);
        placeholder.remove();

        if (!this.container.hasClass("select2-container-active")) {
            this.opts.element.trigger($.Event("select2-focus"));
        }

        if (this.opened()) {
            this.close();
        } else if (this.isInterfaceEnabled()) {
            this.open();
        }

        event.preventDefault();
        event.stopPropagation();
    }

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
        var scrollTop = $results.scrollTop();

        $dropdown.on('DOMMouseScroll.st.select2hammerscroll mousewheel.st.select2hammerscroll touchmove.st.select2hammerscroll', $.proxy(blockEvent, this));
        $results.addClass('select2-hammer-scroll');
        this.$content = $results.hammerScroll($.extend(this.options, {'scrollTop': scrollTop}));
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
        var $results = $('.select2-results', $dropdown);

        $dropdown.off('DOMMouseScroll.st.select2hammerscroll mousewheel.st.select2hammerscroll touchmove.st.select2hammerscroll', $.proxy(blockEvent, this));
        $results.removeClass('select2-hammer-scroll');
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
