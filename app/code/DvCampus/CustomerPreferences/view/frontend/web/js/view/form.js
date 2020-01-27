define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'DvCampus_CustomerPreferences/js/model/customer-preferences',
    'DvCampus_CustomerPreferences/js/action/save-preferences',
    'Magento_Ui/js/modal/modal'
], function ($, ko, Component, customerData, customerPreferencesModel, savePreferences) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'DvCampus_CustomerPreferences/form',
            customerPreferences: customerData.get('customer-preferences'),
            action: '',
            attributes: [],
            listens: {
                customerPreferences: 'updateCustomerPreferences'
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            $(document).on(
                'dvCampus_CustomerPreferences_editPreferences.dvCampus_customerPreferences',
                $.proxy(this.openModal, this)
            );

            // value must be observable - otherwise the list will not be rendered when customerData is updated
            this.attributes.forEach(function (attributeData) {
                attributeData.value = ko.observable('');
            });

            this.updateCustomerPreferences(this.customerPreferences());
        },

        /**
         * Populate customer preferences with data from the localStorage
         */
        updateCustomerPreferences: function (newCustomerPreferences) {
            this.attributes.forEach(function (attributeData) {
                if (newCustomerPreferences[attributeData['attribute_code']] !== undefined) {
                    attributeData.value(newCustomerPreferences[attributeData['attribute_code']]);
                }
            });

            customerPreferencesModel.preferences(this.attributes);
        },

        /**
         * Init modal from the component HTML
         */
        initModal: function (formElement) {
            this.modal = $(formElement).modal({
                buttons: []
            });
        },

        /**
         * Open modal form with preferences for editing
         */
        openModal: function () {
            this.modal.modal('openModal');
        },

        /**
         * Submit request via AJAX. Add form key to the post data.
         */
        savePreferences: function () {
            var payload = {
                attributes: this.attributes,
                'form_key': $.mage.cookies.get('form_key'),
                isAjax: 1
            };

            savePreferences(payload, this.action)
                .done(function () {
                    this.modal.modal('closeModal');
                }.bind(this));
        }
    });
});
