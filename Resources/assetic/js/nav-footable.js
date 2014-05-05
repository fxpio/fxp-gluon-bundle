/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global jQuery*/
/*global document*/

/**
 * @param {jQuery} $
 *
 * @typedef {NavFootable} NavFootable
 */
(function ($) {
    'use strict';

    // NAV FOOTABLE CLASS DEFINITION
    // =============================

    /**
     * @constructor
     *
     * @param {string|elements|object|jQuery} element
     * @param {object}                        options
     *
     * @this NavFootable
     */
    var NavFootable = function (element, options) {
        this.guid     = jQuery.guid;
        this.options  = $.extend({}, NavFootable.DEFAULTS, options);
        this.$element = $(element);
        this.$content = $('.' + this.options.classFootable, this.$element.attr('href'));
        this.footable = this.$content.data('footable');
    },
        old;

    /**
     * Defaults options.
     *
     * @type {object}
     */
    NavFootable.DEFAULTS = {
        classFootable: 'footable'
    };

    /**
     * Refresh the footable.
     *
     * @this NavFootable
     */
    NavFootable.prototype.refresh = function () {
        if (null !== this.footable) {
            this.footable.resize();
        }
    };

    /**
     * Destroy instance.
     *
     * @this NavFootable
     */
    NavFootable.prototype.destroy = function () {
        $(document).off('shown.bs.tab.data-api.st.navfootable', '[data-toggle="tab"], [data-toggle="pill"]');
        this.$element.removeData('st.navfootable');
    };


    // NAV FOOTABLE PLUGIN DEFINITION
    // ==============================

    function Plugin(option, value) {
        return this.each(function () {
            var $this   = $(this),
                data    = $this.data('st.navfootable'),
                options = typeof option === 'object' && option;

            if (!data && option === 'destroy') {
                return;
            }

            if (!data) {
                $this.data('st.navfootable', (data = new NavFootable(this, options)));
            }

            if (typeof option === 'string') {
                data[option](value);
            }
        });
    }

    old = $.fn.navFootable;

    $.fn.navFootable             = Plugin;
    $.fn.navFootable.Constructor = NavFootable;


    // NAV FOOTABLE NO CONFLICT
    // ========================

    $.fn.navFootable.noConflict = function () {
        $.fn.navFootable = old;

        return this;
    };


    // NAV FOOTABLE DATA-API
    // =====================

    $(document).on('shown.bs.tab.data-api.st.navfootable', '[data-toggle="tab"], [data-toggle="pill"]', function () {
        Plugin.call($(this), 'refresh');
    });

}(jQuery));
