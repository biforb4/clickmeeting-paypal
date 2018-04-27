<?php

namespace App\Controller;

use App\Helper\PayPalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PayPalController extends Controller
{
    private $paypalHelper;

    public function __construct(PayPalHelper $paypalHelper)
    {
        $this->paypalHelper = $paypalHelper;
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

}