<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/travgo/home", name="default")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    /**
     * @Route("/", name="def")
     */
    public function indeh()
    {
        return $this->render('default/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/agenda", name="agenda")
     */
    public function agenda()
    {
        return $this->render('default/agenda.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

}
