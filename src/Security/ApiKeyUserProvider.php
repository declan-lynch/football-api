<?php

namespace App\Security;

use Lcobucci\JWT\Token;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyUserProvider implements UserProviderInterface
{

    private $jwt;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    public function getUsernameForApiKey($apiKey)
    {
        try {
            $token = $this->jwt->verifyToken($apiKey);
            if($token instanceof Token){
                return $token->getClaim('username');
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    public function loadUserByUsername($username)
    {
        return new User($username, null, array('ROLE_API'));
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}