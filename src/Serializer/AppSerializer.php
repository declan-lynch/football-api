<?php

namespace App\Serializer;

use App\Entity\BaseEntity;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AppSerializer
{
    public $serializer;

    public function __construct()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $normalizer->setCircularReferenceHandler(function ($object) {return $object->getId();});
        $normalizers = array($normalizer);
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    private function getGroupsArray($groups=[])
    {
        $groups = count($groups) ? $groups : null ;
        return ['groups' => $groups];
    }

    public function toJson(BaseEntity $object, Array $groups=[])
    {
        return $this->serializer->serialize($object, 'json', $this->getGroupsArray($groups));
    }

    public function resultsToJson($array, $groups=[])
    {
        $data = [
            'count' => count($array),
            'results' => $array
        ];
        return $this->serializer->serialize($data, 'json', $this->getGroupsArray($groups));
    }

    public function fromJson($class, $json_string)
    {
        return $this->serializer->deserialize($json_string, $class, 'json');
    }
}