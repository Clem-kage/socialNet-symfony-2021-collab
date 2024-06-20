<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IntroController extends AbstractController
{
    /**
     * @Route("/hello-world", name="intro_hello")
     * @return Response - represents the HTTP response
     */
    public function hello(): Response
    {
        // Une route Symfony DOIT retourner une Réponse
        return new Response("<h1>Hello from Symfony !! </h1>");
    }

    /**
     * @Route("/goodbye", name="intro_good_bye")
     * @return Response
     */
    public function goodbye(): Response
    {
        // Souvent la réponse contient beacoup de HTML, dans ce cas, on préfère "rendre" un template Twig
        return $this->render('intro/goodbye.html.twig');
    }


    /**
     * @Route("/intro/go-to-infrep", name="intro_redirect_infrep")
     * @return RedirectResponse
     */
    public function redirectOnInfrepSite() {
        // Mais on peut aussi rediriger ailleurs
        return new RedirectResponse("https://www.infrep.org/");
    }

    // On peut configurer les routes avec les annotations (conseillé) ou via la config
    // Ici pas d'annotation Route
    public function routeWithConfig() {
        return new Response("Cette route est configurée grace au dossier <code>/config</code> ");
    }


    /**
     * @Route("/get", name="intro_get_query_params")
     * @return Response
     */
    public function getParams(Request $request) {

        $name = $request->query->get('name');
        // équivaut à faire
        // $name = $_GET['name'];

        // Mais avec Symfony on n'utilise jamais les variables superglobales
        // $_GET, $_POST, $_SESSION ...

        return new Response("Tu t'appelles $name");

    }

    /**
     * @Route("/params/{name}", name="intro_route_params")
     * @return Response
     */
    public function routeParams($name) {
        // Utiliser les paramètres de l'URL est en général plus élégant, et avec Symfony c'est très simple :
        // les paramètres fe l'URLs sont envoyés en paramètres de la méthode
        return new Response("Tu t'appelles $name");
    }
}