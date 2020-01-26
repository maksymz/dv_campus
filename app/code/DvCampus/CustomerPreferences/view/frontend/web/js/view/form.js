define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal'
], function ($, ko, Component, customerData, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'DvCampus_CustomerPreferences/form'
        },
        attributes: {},

        /**
         * Watch customer data change and update input values
         */
        initObservable: function () {
            var customerPreferences = customerData.get('customer-preferences')();

            this._super();

            // @TODO: JS may break if new attributes are added
            this.attributes.forEach(function (attributeData) {
                attributeData.value = customerPreferences[attributeData['attribute_code']];
            });

            customerData.get('customer-preferences').subscribe(function (newCustomerPreferences) {
                this.attributes.forEach(function (attributeData) {
                    attributeData.value = newCustomerPreferences[attributeData['attribute_code']];
                });
            }.bind(this));

            return this;
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
                        content: $.mage.__('Your preferences can\'t be saved. Please, contact us if ypu see this message.')
                    });
                }
            });
        }
    });

    // Start rewriting form into the Knockout component
    $.widget('dvCampusCustomerPreferences.form', {
        /**
         * @private
         */
        _create: function () {
            this.modal = $(this.element).modal({
                buttons: []
            });

            $(this.element).on('submit.dvCampus_customerPreferences', $.proxy(this.savePreferences, this));
        },
    });
});
