<?php

namespace App\Security;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Ramsey\Uuid\Uuid;

class JWT
{
    private $params;
    private $signer;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
        $this->signer = new Sha256();
    }

    private function host_from_params()
    {
        $bits = [];
        $bits[] = $this->params->get('router.request_context.scheme');
        $bits[] = '://';
        $bits[] = $this->params->get('router.request_context.host');
        return implode('', $bits);
    }

    public function getToken($username = 'anon')
    {
        $token = new Builder();
        $token->setIssuer($this->host_from_params());
        $token->setId(Uuid::uuid4(), true);
        $token->setIssuedAt(time());
        $token->setExpiration(time() + 3600);
        $token->set('username', $username);
        $token->sign($this->signer, getenv('APP_SECRET'));
        return $token->getToken();
    }

    public function verifyToken($json_str)
    {
        $errors = ['the token could not be verified'];
        try {
            $token = (new Parser())->parse((string)$json_str);
            if ($token->verify($this->signer, getenv('APP_SECRET'))) {
                return $token;
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        throw new \InvalidArgumentException(implode(' : ', $errors));
    }
}