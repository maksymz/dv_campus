define([
    'jquery',
    'dvCampus_customerPreferences_form'
], function ($) {
    'use strict';

    $.widget('dvCampusCustomerPreferences.sidebar', {
        options: {
            sidebarOpenButton: '.dv-campus-customer-preferences-open-button',
            editButton: '#dv-campus-customer-preferences-edit-button',
            closeSidebar: '#dv-campus-customer-preferences-close-sidebar-button',
            customerPreferencesList: '#dv-campus-customer-preferences-list',
            form: '#dv-campus-customer-preferences-form'
        },

        /**
         * @private
         */
        _create: function () {
            $(document).on('dvCampus_CustomerPreferences_openPreferences.dvCampus_customerPreferences', $.proxy(this.openPreferences, this));
            $(this.options.closeSidebar).on('click.dvCampus_customerPreferences', $.proxy(this.closePreferences, this));
            $(this.options.editButton).on('click.dvCampus_customerPreferences', $.proxy(this.editPreferences, this));

            // make the hidden form visible after the styles are initialized
            $(this.element).show();
        },

        /**
         * @private
         */
        _destroy: function () {
            $(document).off('dvCampus_CustomerPreferences_openPreferences.dvCampus_customerPreferences');
            $(this.options.closeSidebar).off('click.dvCampus_customerPreferences');
            $(this.options.editButton).off('click.dvCampus_customerPreferences');
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
        },

        /**
         * Open popup with the form to edit preferences
         */
        editPreferences: function () {
            $(this.options.form).data('mage-modal').openModal();
        }
    });

    return $.dvCampusCustomerPreferences.sidebar;
});
