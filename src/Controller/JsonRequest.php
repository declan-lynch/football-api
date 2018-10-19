<?php

namespace App\Controller;

use Symfony\Component\PropertyAccess\PropertyAccess;

class JsonRequest {

    public function getJson($request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $json_obj = json_decode($request->getContent(), true);
            return json_encode($json_obj);
        }
        throw new \InvalidArgumentException('the data was not in json format - check the content-type setting');
    }

    public function mergeJsonEntity($entity, $json_str)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $request_jsonarr = json_decode($json_str);
        foreach (array_keys((array)$request_jsonarr) as $tmpkey) {
            if ($accessor->isWritable($entity, $tmpkey)) {
                $accessor->setValue($entity, $tmpkey, $request_jsonarr->{$tmpkey});
            }
        }
        return $entity;
    }
}