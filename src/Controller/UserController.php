<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\User;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(PaymentRepository $appRepository, EntityManagerInterface $em)
{
    $this->appRepository = $appRepository;
    $this->em = $em;
}

    /**
     * @Route("/user/payment/{id}", name="user.payment" ,requirements={"id" = "\d+"})
     */
    public function indexpayment(User $user): Response
    {

        $this->denyAccessUnlessGranted('onlyuser', $user);
        
        $payments = $this->appRepository->findBy([
            'user' => $user,
             ]);

        return $this->render('user/index-payment.html.twig', [
            'user' => $user,
            'payments' => $payments 
        ]);
    }

    /**
     * @Route("/user/payment/{id}", name="user.delete.payment", requirements={"id" = "\d+"}, methods="DELETE")
     */
    public function deletepayment(Payment $payment, Request $request): Response
    {

        $user = $payment->getUser();
        $this->denyAccessUnlessGranted('onlyuser', $user);


        if ($this->isCsrfTokenValid('delete'. $payment->getId(), $request->get('_token'))) {
             $this->em->remove($payment);
             $this->em->flush();
             $this->addFlash('success', 'Bien supprimé avec succès');
        }
       
            return $this->redirectToRoute('user.payment',['id'=>$user->getId()]);
        }
    }



