<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Payment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SubscriptionController extends AbstractController
{

  private $privateKey;

  public function __construct()
  {
    if ($_ENV['APP_ENV']  === 'dev') {
      $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
    } else {
      $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
    }
  }

  /**
   * @Route("/article/{id}/payment", name="payment")
   */
  public function index(Article $article): Response
  {
    return $this->render('payment/index.html.twig', [
      'title' => $article->getTitle(),
      'article' => $article
    ]);
  }

  /**
   * @Route("/subscribe/checkout", name="checkoutSubscription")
   */
  public function checkout(): Response
  {
    if (!$this->getUser()) {
      return $this->redirectToRoute('app_login');
  }
    $privateKeys = $this->privateKey;
    // Clé secrète Api Stripe
    Stripe::setApiKey($privateKeys);
    $session = Session::create([
      'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
      'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
      'mode' => 'subscription',
      'line_items' => [[
        'price' => 'price_1K7nLgAby9BSkBRiHl0FjwvP',
        // For metered billing, do not pass quantity
        'quantity' => 1,
      ]],
    ]);
    // return $response->withHeader('Location', $session->url)->withStatus(303);
    return $this->redirect($session->url, 303);
  }

  /**
   * @Route("/success-url", name="success_url")
   */
  public function successUrl(EntityManagerInterface $em): Response
  {
    //Une fois l'abonnement fait, on met à jour le role et on enregistre l'abonnement de l'utilisateur
    $user = $this->getUser();
    $user->setSubscription(1);
    //ROLE_SUBSCRIBER permet d'accéder à tout les article du site
    $user->setRoles(["ROLE_SUBSCRIBER"]);
    $em->persist($user);
    $em->flush();
    return $this->render('subscription/success.html.twig', []);
  }
  /**
   * @Route("/cancel-url", name="cancel_url")
   */
  public function cancelUrl(): Response
  {
    return $this->render('subscription/cancel.html.twig', []);
  }

      /**
     * @Route("/subscriber/article/{id}", name="subscriber.article.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Article $article): Response
    { 

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'current_menu' => 'articles',
            ]);
    }
}
