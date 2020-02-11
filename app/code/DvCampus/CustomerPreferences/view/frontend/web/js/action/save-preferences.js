define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    /**
     * @param {Object} payload
     * @param {String} url
     */
    return function (payload, url) {
        return $.ajax({
            url: url,
            data: payload,
            type: 'post',
            dataType: 'json',

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
                        'Your preferences can\'t be saved. Please, contact us if you see this message.'
                    )
                });
            }
        });
    };
});
