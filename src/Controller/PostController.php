<?php

namespace App\Controller;

use App\Entity\Notif;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="app_post_list", methods={"GET"})
     */
    public function list(): Response
    {


        // Récupérer la liste des Posts en base de données

        // 1. Obtenir le Repository Doctrine qui nous intéresse
        $repo = $this->getDoctrine()->getRepository(Post::class);

        // 2. Utiliser le Repository pour accéder aux données hydratées
        $posts = $repo->findAll();

        /*
        // Ou bien plus simplement en une ligne :
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();
        */

        // Afficher cette liste, ("on injecte les données dans la vue")
        return $this->render('all-posts.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @example "/posts/page?number=2"
     *
     * @Route("/posts/page", name="app_post_list_pagination")
     * @return Response
     */
    public function pagination(Request $request) {

        /**
         * Le numéro de la page demandée
         */
        $pageNumber = $request->query->get('number');
        // Equivaut en PHP "pur" à :
        // $number = $_GET['number'];

        $perPage = 5;
        $offset = ($pageNumber - 1) * $perPage;

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(
                [],
                ['id' => 'DESC'],
                $perPage,
                $offset
        );


        return $this->render("post/post-list.html.twig", [
            'posts' => $posts
        ]);
    }


    /**
     * @Route("/posts/new", name="app_post_create")
     */
    public function create(Request $request) {

        // On instancie un nouveau Post
        $post = new Post();

        // On lui associe son auteur
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $post->setAuthor($currentUser);

        // construire le formulaire pour cette instance
        $form = $this->createForm(PostType::class, $post);

        // On fait analyser la requête par le Formulaire
        $form->handleRequest($request);

        // Si le formulaire a été soumis
        if ($form->isSubmitted()) {
            // Si les données du form sont valides
            if ($form->isValid()) {
                // On récupère les données
                $post = $form->getData();

                // On enregistre en BDD
                // 1. On récupère le Manager Doctrine
                $manager = $this->getDoctrine()->getManager();
                // 2. On dit au Manager qu'il doit "gérer" une nouvelle instance d'une Entité
                $manager->persist($post);
                // 3. On dit au Manager de mettre la base de données à jour
                $manager->flush();

                // Si on veut, on peut le rediriger ver sune autre page
                return $this->redirectToRoute('app_homepage');
            }
        }

        // afficher le formulaire dans la page
        return $this->render('new-post.html.twig', [
            // Avec les Form, on doit injecter uniquement la "vue" dans Twig,
            // pas le Form en entier
            'postForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/posts/{id}", name="app_post_read")
     */
    public function read($id, Request $request) {

        // Récupérer le Post depuis la base de données
        /** @var Post $post */
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        $comments = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy([
                'parent' => $post,
            ], [
                "createdAt" => "DESC"
            ]);

        // Vérifier que ce Post existe !!
        if (empty($post)) {
            throw $this->createNotFoundException("Le post #$id n'existe pas !");
        }



        // Créer un formulaire
        $comment = new Post();
        $form = $this->createForm(PostType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //si l'utilisateur connecté est ami avec l'auteur du post
            $currentUser = $this->getUser();
            /** @var User $currentUser */
           // $post->setAuthor(!$currentUser);
            $user = $post->getAuthor();


            /** @var User $user */
            if($user->isFollowedBy($currentUser)){
                /** @var Post $comment */
                $comment = $form->getData();
                $comment->setAuthor($this->getUser());
                $comment->setParent($post);

                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('app_post_read', ['id' => $id]);

            }else{
                $comments = null;
            }
        }

        // Afficher le post
        /** @var User $user */
        return $this->render("post-single.html.twig", [
            'post' => $post,
            'comments' => $comments,
            'commentForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/posts/{id}/update", name="app_post_update")
     */
    public function update($id, Request $request): Response
    {
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        // Si on n'a pas trouvé Post demandé
        if (empty($post)) {
            throw $this->createNotFoundException("Le Post #$id n'existe pas !");
        }

        // On créé un formulaire pour ce Post en particulier
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        // si on a recu des données du formulaire et qu'elles sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // on enregistre en BDD
            $this
                ->getDoctrine()
                ->getManager() // on recupère le manager de Doctrine
                ->flush(); // on met a jour la Base de données
        }

        return $this->render("update-post.html.twig", [
            'post' => $post,
            'updateForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/posts/{id}/like", name="app_post_like_toggle")
     */
    public function toggleLike($id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
//        if (empty($user)) {
//            throw $this->createAccessDeniedException("Vous ne passerez paaaaaaas !");
//        }

        /** @var Post $post */
        $post = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        if (empty($post)) {
            throw $this->createNotFoundException("le post #$id n'existe pas");
        }

        // On créé la relation
        if ($post->isLikedBy($user)) {
            $user->dislike($post);
        } else {
            $user->like($post);
        }

        // on l'enregistre (inutile de persister quoique ce soit, car Doctrine
        // connait déjà les deux entités $post et $user
        $this->getDoctrine()->getManager()->flush();


        // Solution 1 : c'est le serveur qui rend le nouveau HTML
        /*
        $this->getDoctrine()->getManager()->refresh($post);
        return $this->render("post/post-likes.html.twig", [
            'post' => $post
        ]);
        */


        // On redirige :
        // Si possible, on le renvoie à l'URL d'où il vient
        $refererUrl = $request->headers->get('referer');
        if (!empty($refererUrl)) {
            // TODO: améliorer la concaténation de l'ancre
            return $this->redirect($refererUrl . "#post-$id");
        }

        // Sinon, on le renvoie la homepage, dans le pire des cas
        return $this->redirectToRoute("app_homepage", [
            '_fragment' => "post-$id"
        ]);

    }

    /**
     * @Route ("/post/load", name="app_post_load")
     * @param Request $request
     * @return Response
     */
    public function loadPosts(Request $request): Response
    {
        //sleep(2);

        $authorId = $request->get('authorId');
        $number = $request->get('number');
        $from = $request->get('from');

        $repoPosts = $this->getDoctrine()->getRepository(Post::class);

        if(empty($authorId)){
            $posts = $repoPosts->findPrimariesWithCounts($this->getUser(),$number,$from);
        } else {
            $repoUsers = $this->getDoctrine()->getRepository(User::class);
            $author = $repoUsers->find($authorId);

            $posts=[];

            if(!empty($author)){
                $posts = $posts = $repoPosts->findPrimariesFromAuthorWithCounts($author,$this->getUser(),$number,$from);
            }
        }

        $responseText = "";
        foreach ($posts as $key=>$post)
        {
            $responseText = $responseText . $this->renderView("post/post.html.twig",['post'=>$post]);
            if($key< count($posts)-1)
            {
                $responseText = $responseText . "///";
            }
        }

        return new Response($responseText);


    }


}
