<?php

namespace App\Controller;


use App\Security\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginController extends AbstractController
{
    public function index(JWT $jwt, UserInterface $user = null)
    {
        return new Response($jwt->getToken($user->getUsername()));
    }

}

