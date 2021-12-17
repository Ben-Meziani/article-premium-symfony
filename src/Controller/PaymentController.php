<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Payment;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    /**
     * @Route("/article/{id}/payment", name="payment")
     */
    public function index(Article $article): Response
    {
        $title = $article->getTitle();

        return $this->render('payment/index.html.twig', [
            'title' => $title,
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/{id}/checkout", name="checkout")
     */
    public function checkout($stripeSK, Article $article): Response
    {
        // Clé secrète Api Stripe
     Stripe::setApiKey($stripeSK);
     $session = Session::create([
        'line_items' => [[
          'price_data' => [
            'currency' => 'eur',
            'product_data' => [
              'name' => $article->getTitle(),
            ],
            'unit_amount' => 100,
          ],
          'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
      ]);
      // return $response->withHeader('Location', $session->url)->withStatus(303);
      return $this->redirect($session->url, 303);
    }

    /**
     * @Route("/success-url", name="success_url")
     */
    public function successUrl(Article $article, EntityManagerInterface $em): Response
    {
      $payment = new Payment;
      $payment->getArticle($article->getId());
      $em->persist($payment);
      $em->flush();
        return $this->render('payment/success.html.twig', []);
    }
     /**
     * @Route("/cancel-url", name="cancel_url")
     */
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }
}
