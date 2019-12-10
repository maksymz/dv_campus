define(
    [
        'jquery',
        'jquery/ui'
    ],
    function ($) {
        'use strict';

        $.widget('dvCampus.catalog_demoFunction', {
            _create: function () {
                this.append();
            },

            append: function () {
                var tag = $('<p></p>').html('Demo function copy!!!!!!!!');
                $(this.element).append(tag);
            }
        });

        return $.dvCampus.catalog_demoFunction;
    }
);