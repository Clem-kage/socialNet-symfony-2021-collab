<?php

namespace App\Controller;

use App\Entity\Notif;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotifController extends AbstractController
{
    /**
     * @Route("/deletenotif/{id}", name="app_deletenotif")
     */
    public function index(Notif $notif): Response
    {

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($notif);
        $manager->flush();
        return $this->redirectToRoute("app_homepage");

    }
}
