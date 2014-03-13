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
        striped: {
            enabled: true
        },
        classes: {
            striping: {
                odd:  'footable-odd',
                even: 'footable-even'
            }
        }
    };

    // FOOTABLE STRIPED CLASS DEFINITION
    // =================================

    /**
     * @constructor
     *
     * @this
     */
    function Striped () {
        this.name = "Sonatra Footable Striped";
        this.init = function (ft) {
            if (!ft.options.striped.enabled) {
                return;
            }

            var eventType = 'footable_initialized.striped footable_row_removed.striped footable_redrawn.striped footable_sorted.striped footable_filtered.striped';

            $(ft.table).on(eventType, $.proxy(stripingTable, ft.table));
        };
    }

    function stripingTable (event) {
        var ft = event.ft;
        var rowIndex = 0;

        $(ft.table).find('> tbody > tr:not(.footable-row-detail)').each(function () {
            var $row = $(this);

            // clean off old classes
            $row
                .removeClass(ft.options.classes.striping.even)
                .removeClass(ft.options.classes.striping.odd)
            ;

            if (rowIndex % 2 === 0) {
                $row.addClass(ft.options.classes.striping.even);

            } else {
                $row.addClass(ft.options.classes.striping.odd);
            }

            rowIndex++;
        });
    }


    // FOOTABLE STRIPED PLUGIN DEFINITION
    // ==================================

    w.footable.plugins.register(Striped, defaults);

}(jQuery, window);
