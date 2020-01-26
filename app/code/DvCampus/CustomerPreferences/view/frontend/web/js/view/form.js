define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'DvCampus_CustomerPreferences/js/model/customer-preferences',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal'
], function ($, ko, Component, customerData, customerPreferencesModel, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'DvCampus_CustomerPreferences/form',
            customerPreferences: customerData.get('customer-preferences'),
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
                attributeData.value(newCustomerPreferences[attributeData['attribute_code']]);
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

            $.ajax({
                url: this.action,
                data: payload,
                type: 'post',
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                beforeSend: function () {
                    $('body').trigger('processStart');
                },

                // @TODO: in case or connection or server-side error - show mailto link
                /** @inheritdoc */
                success: function (response) {
                    $('body').trigger('processStop');
                    alert({
                        title: $.mage.__('Success'),
                        content: response.message
                    });
                },

                /** @inheritdoc */
                error: function () {
                    $('body').trigger('processStop');
                    alert({
                        title: $.mage.__('Error'),
                        content: $.mage.__(
                            'Your preferences can\'t be saved. Please, contact us if ypu see this message.'
                        )
                    });
                },

                /** @inheritdoc */
                complete: function () {
                    this.modal.modal('closeModal');
                }
            });
        }
    });
});
