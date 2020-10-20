<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RequestValidator {

    private $validator;

    public function __construct(ValidatorInterface $validator,SerializerInterface $serializer){

        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function validateRequest($request, $className){
        $model = $this->serializer->deserialize($request->getContent(),$className, 'json');
        $errors = $this->validator->validate($model);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {

                $messages[] = [
                    'property' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }

            throw new \Exception(json_encode($messages));
        }

        return $model;
    }
}