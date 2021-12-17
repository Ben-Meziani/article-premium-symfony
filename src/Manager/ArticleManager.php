<?php

namespace App\Manager;

use App\Entity\Payment;
use App\Entity\Article;
use App\Entity\User;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ArticleManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var StripeService
     */
    protected $stripeService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StripeService $stripeService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StripeService $stripeService
    ) {
        $this->em = $entityManager;
        $this->stripeService = $stripeService;
    }

    public function getArticles()
    {
        return $this->em->getRepository(Article::class)
            ->findAll();
    }

    public function intentSecret(Article $article)
    {
        $intent = $this->stripeService->paymentIntent($article);

        return $intent['client_secret'] ?? null;
    }

    /**
     * @param array $stripeParameter
     * @param Article $article
     * @return array|null
     */
    public function stripe(array $stripeParameter, Article $article)
    {
        $resource = null;
        $data = $this->stripeService->stripe($stripeParameter, $article);

        if($data) {
            $resource = [
                'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
                'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
                'stripeId' => $data['charges']['data'][0]['id'],
                'stripeStatus' => $data['charges']['data'][0]['status'],
                'stripeToken' => $data['client_secret']
            ];
        }

        return $resource;
    }

    /**
     * @param array $resource
     * @param Article $article
     * @param User $user
     */
    public function create_subscription(array $resource, Article $article, User $user)
    {
        $payment = new Payment();
        $payment->setUser($user);
        $payment->setArticle($article);
        //$payment->setprice($article->getPrice());
        //$payment->setReference(uniqid('', false));
        $payment->setBrandStripe($resource['stripeBrand']);
        $payment->setLast4Stripe($resource['stripeLast4']);
        $payment->setIdChargeStripe($resource['stripeId']);
        $payment->setStripeToken($resource['stripeToken']);
        $payment->setStatusStripe($resource['stripeStatus']);
        $payment->setUpdatedAt(new \Datetime());
        $payment->setCreatedAt(new \Datetime());
        $this->em->persist($payment);
        $this->em->flush();
    }
}