<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]  // Sécuriser l'accès à cette page pour les admins seulement
   public function dashboard(ArticleRepository $articleRepository): Response
{
    // Récupérer tous les articles (ou en fonction de ce que tu souhaites afficher)
    $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);

    // Passer la variable $articles à la vue
    return $this->render('admin/dashboard.html.twig', [
        'articles' => $articles,  // On passe la variable articles ici
    ]);
}

    #[Route('/admin/articles', name: 'app_admin_articles')]
    #[IsGranted('ROLE_ADMIN')]  // Sécuriser l'accès à cette page pour les admins seulement
    public function index(ArticleRepository $articleRepository): Response
    {
        // Récupérer tous les articles
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/admin/new', name: 'app_admin_new')]
    #[IsGranted('ROLE_ADMIN')]  // Sécuriser l'accès à cette page pour les admins seulement
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer un nouvel article
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_admin_edit')]
    #[IsGranted('ROLE_ADMIN')]  // Sécuriser l'accès à cette page pour les admins seulement
    public function edit(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Modifier un article existant
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/admin/articles/delete', name: 'app_article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]  // Sécuriser l'accès à cette page pour les admins seulement
    public function delete(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Supprimer un article
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_articles');
    }
}
