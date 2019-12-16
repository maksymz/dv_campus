define([
    'jquery',
    'jquery/ui',
    'mage/translate'
], function ($) {
    'use strict';

    $.widget('dvCampusCustomerPreferences.sidebar', {
        options: {
            sidebarOpenButton: '.dv-campus-customer-preferences-open-button',
            editButton: '#dv-campus-customer-preferences-edit-button',
            closeSidebar: '#dv-campus-customer-preferences-close-sidebar-button',
            customerPreferencesList: '#dv-campus-customer-preferences-list'
        },

        /**
         * @private
         */
        _create: function () {
            $(document).on('dvCampus_CustomerPreferences_openPreferences', $.proxy(this.openPreferences, this));
            $(this.options.closeSidebar).on('click.dvCampus_CustomerPreferences', $.proxy(this.closePreferences, this));

            // make the hidden form visible after the styles are initialized
            $(this.element).show();
        },

        /**
         * @private
         */
        _destroy: function () {
            $(document).off('dvCampus_CustomerPreferences_openPreferences');
            $(this.options.closeSidebar).off('click.dvCampus_CustomerPreferences');
        },

        /**
         * Open preferences sidebar
         */
        openPreferences: function () {
            $(this.element).addClass('active');
        },

        /**
         * Close preferences sidebar
         */
        closePreferences: function () {
            $(this.element).removeClass('active');
            $(this.options.sidebarOpenButton).trigger('dvCampus_CustomerPreferences_closePreferences');
        }
    });

    return $.dvCampusCustomerPreferences.sidebar;
});
