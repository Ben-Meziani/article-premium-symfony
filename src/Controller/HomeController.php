<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $articleRepository, PaymentRepository $paymentRepository): Response
    {
         //to see the 10 latest missions
         $articles = $articleRepository->findLatest(array(), array('id' => 'asc'));
         $payments = $paymentRepository->findAll();

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'payments' => $payments,
        ]);
    }
}
