<?php

namespace App\Controller;

use App\Component\PaymentGatewayInterface;
use App\Helper\PayPalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PayPalController extends Controller
{
    private $paypalHelper;
    private $gateway;

    public function __construct(PayPalHelper $paypalHelper, PaymentGatewayInterface $gateway)
    {
        $this->paypalHelper = $paypalHelper;
        $this->gateway = $gateway;
    }

    /**
     * @Route("/paypal/token")
     */
    public function getToken()
    {
        return new JsonResponse([
            'token'  => $this->paypalHelper->getClientToken(),
            'amount' => getenv('PAYMENT_AMOUNT')
        ]);
    }

    /**
     * @Route("/paypal/pay")
     * @param Request $request
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function pay(Request $request): Response
    {
        if(!$request->isMethod('POST')) {
            return new Response('Not allowed', 405);
        }

        $requestContent = json_decode($request->getContent(), true);
        $paymentNonce = $requestContent['payload']['nonce'];

        $this->gateway->makePayment([
            'amount' => getenv('PAYMENT_AMOUNT'),
            'nonce' => $paymentNonce
        ]);

        if($this->gateway->validatePayment() === true) {
            //todo: generate room

            $success = true;
            $responseCode = 200;
        } else {
            $success = false;
            $responseCode = 400;
        }

        return new JsonResponse(['success' => $success], $responseCode);

    }

}