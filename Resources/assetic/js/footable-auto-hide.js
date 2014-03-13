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
                $table.on('footable_initializing.autohide', $.proxy(initializingTable, ft.table));
                $table.on('footable_resizing.autohide', $.proxy(resizingTable, ft.table));
                $table.on('footable_resized.autohide', $.proxy(resizedTable, ft.table));
            }
        };
    }

    function initializingTable (event) {
        var ft = event.ft;
        var $table = $(this);
        var $columns = $table.find(ft.options.columnDataSelector);
        var colSize = Math.max(0, $columns.size() - 1);

        event.ft.options.breakpoints['autohide'] = $(this).parent().innerWidth();

        for (var i = 1; i < $columns.size(); i++) {
            var $column = $columns.eq(i);
            var hide = $column.attr('data-hide') || '';
            hide += ',autohide';

            while(hide.charAt(0) === ',') {
                hide = hide.substr(1);
            }

            $column.attr('data-hide', hide);

            if ('none' == $column.css('min-width')) {
                $column.css('min-width', ft.options.autoHide.minWidth);
            }
        }
    }

    function resizingTable (event) {
        var ft = event.ft;
        var $table = $(this);
        var $columns = $table.find(ft.options.columnDataSelector);
        var tableWidth = $table.parent().innerWidth();
        var colSize = Math.max(0, $columns.size() - 1);

        // reordering breakpoints
        for (var i = 0; i < ft.breakpoints.length; i++) {
            if ('autohide' == ft.breakpoints[i].name) {
                ft.breakpoints[i].width = tableWidth;
                break;
            }
        }

        ft.breakpoints.sort(function (a, b) {
            return a['width'] - b['width'];
        });

        // mask last columns
        var contentWidth = 0;
        var indexColumns = [];

        for (var i = colSize; i >= 0; i--) {
            var $column = $columns.eq(i);
            var current = null;
            var breakpoint;

            for (var j = 0; j < ft.breakpoints.length; j++) {
                breakpoint = ft.breakpoints[j];

                if (breakpoint && 'autohide' != breakpoint.name && breakpoint.width && tableWidth <= breakpoint.width) {
                    current = breakpoint;
                    break;
                }
            }

            if (null == current || !ft.columns[i].hide[current.name]) {
                var oldMaxWidth = $column.css('max-width');
                    oldMaxWidth = 'none' == oldMaxWidth ? '' : oldMaxWidth;

                $column.css('max-width', 1);

                indexColumns.push(i);
                contentWidth += Math.max($column.outerWidth(), parseInt($column.css('min-width')));
                $column.css('max-width', oldMaxWidth);
            }
        }

        // start resize
        for (var i = 0; i < indexColumns.length; i++) {
            var j = indexColumns[i];
            var $column = $columns.eq(j);

            if (contentWidth >= tableWidth) {
                contentWidth -= $column.outerWidth();

            } else {
                var hide = $column.data('hide') || '';
                hide = hide.replace('autohide', '');

                while(hide.charAt(0) === ',') {
                    hide = hide.substr(1);
                }

                while(hide.charAt(hide.length-1) === ',') {
                    hide = hide.substr(0, hide.length - 1);
                }

                $column.data('hide', hide);
            }
        }

        $table.find(ft.options.columnDataSelector).each(function () {
            var data = ft.getColumnData(this);
            ft.columns[data.index] = data;
        });
    }

    function resizedTable (event) {
        var $table = $(this);
        var $columns = $table.find(event.ft.options.columnDataSelector);

        for (var i = 0; i < $columns.size(); i++) {
            var $column = $columns.eq(i);

            $column.data('hide', $column.attr('data-hide') || '');
        }

        event.ft.redraw();
    }


    // FOOTABLE AUTO HIDE COLUMN PLUGIN DEFINITION
    // ===========================================

    w.footable.plugins.register(AutoHide, defaults);

}(jQuery, window);
