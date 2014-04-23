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
/*global document*/
/*global CustomEvent*/
/*global Footable*/
/*global AutoHide*/

/**
 * @param {jQuery} $
 * @param {window} w
 *
 * @typedef {AutoHide} AutoHide
 */
(function ($, w) {
    'use strict';

    if (undefined === w.footable) {
        throw new Error('Please check and make sure footable.js is included in the page and is loaded prior to this script.');
    }

    /**
     * Initialize the config.
     *
     * @param {Event} event
     *
     * @typedef {Footable} event.ft
     *
     * @private
     */
    function onInitializedTable(event) {
        var ft = event.ft,
            $table,
            $columns,
            data,
            i;

        if (!ft.hasAnyBreakpointColumn()) {
            $table = $(event.target);
            $columns = $table.find(ft.options.columnDataSelector);

            for (i = 0; i < $columns.size(); i += 1) {
                data = ft.getColumnData($columns.get(i));
                data.hasBreakpoint = true;

                ft.columns[data.index] = data;
            }

            ft.resize();
        }
    }

    /**
     * Restore all column.
     *
     * @param {Event} event
     *
     * @typedef {Footable} event.ft
     *
     * @private
     */
    function onResizingTable(event) {
        var ft = event.ft,
            $table = $(event.target),
            $columns = $table.find(ft.options.columnDataSelector),
            data,
            i;

        for (i = 0; i < $columns.size(); i += 1) {
            data = ft.getColumnData($columns.get(i));
            ft.columns[data.index] = data;
        }

        ft.redraw();
    }

    /**
     * Hides the columns in the scroll.
     *
     * @param {Event} event
     *
     * @typedef {Footable} event.ft
     *
     * @private
     */
    function onResizedTable(event) {
        var ft = event.ft,
            $table = $(event.target),
            $columns = $table.find(ft.options.columnDataSelector),
            tableWidth = $table.parent().innerWidth(),
            contentWidth = 0,
            breakpointName = $table.data('breakpoint'),
            hasHiddenCol = false,
            $column,
            data,
            i;

        $table.addClass('breakpoint');

        for (i = 0; i < $columns.size(); i += 1) {
            $column = $columns.eq(i);

            if ($column.is(":visible")) {
                contentWidth += $column.outerWidth();

                if (contentWidth >= tableWidth) {
                    data = ft.getColumnData($column.get(0));
                    data.hide[breakpointName] = true;
                    data.hasBreakpoint = true;

                    ft.columns[data.index] = data;
                    hasHiddenCol = true;
                }
            }
        }

        $table.removeClass('default breakpoint').removeClass(ft.breakpointNames);

        if (hasHiddenCol) {
            $table.addClass(breakpointName + ' breakpoint');
        }

        ft.redraw();
    }

    /**
     * Defaults options.
     *
     * @type {object}
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
     * @this AutoHide
     */
    function AutoHide() {
        this.name = "Sonatra Footable Auto Hide";
        this.init = function (ft) {
            if (!ft.options.autoHide.enabled) {
                return;
            }

            var $table = $(ft.table),
                $responsive = $table.parent('.table-responsive');

            if (1 === $responsive.size()) {
                $table.on('footable_initialized.autohide', onInitializedTable);
                $table.on('footable_resizing.autohide', onResizingTable);
                $table.on('footable_resized.autohide', onResizedTable);
            }
        };
    }


    // FOOTABLE AUTO HIDE COLUMN PLUGIN DEFINITION
    // ===========================================

    w.footable.plugins.register(AutoHide, defaults);

}(jQuery, window));
