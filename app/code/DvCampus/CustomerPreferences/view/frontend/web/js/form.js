define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

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
        },

        _destroy: function () {
            this.modal.closeModal();
            $(this.element).off('submit.dvCampus_customerPreferences');
            this.modal.destroy();
        },

        savePreferences: function () {
            alert('saved!');
            return false;
        }
    });

    return $.dvCampusCustomerPreferences.form;
});
