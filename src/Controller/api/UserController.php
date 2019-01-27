<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", methods={"POST"}, name="api_users_registration")
     */
    public function registrationAction()
    {
        return null;
    }

    /**
     * @Route("/api/users", methods={"GET"}, name="api_users_list")
     */
    public function listAction()
    {
        return null;
    }
}
