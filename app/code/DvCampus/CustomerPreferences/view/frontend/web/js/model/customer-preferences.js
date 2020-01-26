define([
    'ko'
], function (ko) {
    'use strict';

    // Simple storage for customer preferences.
    // This may not be an optimal way to pass attribute labels, but this is a demo of data exchange between components
    return {
        preferences: ko.observableArray([])
    };
});
