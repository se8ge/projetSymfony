<?php

namespace App\Controller;
use App\Repository\ArticleRepository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
public function index(ArticleRepository $articleRepository): Response
{
    // Récupère tous les articles (ou un filtrage si nécessaire)
    $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);

    return $this->render('blog/index.html.twig', [
        'articles' => $articles,
    ]);
}
}
