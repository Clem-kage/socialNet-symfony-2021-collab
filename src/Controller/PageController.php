<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\PostType;
use App\Form\SearchType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PageController extends AbstractController
{

    /**
     * @Route("/", name="app_homepage")
     * @param AuthenticationUtils $authUtils
     * @return Response - represents the HTTP response
     */
    public function homepage(AuthenticationUtils $authUtils): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        /**
         * @var PostRepository $repo
         */
        $repo = $this->getDoctrine()->getRepository(Post::class); // On documente le type du Repo pour aider PHPStorm
        $allPosts = $repo->findPrimariesWithCounts($currentUser, 10);

        if ($currentUser) {
            $form = $this->createForm(PostType::class, new Post(), [
                'action' => $this->generateUrl('app_post_create')
            ]);
        } else {
            $form = $this->createForm(LoginFormType::class, null, [
                'action' => $this->generateUrl('app_login')
            ]);
        }

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        // On injecte les variables dans le template, en deuxième paramètre de la méthode render()
        return $this->render("homepage.html.twig", [
            // now de la variable twig => nom de la variable PHP
            "popularList" => $allPosts,
            "form" => $form->createView(),
            "error" => $error,
            "last_username" => $lastUsername
        ]);
    }



    /**
     * @Route("/search", name="app_search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $repoPost = $this->getDoctrine()->getRepository(Post::class);
        $repoUser = $this->getDoctrine()->getRepository(User::class);

        $allPosts = null;
        $allUsers = null;
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            // récupère le mot donné dans la barre de recherche
            $word = $form->get('word')->getData();

            // requêtes SQL
            $allPosts = $repoPost->findbyPosts($word);
            $allUsers = $repoUser->findbyUsers($word);
        }

        return $this->render('search.html.twig', [
            'results' => $allUsers,
            'resultPosts' => $allPosts,
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/legals", name="app_legals_mentions")
     */
    public function legals(): Response
    {
        return $this->render('legals.html.twig');
    }

    /**
     * @Route("/about-us", name="app_about")
     * @return Response
     */
    public function about() {
        return $this->render("about.html.twig");
    }

    public function searchBar() {
        $form = $this->createForm(SearchType::class, null, [
            'action' => $this->generateUrl('app_search')
        ]);

        return $this->render('_commons/searchbar.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

}
