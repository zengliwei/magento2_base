/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
], function ($, $t, confirm) {
    'use strict';

    return function (opts, elm) {
        $(elm).on('click', function () {
            confirm({
                content: $t(opts.msg),
                actions: {
                    confirm: function () {
                        window.location.href = opts.url;
                    }
                }
            });
            return false;
        });
    };
});
