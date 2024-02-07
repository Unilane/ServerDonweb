/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require(['jquery'], function ($) {
    'use strict';
    $('#sync-products').click(function () {
        console.log("ejecucion");
        $.ajax({
        type: "POST",
        url: BASE_URL+'/syncproduct/products/importproducts?isAjax=true',
        data: {form_key: window.FORM_KEY},
        dataType: "json",
        showLoader: true,
        }).done(function (data) {
            console.log("si entra");
        });
    }); 
});