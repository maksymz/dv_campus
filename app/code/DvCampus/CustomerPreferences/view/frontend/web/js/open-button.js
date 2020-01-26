define([
    'jquery',
    'jquery/ui',
    'mage/translate'
], function ($) {
    'use strict';

    $.widget('dvCampusCustomerPreferences.openButton', {
        options: {
            hideButton: true
        },

        /**
         * @private
         */
        _create: function () {
            $(this.element).on('click.dvCampus_customerPreferences', $.proxy(this.openPreferences, this));

            // generate unique hash to bind/unbind events only for this widget instance
            this.hash = Math.random().toString(36).substr(2, 9);
            $(document).on(
                'dvCampus_CustomerPreferences_closePreferences.' + this.hash,
                $.proxy(this.closePreferences, this)
            );
        },

        /**
         * @private
         */
        _destroy: function () {
            $(this.element).off('click.dvCampus_customerPreferences');
            $(this.element).off(
                'dvCampus_CustomerPreferences_closePreferences.' + this.hash
            );
        },

        /**
         * Open preferences sidebar
         */
        openPreferences: function () {
            $(document).trigger('dvCampus_CustomerPreferences_openPreferences');

            if (this.options.hideButton) {
                $(this.element).removeClass('active');
            }
        },

        /**
         * Close preferences sidebar
         */
        closePreferences: function () {
            $(this.element).addClass('active');
        }
    });

    return $.dvCampusCustomerPreferences.openButton;
});
