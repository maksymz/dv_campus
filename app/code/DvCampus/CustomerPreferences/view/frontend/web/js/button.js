define([
    'jquery',
    'jquery/ui',
    'mage/translate'
], function ($) {
    'use strict';

    $.widget('dvCampusCustomerPreferences.button', {
        options: {
            customerPreferences: '#dv-campus-customer-preferences',
            customerPreferencesEditButton: '#dv-campus-customer-preferences-edit-button',
            customerPreferencesList: '#dv-campus-customer-preferences-list'
        },

        _create: function () {
            $(this.element).click($.proxy(this.openPreferences, this));
        },

        openPreferences: function () {
            alert('Click event works fine');
        }
    });

    return $.dvCampusCustomerPreferences.button;
});
