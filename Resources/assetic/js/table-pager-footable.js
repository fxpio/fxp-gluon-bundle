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
        this.$table   = $('#' + this.$element.attr('data-table-id'));
        this.footable = this.$table.data('footable');

        if (undefined != this.footable) {
            this.$element
                .on('table-pager-refreshed.st.tablepagerfootable', $.proxy(onPagerRefreshed, this))
            ;

            this.$table
                .on('footable_row_detail_updated.st.tablepagerfootable', $.proxy(onFootableRowDetailUpdated, this))
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
        this.$table
            .off('footable_row_detail_updated.st.tablepagerfootable', $.proxy(onFootableRowDetailUpdated, this))
        ;

        this.$element
            .off('table-pager-refreshed.st.tablepagerfootable', $.proxy(onPagerRefreshed, this))
            .removeData('st.tablepagerfootable')
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

    /**
     * Action on footable row detail is updated.
     *
     * @param Event event
     *
     * @this
     * @private
     */
    function onFootableRowDetailUpdated (event) {
        var $cols = this.$table.find('> thead > tr:last > th:not(:visible)');
        var $detailNames = $(event.detail).find('div.footable-row-detail-name');

        if ($cols.size() != $detailNames.size()) {
            return;
        }

        for (var i = 0; i < $detailNames.size(); i++) {
            var $col = $cols.eq(i);
            var $detail = $detailNames.eq(i);
            var $icon = $col.find('> i.table-sort-icon');

            if (undefined != $col.attr('data-col-name')) {
                $detail.attr('data-col-name', $col.attr('data-col-name'));
            }

            if (undefined != $col.attr('data-table-pager-sortable')) {
                $detail.attr('data-table-pager-sortable', $col.attr('data-table-pager-sortable'));
            }

            if (undefined != $col.attr('data-table-sort')) {
                $detail.attr('data-table-sort', $col.attr('data-table-sort'));
            }

            if ($icon.size() > 0) {
                $detail.append($icon.clone());
            }
        }
    }


    // TABLE PAGER FOOTABLE OVERRIDE TABLE PAGER DEFAULT OPTIONS DEFINITION
    // ====================================================================

    if (undefined != $.fn.tablePager) {
        $.fn.tablePager.Constructor.DEFAULTS = $.extend(true, $.fn.tablePager.Constructor.DEFAULTS, {
            selectors: {
                sortable: $.fn.tablePager.Constructor.DEFAULTS.selectors.sortable + ', > tbody > tr.footable-row-detail div.footable-row-detail-name[data-table-pager-sortable=true]'
            }
        });
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
