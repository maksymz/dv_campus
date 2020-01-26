define([
    'jquery',
    'ko',
    'uiComponent',
    'DvCampus_CustomerPreferences/js/model/customer-preferences',
    // Guarantee that the form is initialized and labels are present in the model
    'dvCampus_customerPreferences_form'
], function ($, ko, Component, customerPreferencesModel) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'DvCampus_CustomerPreferences/sidebar'
        },

        customerPreferences: customerPreferencesModel.preferences,
        sidebarClass: ko.observable(''),

        /** @inheritdoc */
        initialize: function () {
            this._super();

            $(document).on(
                'dvCampus_CustomerPreferences_openPreferences.dvCampus_customerPreferences',
                $.proxy(this.openPreferences, this)
            );
        },

        /**
         * Open preferences sidebar
         */
        openPreferences: function () {
            this.sidebarClass('active');
        },

        /**
         * Open popup with the form to edit preferences
         */
        editPreferences: function () {
            $(document).trigger('dvCampus_CustomerPreferences_editPreferences');
        },

        /**
         * Close preferences sidebar
         */
        closeSidebar: function () {
            this.sidebarClass('');
            $(document).trigger('dvCampus_CustomerPreferences_closePreferences');
        }
    });
});
