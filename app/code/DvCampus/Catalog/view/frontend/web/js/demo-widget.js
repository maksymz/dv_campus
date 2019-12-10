define(
    [
        'jquery',
        'jquery/ui',
        'mage/translate'
    ],
    function ($) {
        'use strict';

        $.widget('dvCampus.catalog_demoFunction', {
            options: {
                text: 'Default text'
            },

            _create: function () {
                this.append();
            },

            append: function () {
                var tag = $('<p></p>').html($.mage.__(this.options.text));
                $(this.element).append(tag);
            }
        });

        return $.dvCampus.catalog_demoFunction;
    }
);