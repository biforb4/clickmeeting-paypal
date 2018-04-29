<?php

namespace App\Controller;

use App\Component\ConferenceRoomCreatorInterface;
use App\Component\PaymentGatewayInterface;
use App\Helper\PayPalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PayPalController extends Controller
{
    private $paypalHelper;
    private $gateway;
    private $conferenceRoomCreator;

    public function __construct(PayPalHelper $paypalHelper, PaymentGatewayInterface $gateway, ConferenceRoomCreatorInterface $conferenceRoomCreator)
    {
        $this->paypalHelper = $paypalHelper;
        $this->gateway = $gateway;
        $this->conferenceRoomCreator = $conferenceRoomCreator;
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
     * @param SessionInterface $session
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function pay(Request $request, SessionInterface $session): Response
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

        try {
            if($this->gateway->validatePayment() === false) {
                throw new \RuntimeException('Failed Payment');
            }

            $conferenceRoomUrl = $this->conferenceRoomCreator->createRoom(getenv('CLICKMEETING_ROOM_NAME'));
            $session->set('conferenceRoomUrl', $conferenceRoomUrl);
            $session->remove('email');
            $session->remove('nickname');

            $success = true;
            $responseCode = 200;
        } catch (\Exception $e) {
            $this->gateway->refund();
            $success = false;
            $responseCode = 400;
        }

        return new JsonResponse(['success' => $success], $responseCode);
    }

}