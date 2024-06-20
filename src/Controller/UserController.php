<?php


namespace App\Controller;


use App\Entity\Post;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\PostRepository;
use App\Services\PictureGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/members", name="app_users_list")
     */
    public function list(): Response
    {
        // Récupérer la liste des auteurs depuis la base de données
        $repository = $this->getDoctrine()->getRepository(User::class);

        $authors = $repository->findAll();

        return $this->render('authors.html.twig', [
            'authorsList' => $authors
        ]);
    }

    /**
     * @Route("/members/{username}", name="app_user_profile")
     */
    public function userProfile(string $username, Request $request, SluggerInterface $slugger): Response
    {

        /** @var User $user */
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (empty($user)) {
            throw $this->createNotFoundException("Cet utilisateur ($username) n'existe pas.");
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->canSeePostsOf($user)) {
            /** @var PostRepository $postRepo */
            $postRepo = $this->getDoctrine()->getRepository(Post::class);
            $posts = $postRepo->findPrimariesFromAuthorWithCounts($user, $currentUser, 10);
        } else {
            $posts = null;
        }

        $editForm = null;
        if ($this->getUser() == $user) {
            $editForm = $this->createForm(UserType::class, $user);

            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {

                /** @var UploadedFile $picture */
                $picture = $editForm->get('picture')->getData();

                if ($picture) {
                    $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $picture->guessExtension();

                    try {
                        $picture->move(
                            $this->getParameter('profile_picture_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // TODO ... handle exception if something happens during file upload
                    }

                    $user->setProfilePicture($newFilename);
                }

                $this->getDoctrine()->getManager()->flush();
            }
        }


        return $this->render('user_profile.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'editForm' => empty($editForm) ? null : $editForm->createView()
        ]);
    }

    /**
     * @Route("/members/follow/{id}", name="app_relationship")
     * @param $id
     * @return Response
     */
    public function relationship($id): Response
    {
        // on récupère la personne associée à l'id
        /** @var User $person */
        $person = $this->getDoctrine()->getRepository(User::class)
            ->find($id);

        //vérifier si la personne existe bien sinon erreur
        if (empty($person)) {
            throw $this->createNotFoundException("cette personne $person n'existe pas");
        }
        //permet d'identifier l'utilisateur en cours
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // si la personne dans la liste n'est pas dans la liste des gens que l'on demande en ami
        if(!$currentUser->doesFollow($person)) {
            // on l'ajoute à la liste des amis
            $currentUser->follow($person);
        } else {
            // sinon on le retire
            $currentUser->unfollow($person);
        }
        //1.récupère le manager doctrine
        $manager = $this->getDoctrine()->getManager();

        //3. on dit au manager de mettre la BDD a jour
        $manager->flush();

        return $this->redirectToRoute('app_user_profile', [
            'username' => $person->getUsername()
        ]);
    }

    /**
     * @Route("/generate/{username}", name="app_generate")
     * @param $username
     * @param PictureGenerator $pictureGenerator
     * @return Response
     */
    public function generatePicture($username, PictureGenerator $pictureGenerator, Request $request): Response
    {

        $save = !!$request->query->get('save');

        $pictureGenerator->pictureGenerate($username, $save);

        $headers = array(
            'Content-type' => 'image/png',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-cache'
        );

        ob_start();
        $imageString = ob_get_clean();

        return new Response($imageString, 200, $headers);
    }
}