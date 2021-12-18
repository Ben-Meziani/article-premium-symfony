<?php

namespace App\Controller\Middle;

use App\Entity\Article;
use App\Manager\ArticleManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/user/payment/{id}/show", name="checkoutPayment", methods={"GET", "POST"})
     * @param Article $article
     * @return Response
     */
    public function payment(Article $article, ArticleManager $articleManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/payment.html.twig', [
            'user' => $this->getUser(),
            'intentSecret' => $articleManager->intentSecret($article),
            'article' => $article
        ]);
    }

    /**
     * @Route("/user/subscription/{id}/paiement/load", name="subscription_paiement", methods={"GET", "POST"})
     * @param Article $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function subscription(
        Article $article,
        Request $request,
        ArticleManager $articleManager
    ){
        $user = $this->getUser();

        if($request->getMethod() === "POST") {
            $resource = $articleManager->stripe($_POST, $article);

            if(null !== $resource) {
                $articleManager->create_subscription($resource, $article, $user);

                return $this->render('user/reponse.html.twig', [
                    'article' => $article
                ]);
            }
        }

        return $this->redirectToRoute('payment', ['id' => $article->getId()]);
    }

    /**
     * @Route("/user/payment/orders", name="payment_orders", methods={"GET"})
     * @param ArticleManager $articleManager
     * @return Response
     */
    public function payment_orders(ArticleManager $articleManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('user/payment_story.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}