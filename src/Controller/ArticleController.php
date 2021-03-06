<?php

namespace App\Controller;

use App\Data\ArticleData;
use App\Entity\Article;
use App\Entity\Payment;
use App\Repository\ArticleRepository;
use App\Repository\PaymentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
     /**
     * @Route("/articles", name="article.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaymentRepository $paymentRepository ,ArticleRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {   
        $data = $repository->findAll();
        $payments = $paymentRepository->findAll();
        $articles = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1), 
            6
        );        

        return $this->render('Article/index.html.twig', [
            'articles' => $articles,
            'payments' => $payments,
        ]);
    }

      /**
     * @Route("/article/{id}", name="article.show", requirements={"slug": "[a-z0-9\-]*"})
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
