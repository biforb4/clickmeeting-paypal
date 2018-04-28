<?php

namespace App\Component;


interface PaymentGatewayInterface
{
    /**
     * Creates a transaction with the payment gateway provider from the passed in array of data
     *
     * @param array $data
     */
    public function makePayment(array $data): void;

    /**
     * Checks if payment was successful
     *
     * @return bool
     */
    public function validatePayment(): bool;
}