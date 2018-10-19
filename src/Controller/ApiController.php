<?php

namespace App\Controller;

use App\Entity\League;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{

    private $controller_service;

    public function __construct(ControllerService $c_service)
    {
        $this->controller_service = $c_service;
    }

    public function index()
    {
        return new Response('this is where the api lives');
    }

    public function getLeague($id)
    {
        return $this->controller_service->findEntityJsonResponse(League::class, $id);
    }

    public function getTeam($id)
    {
        return $this->controller_service->findEntityJsonResponse(Team::class, $id);
    }

    public function createTeam(Request $request)
    {
        return $this->controller_service->createEntityJsonResponse(Team::class, $request);
    }

    public function createLeague(Request $request)
    {
        return $this->controller_service->createEntityJsonResponse(League::class, $request);
    }

    public function updateTeam($id, Request $request)
    {
        return $this->controller_service->updateEntityJsonResponse(Team::class, $id, $request);
    }

    public function deleteLeague($id)
    {
        return $this->controller_service->removeEntityBlankResponse(League::class, $id);
    }







}

