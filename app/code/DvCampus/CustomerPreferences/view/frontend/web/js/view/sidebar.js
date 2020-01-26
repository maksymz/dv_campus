define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'DvCampus_CustomerPreferences/sidebar'
        },

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