/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'ko',
        'jquery',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Mageplaza_Osc/js/model/resource-url-manager',
        'mage/storage',
        'Mageplaza_Osc/js/model/osc-data',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/select-billing-address'
    ],
    function (ko,
              $,
              _,
              quote,
              resourceUrlManager,
              storage,
              oscData,
              paymentService,
              methodConverter,
              errorProcessor,
              fullScreenLoader,
              selectBillingAddressAction) {
        'use strict';

        return {
            saveShippingInformation: function () {
                var payload,
                    addressInformation = {},
                    additionInformation = oscData.getData();
                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }
                payload = {
                    addressInformation: addressInformation,                   
                    additionInformation: additionInformation
                };

                this.payloadExtender(payload);

                fullScreenLoader.startLoader();

                return storage.post(
                    resourceUrlManager.getUrlForSetCheckoutInformation(quote),
                    JSON.stringify(payload)
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        fullScreenLoader.stopLoader();
                    }
                );
            },

            payloadExtender: function (payload) {
                if (!payload.addressInformation.hasOwnProperty('shipping_address')) {
                    return payload;
                }
            }
        };
    }
);
