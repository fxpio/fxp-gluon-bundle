/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

+function ($, w) {
    'use strict';

    if (w.footable === undefined) {
        throw new Error('Please check and make sure footable.js is included in the page and is loaded prior to this script.');
    }

    /**
     * Defaults options.
     *
     * @type Array
     */
    var defaults = {
        autoHide: {
            enabled: true,
            minWidth: 90
        }
    };

    // FOOTABLE AUTO HIDE COLUMN CLASS DEFINITION
    // ==========================================

    /**
     * @constructor
     *
     * @this
     */
    function AutoHide () {
        this.name = "Sonatra Footable Auto Hide";
        this.init = function (ft) {
            if (!ft.options.autoHide.enabled) {
                return;
            }

            var $table = $(ft.table);
            var $responsive = $table.parent('.table-responsive');

            if (1 == $responsive.size()) {
                $table.on('footable_initialized.autohide', $.proxy(onInitializedTable, ft.table));
                $table.on('footable_resizing.autohide', $.proxy(onResizingTable, ft.table));
                $table.on('footable_resized.autohide', $.proxy(onResizedTable, ft.table));
            }
        };
    }

    /**
     * Initialize the config.
     *
     * @param Event event
     *
     * @private
     */
    function onInitializedTable (event) {
        var ft = event.ft;

        if (!ft.hasAnyBreakpointColumn()) {
            var $table = $(this);
            var $columns = $table.find(ft.options.columnDataSelector);

            for (var i = 0; i < $columns.size(); i++) {
                var data = ft.getColumnData($columns.get(i));
                data.hasBreakpoint = true;

                ft.columns[data.index] = data;
            }

            ft.resize();
        }
    }

    /**
     * Restore all column.
     *
     * @param Event event
     *
     * @private
     */
    function onResizingTable (event) {
        var ft = event.ft;
        var $table = $(this);
        var $columns = $table.find(ft.options.columnDataSelector);

        for (var i = 0; i < $columns.size(); i++) {
            var data = ft.getColumnData($columns.get(i));
            ft.columns[data.index] = data;
        }

        ft.redraw();
    }

    /**
     * Hides the columns in the scroll.
     *
     * @param Event event
     *
     * @private
     */
    function onResizedTable (event) {
        var ft = event.ft;
        var $table = $(this);
        var $columns = $table.find(event.ft.options.columnDataSelector);
        var tableWidth = $table.parent().innerWidth();
        var contentWidth = 0;
        var breakpointName = $table.data('breakpoint');
        var hasHiddenCol = false;

        for (var i = 0; i < $columns.size(); i++) {
            var $column = $columns.eq(i);

            contentWidth += $column.outerWidth();

            if ($column.is(":visible")) {
                if (contentWidth >= tableWidth) {
                    var data = ft.getColumnData($column.get(0));
                    data.hide[breakpointName] = true;
                    data.hasBreakpoint = true;

                    ft.columns[data.index] = data;
                    hasHiddenCol = true;
                }
            }
        }

        if (hasHiddenCol) {
            $table
                .removeClass('default breakpoint').removeClass(ft.breakpointNames)
                .addClass(breakpointName + ' breakpoint');
            ;
        }

        event.ft.redraw();
    }


    // FOOTABLE AUTO HIDE COLUMN PLUGIN DEFINITION
    // ===========================================

    w.footable.plugins.register(AutoHide, defaults);

}(jQuery, window);
