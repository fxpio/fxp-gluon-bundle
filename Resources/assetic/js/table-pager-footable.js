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

    // TABLE PAGER FOOTABLE CLASS DEFINITION
    // =====================================

    /**
     * @constructor
     *
     * @param htmlString|Element|Array|jQuery element
     * @param Array                           options
     *
     * @this
     */
    var TablePagerFootable = function (element, options) {
        this.$element = $(element);
        this.$table = this.$element.parents('table:first');
        this.footable = this.$table.data('footable');

        if (undefined != this.footable) {
            this.$element
                .on('table-pager-refreshed.st.tablepagerfootable', $.proxy(onPagerRefreshed, this))
            ;

        } else {
            this.destroy();
        }
    };

    /**
     * Destroy instance.
     *
     * @this
     */
    TablePagerFootable.prototype.destroy = function () {
        this.$element
            .off('table-pager-refreshed.st.tablepagerfootable', $.proxy(onPagerRefreshed, this))
            .$element.removeData('st.tablepagerfootable')
        ;
    };

    /**
     * Action on pager refreshed.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onPagerRefreshed (event) {
        this.footable.resize();
    }


    // TABLE PAGER FOOTABLE PLUGIN DEFINITION
    // ======================================

    var old = $.fn.tablePagerFootable;

    $.fn.tablePagerFootable = function (option, _relatedTarget) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('st.tablepagerfootable');
            var options = typeof option == 'object' && option;

            if (!data && option == 'destroy') {
                return;
            }

            if (!data) {
                $this.data('st.tablepagerfootable', (data = new TablePagerFootable(this, options)));
            }

            if (typeof option == 'string') {
                data[option]();
            }
        });
    };

    $.fn.tablePagerFootable.Constructor = TablePagerFootable;


    // TABLE PAGER FOOTABLE NO CONFLICT
    // ================================

    $.fn.tablePagerFootable.noConflict = function () {
        $.fn.tablePagerFootable = old;

        return this;
    };


    // TABLE PAGER FOOTABLE DATA-API
    // =============================

    $(window).on('load', function () {
        $('[data-table-pager="true"]').each(function () {
            var $this = $(this);
            $this.tablePagerFootable($this.data());
        });
    });

}(jQuery);
