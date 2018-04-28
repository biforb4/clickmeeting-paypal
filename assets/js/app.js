var axios = require('axios');

var app = (function () {
    var me = this;

    this.config = {
        paypalButton: document.getElementById('paypal-button')
    };

    this.initPayPal = function (token, amount) {
        // Create a client.
        braintree.client.create({
            authorization: token
        }, function (clientErr, clientInstance) {
            if (clientErr) {
                console.error('Error creating client:', clientErr);
                return;
            }

            // Create a PayPal Checkout component.
            braintree.paypalCheckout.create({
                client: clientInstance
            }, function (paypalCheckoutErr, paypalCheckoutInstance) {

                if (paypalCheckoutErr) {
                    console.error('Error creating PayPal Checkout:', paypalCheckoutErr);
                    return;
                }

                // Set up PayPal with the checkout.js library
                paypal.Button.render({
                    env: 'sandbox',

                    payment: function () {
                        return paypalCheckoutInstance.createPayment({
                            flow: 'checkout',
                            amount: amount,
                            currency: 'GBP'
                        });
                    },

                    onAuthorize: function (data, actions) {
                        return paypalCheckoutInstance.tokenizePayment(data)
                            .then(function (payload) {
                                axios.post('/paypal/pay', {
                                    payload: payload
                                }).then(function () {
                                    window.location.replace('/success');
                                }).catch(function () {
                                    window.location.replace('/error');
                                });
                            });
                    },

                    onCancel: function (data) {
                        console.log('checkout.js payment cancelled', JSON.stringify(data, 0, 2));
                    },

                    onError: function (err) {
                        console.error('checkout.js error', err);
                    }
                }, '#paypal-button');

            });

        });
    };

    this.init = function () {
        if (me.config.paypalButton !== null) {
            axios.get('/paypal/token').then(function (response) {
                me.initPayPal(response.data.token, response.data.amount);
            })
        }
    };

    return {
        init: me.init
    }
}());

document.addEventListener('DOMContentLoaded', function () {
    app.init();
}, false);