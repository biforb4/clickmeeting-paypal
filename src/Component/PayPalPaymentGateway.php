<?php

namespace App\Component;


use App\Helper\PayPalHelper;
use Braintree\Result\Successful;

class PayPalPaymentGateway implements PaymentGatewayInterface
{

    private $payPalHelper;
    private $transactionId;
    private $paymentSuccessful = false;

    public function __construct(PayPalHelper $payPalHelper)
    {
        $this->payPalHelper = $payPalHelper;
    }

    /**
     * Creates a transaction with the payment gateway provider
     *
     * @param array $data
     */
    public function makePayment(array $data): void
    {
        $result = $this->payPalHelper->createSale($data['amount'], $data['nonce']);

        $this->transactionId = $result->transaction->id;
        if($result instanceof Successful) {
            $this->paymentSuccessful = true;
        }
    }

    /**
     * Checks if payment was successful
     *
     * @return bool
     */
    public function validatePayment(): bool
    {
        return $this->paymentSuccessful;
    }


    /**
     * Refunds transaction
     */
    public function refund(): void
    {
        $this->payPalHelper->refundTransaction($this->transactionId);
    }
}