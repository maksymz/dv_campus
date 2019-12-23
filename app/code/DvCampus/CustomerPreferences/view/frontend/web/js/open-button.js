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
            $(this.element).on('dvCampus_CustomerPreferences_closePreferences.dvCampus_customerPreferences', $.proxy(this.closePreferences, this));
        },

        /**
         * jQuery(jQuery('.dv-campus-customer-preferences-open-button').get(0)).data('dvCampusCustomerPreferencesOpenButton').destroy()
         * @private
         */
        _destroy: function () {
            $(this.element).off('click.dvCampus_customerPreferences');
            $(this.element).off('dvCampus_CustomerPreferences_closePreferences.dvCampus_customerPreferences');
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
