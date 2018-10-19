<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerTest extends WebTestCase
{
    private $client;
    private $logindetails;
    private $content_type;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->logindetails = [
            'PHP_AUTH_USER' => 'commercialpeople',
            'PHP_AUTH_PW' => 'kerching'
        ];
        $this->content_type = ['CONTENT_TYPE' => 'application/json'];
    }

    private function getStatusCode()
    {
        return $this->client->getResponse()->getStatusCode();
    }

    public function test_default_route()
    {
        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_no_key_error()
    {
        $this->client->request('GET', '/api');
        $this->assertEquals(401, $this->getStatusCode());
    }

    public function test_login_basic_error()
    {
        $this->client->request('GET', '/login');
        $this->assertEquals(401, $this->getStatusCode());
    }

    private function loginAsUser()
    {
        $this->client->setServerParameters($this->logindetails);
        return $this->client->request('GET', '/login');
    }

    private function getResponseText($crawler)
    {
        return $crawler->filter('body > p')->text();
    }

    private function fetchJWT()
    {
        return $this->getResponseText($this->loginAsUser());
    }

    public function test_login_logout_with_details()
    {
        $this->loginAsUser();

        $this->assertEquals(200, $this->getStatusCode());

        $this->client->setServerParameters([]);
        $this->client->request('GET', '/logout');

        $this->client->request('GET', '/login');
        $this->assertEquals(401, $this->getStatusCode());
    }

    public function test_login_returns_jwt()
    {
        $jwt1 = $this->fetchJWT();
        $jwt2 = $this->fetchJWT();

        $this->assertNotEquals($jwt1, $jwt2);
    }

    public function test_api_team_post_url()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('POST', '/api/team?key='.$jwt, [], [], ['CONTENT_TYPE' => 'application/json']);
        $this->assertEquals(404, $this->getStatusCode());
    }

    public function test_api_team_post_url_invalid_key()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('POST', '/api/team?key='.$jwt.'_now_invalid');
        $this->assertEquals(401, $this->getStatusCode());
    }

    public function test_api_league_post_url()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('POST', '/api/league?key='.$jwt, [], [], ['CONTENT_TYPE' => 'application/json']);
        $this->assertEquals(404, $this->getStatusCode());
    }

    public function test_api_league_post_valid_data()
    {
        $jwt = $this->fetchJWT();
        $response = $this->client->request(
            'POST',
            '/api/league?key='.$jwt,
            [],
            [],
            $this->content_type,
            '{"name":"league of leagues"}'
        );
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_team_post_valid_data()
    {
        $jwt = $this->fetchJWT();
        $response = $this->client->request(
            'POST',
            '/api/team?key='.$jwt,
            [],
            [],
            $this->content_type,
            '{"name":"post united"}'
        );
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_team_post_valid_data_with_extra_fields_errors()
    {
        $jwt = $this->fetchJWT();
        $response = $this->client->request(
            'POST',
            '/api/team?key='.$jwt,
            [],
            [],
            $this->content_type,
            '{"name":"post united", "newvar": "new value"}'
        );
        $this->assertEquals(404, $this->getStatusCode());
    }

    public function test_api_team_put_url()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('PUT', '/api/team?key='.$jwt);
        $this->assertEquals(404, $this->getStatusCode());
    }

    public function test_api_team_put_url_valid_data()
    {
        $jwt = $this->fetchJWT();
        $response = $this->client->request(
            'PUT',
            '/api/team/1?key='.$jwt,
            [],
            [],
            $this->content_type,
            '{"name" : "puttingham city"}'
        );
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_team_put_url_extra_data()
    {
        $jwt = $this->fetchJWT();
        $response = $this->client->request(
            'PUT',
            '/api/team/1?key='.$jwt,
            [],
            [],
            $this->content_type,
            '{"name" : "puttingham city2", "leagueId": 1}'
        );
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_league_delete_url()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('DELETE', '/api/league?key='.$jwt);
        $this->assertEquals(404, $this->getStatusCode());
    }

    public function test_api_league_post_valid_data_then_delete()
    {
        $jwt = $this->fetchJWT();
        $response = $this->client->request(
            'POST',
            '/api/league?key='.$jwt,
            [],
            [],
            $this->content_type,
            '{"name":"short lived league"}'
        );
        $json_str = $this->getResponseText($response);
        $json_obj = json_decode($json_str);
        $this->client->request('DELETE', '/api/league/'.$json_obj->id.'?key='.$jwt);
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_call_with_key_in_url()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('GET', '/api/team/1?key='.$jwt);
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_call_with_key_in_header()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('GET', '/api/team/1', [], [], ['HTTP_AUTHORISE' => $jwt]);
        $this->assertEquals(200, $this->getStatusCode());
    }

    public function test_api_call_with_invalidkey_in_url()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('GET', '/api/team/1?key='.$jwt.'_now_invalid');
        $this->assertEquals(401, $this->getStatusCode());
    }

    public function test_api_call_with_invalid_key_in_header()
    {
        $jwt = $this->fetchJWT();
        $this->client->request('GET', '/api/team/1', [], [], ['HTTP_AUTHORISE' => $jwt.'_now_invalid']);
        $this->assertEquals(401, $this->getStatusCode());
    }

}