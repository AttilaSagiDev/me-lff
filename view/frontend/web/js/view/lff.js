/**
 * Copyright © 2016 Magevolve Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function ($, ko, Component, customerData) {
    'use strict';

    var cartData = customerData.get('cart');

    return Component.extend({
        defaults: {
            template: 'Me_Lff/sidebar/lff'
        },

        initialize: function () {
            this._super();
            this.lff = customerData.get('lff');

            if (cartData._latestValue.summary_count === 0) {
                this.lff._latestValue.show_success = 0;
            }

        },
        getBlockTitle: function () {
            if (typeof this.lff().block_title === 'undefined') {
                return this.initBlockTitle;
            }
            return this.lff().block_title;
        },
        getBlockPriceText: function () {
            if (typeof this.lff().price_text === 'undefined') {
                return this.initPriceText;
            }
            return this.lff().price_text;
        },
        getShowBlock: function () {
            if (typeof this.lff().show_block === 'undefined') {
                return parseInt(this.initShowBlock);
            }
            return this.lff().show_block;
        },
        getShowSuccess: function () {
            if (typeof this.lff().show_success === 'undefined') {
                return parseInt(this.initShowSuccess);
            }
            return this.lff().show_success;
        },
        getSuccessMessage: function () {
            if (typeof this.lff().success_message === 'undefined') {
                return parseInt(this.initSuccessMessage);
            }
            return this.lff().success_message;
        },
        getShowProgress: function () {
            if (typeof this.lff().show_progress === 'undefined') {
                return parseInt(this.initProgress);
            }
            return this.lff().show_progress;
        },
        getShowProgressCart: function () {
            if (typeof this.lff().show_progress_cart === 'undefined') {
                return parseInt(this.initCartProgress);
            }
            return this.lff().show_progress_cart;
        },
        getShowWidgetProgress: function () {
            if (typeof this.lff().show_progress_widget === 'undefined') {
                return parseInt(this.initWidgetProgress);
            }
            return this.lff().show_progress_widget;
        },
        getProgressBar: function () {
            var width = 0;
            var until = parseInt(this.lff().progress_value);
            $('#me-lff-progress-bar').show();
            var id = setInterval(function () {
                if (width >= 100) {
                    clearInterval(id);
                } else {
                    if (width < until) {
                        width++;
                        $('#me-lff-bar').css("width", width + '%');
                    }
                }
            }, 10);

            return '<div id="me-lff-bar"></div>';
        }
    });
});
