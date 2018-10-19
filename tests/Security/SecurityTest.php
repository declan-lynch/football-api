<?php

namespace App\Tests\Security;

use App\Security\JWT;
use App\Entity\Team;
use Lcobucci\JWT\Token;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{

    private $params;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->params = $container->get('parameter_bag');
    }

    private function jwt()
    {
        return new JWT($this->params);
    }

    public function testLoads()
    {
        $jwt = $this->jwt();
        $this->assertInstanceOf(JWT::class, $jwt);
    }

    public function test_returns_string()
    {
        $jwt = $this->jwt();
        $username = 'new user';
        $token = $jwt->getToken($username);
        $this->assertGreaterThan(10, strlen($token));
    }

    public function test_returned_token_verifies()
    {
        $jwt = $this->jwt();
        $username = 'new user';
        $token = $jwt->getToken($username);
        $untampered_string = $token.'';
        $recovered_token = $jwt->verifyToken($untampered_string);
        $this->assertInstanceOf(Token::class, $recovered_token);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_tampered_token_does_not_verify()
    {
        $jwt = $this->jwt();
        $username = 'new user';
        $token = $jwt->getToken($username);
        $tampered_string = $token.'_this_is_not_the_same';
        $recovered_token = $jwt->verifyToken($tampered_string);
    }

    public function test_recovered_token_contains_username()
    {
        $jwt = $this->jwt();
        $username = 'new user';
        $token = $jwt->getToken($username);
        $untampered_string = $token.'';
        $recovered_token = $jwt->verifyToken($untampered_string);
        $this->assertEquals($username, $recovered_token->getClaim('username'));
    }

}