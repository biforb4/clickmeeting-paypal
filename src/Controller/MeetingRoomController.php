<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class MeetingRoomController extends Controller
{
    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function index(Request $request)
    {
        $form = $this->buildSignupForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $this->session->set('nickname', $formData['nickname']);
            $this->session->set('email', $formData['email']);

            return $this->redirectToRoute('confirm');
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm", name="confirm")
     * @return Response | RedirectResponse
     */
    public function confirm()
    {
        $nickname = $this->session->get('nickname');
        $email = $this->session->get('email');

        if(empty($nickname) || empty($email)) {
            return $this->redirectToRoute('index');
        }

        if($this->session->has('conferenceRoomUrl')) {
            $this->session->remove('conferenceRoomUrl');
        }

        return $this->render('confirm.html.twig', [
            'nickname' => $nickname,
            'email'    => $email,
            'amount'   => getenv('PAYMENT_AMOUNT'),
        ]);
    }

    /**
     * @Route("/error")
     */
    public function error()
    {
        return $this->render('error.html.twig');
    }

    /**
     * @Route("/success")
     */
    public function success()
    {
        if(!$this->session->has('conferenceRoomUrl')) {
           return $this->redirectToRoute('index');
        }
        return $this->render('success.html.twig', ['url' => $this->session->get('conferenceRoomUrl')]);
    }

    /**
     * @return FormInterface
     */
    private function buildSignupForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('nickname', TextType::class, [
                'required'    => true,
                'constraints' => [new Length(['min' => 3, 'max' => 15])]
            ])
            ->add('email', EmailType::class, [
                'required'    => true,
                'constraints' => [new Email()]
            ])
            ->add('save', SubmitType::class, ['label' => 'Go'])
            ->getForm();
    }

}