define([
    'jquery',
    'uiComponent',
    'ko',
    'mage/translate',
    'mage/validation'
], function ($, Component, ko, $t) {
    'use strict';
    return Component.extend({
        productsResult: ko.observable(null),
        fromPrice: ko.observable(null),
        formElem: '#products-range-form',
        toPriceElem: '.field-to-price input',
        toPriceErrorElem: '.field-to-price #price-to-error',
        searched: ko.observable(false),
        initialize: function () {
            this._super();
        },

        search: function () {
            let self = this;

            $(self.toPriceErrorElem).remove();

            let minFromPrice = parseFloat(self.fromPrice()) + 0.01;
            let maxFromPrice = parseFloat(self.fromPrice()) * 5;

            $(self.toPriceElem).removeClass()
                .addClass('input-text required-entry range validate-number-range')
                .addClass(`number-range-${minFromPrice}-${maxFromPrice}`);

            $(self.formElem).validation();

            if ($(self.formElem).validation('isValid')) {
                $.ajax({
                    url: $(self.formElem).attr('action'),
                    type: 'GET',
                    showLoader: true,
                    data: $(self.formElem).serialize(),
                    contentType: "application/json; charset=utf-8",
                    dataType: "html",
                    success: function (response) {
                        if (response !== "") {
                            self.productsResult(response);
                        } else {
                            self.searched(true);
                            self.productsResult(null);
                        }
                    },
                    error: function () {
                        self.searched(true);
                        self.productsResult(null);
                    }
                });
            } else if (minFromPrice && $(self.toPriceErrorElem).length > 0) {
                $(self.toPriceErrorElem).text(
                    $t('Please enter a value between {0} and {1}.')
                        .replace('{0}', minFromPrice)
                        .replace('{1}', maxFromPrice)
                );
            }
        }
    });
});
