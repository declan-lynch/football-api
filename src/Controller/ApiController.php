<?php

namespace App\Controller;

use App\Entity\League;
use App\Entity\Team;
use App\Serializer\AppSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{

    private $serializer;
    private $serial_group;
    private $json_request;

    public function __construct(AppSerializer $appSerializer, JsonRequest $json_request)
    {
        $this->serializer = $appSerializer;
        $this->serial_group = ['default'];
        $this->json_request = $json_request;
    }

    public function index()
    {
        return new Response('this is where the api lives');
    }

    public function getLeague($id)
    {
        return $this->findEntityJsonResponse(League::class, $id);
    }

    public function getTeam($id)
    {
        return $this->findEntityJsonResponse(Team::class, $id);
    }

    public function createTeam(Request $request)
    {
        return $this->createEntityJsonResponse(Team::class, $request);
    }

    public function createLeague(Request $request)
    {
        return $this->createEntityJsonResponse(League::class, $request);
    }

    private function createEntityJsonResponse($class, $request)
    {
        try {
            $valid_json = $this->json_request->getJson($request);
            if ($team = $this->serializer->fromJson($class, $valid_json))
            {
                try{
                    return $this->saveEntityJsonRepsonse($team);
                } catch (\Exception $e) {
                    $error = 'the data supplied could not be processed';
                }
            }
        } catch (\Exception $e) {
            $error = 'no data received : '.$e->getMessage();
        }
        return new Response($error, 404);
    }

    public function updateTeam($id, Request $request)
    {
        try {
            $valid_json = $this->json_request->getJson($request);
            $entity = $this->findByClass(Team::class, $id);
            if (is_object($entity))
            {
                try{
                    //check json does not contain any spurious data
                    if($this->serializer->fromJson(get_class($entity), $valid_json)) {
                        return $this->mergeJsonEntity($entity, $valid_json);
                    }
                } catch (\Exception $e) {
                    $error = 'the data supplied could not be processed';
                }
            }
            $error = 'the request could not be processed';
        } catch (\Exception $e) {
            $error = 'no data received';
        }
        return new Response($error, 404);
    }

    public function deleteLeague($id)
    {
        return $this->removeEntityBlankResponse(League::class, $id);
    }

    private function mergeJsonEntity($entity, $json_str)
    {
        if($entity = $this->json_request->mergeJsonEntity($entity, $json_str))
        {
            return $this->saveEntityJsonRepsonse($entity);
        } else {
            throw new \Exception('oh dear...');
        }
    }

    private function saveEntityJsonRepsonse($entity)
    {
        $this->getDoctrine()->getManager()->persist($entity);
        $this->getDoctrine()->getManager()->flush();
        return $this->entityJsonResponse($entity);
    }

    private function findEntityJsonResponse($class, $id)
    {
        if('all' == $id){
            return new Response($this->serializer->resultsToJson($this->findAllbyClass($class), $this->serial_group));
        } else {
            $entity = $this->findByClass($class, $id);
            if (!$entity) {
                throw $this->createNotFoundException('the item you were looking for cannot be found');
            }
            return $this->entityJsonResponse($entity);
        }
    }

    private function removeEntityBlankResponse($class, $id)
    {
        try {
            $entity = $this->findByClass($class, $id);
            if (!$entity) {
                throw $this->createNotFoundException('the item you were looking for cannot be found');
            }
            $this->getDoctrine()->getManager()->remove($entity);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 404);
        }
        return new Response('');
    }

    private function entityJsonResponse($object)
    {
        return new Response($this->serializer->toJson($object, $this->serial_group));
    }

    private function findByClass($class, $id)
    {
        return $class && $id ? $this->getDoctrine()->getRepository($class)->find($id) : false ;
    }

    private function findAllbyClass($class)
    {
        return $this->getDoctrine()->getRepository($class)->findAll();
    }

}

