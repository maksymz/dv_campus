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

        inputValue: ko.observable(),

        initObservable: function () {
            this._super();
            this.inputValue.subscribe(function (newValue) {
                console.log(newValue);
            });

            return this;
        }
    });

    // Start rewriting form into the Knockout component
    $.widget('dvCampusCustomerPreferences.form', {
        options: {
            action: ''
        },

        /**
         * @private
         */
        _create: function () {
            this.modal = $(this.element).modal({
                buttons: []
            });

            $(this.element).on('submit.dvCampus_customerPreferences', $.proxy(this.savePreferences, this));

            console.log(customerData.get('customer-preferences')());
            customerData.get('customer-preferences').subscribe(function (value) {
                console.log(value);
            });
        },

        _destroy: function () {
            this.modal.closeModal();
            $(this.element).off('submit.dvCampus_customerPreferences');
            this.modal.destroy();
        },

        savePreferences: function () {
            if (!this.validateForm()) {
                return;
            }

            this.ajaxSubmit();
        },

        /**
         * Validate request form
         */
        validateForm: function () {
            return $(this.element).validation().valid();
        },

        /**
         * Submit request via AJAX. Add form key to the post data.
         */
        ajaxSubmit: function () {
            var formData = new FormData($(this.element).get(0));

            formData.append('form_key', $.mage.cookies.get('form_key'));
            formData.append('isAjax', 1);

            $.ajax({
                url: this.options.action,
                data: formData,
                processData: false,
                contentType: false,
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
        },
    });

    return $.dvCampusCustomerPreferences.form;
});
