<?php

namespace App\Helper;


class PayPalHelper
{
    private $braintreeGateway;

    public function __construct()
    {
        $this->braintreeGateway = new \Braintree_Gateway([
            'accessToken' => getenv('PAYPAL_ACCESS_TOKEN')
        ]);
    }

    public function getClientToken(): string
    {
        return $this->braintreeGateway->clientToken()->generate();
    }

}