<?php

namespace App\Controller;

use App\Entity\Mobile;
use App\Repository\MobileRepository;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security as nSecurity;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpClient\HttpClient;

class MobileController extends AbstractController
{
    private $serializer;
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * @Route("/mobiles", name="api_get_all_mobiles", methods = {"GET"})
     * 
     * @return JsonResponse
     */
    public function getAllMobiles(MobileRepository $mobileRepository)
    {
        
        $mobiles = $this->serializer->serialize($mobileRepository->findAll(), 'json');


        return new JsonResponse(
            $mobiles,
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
    /** 
     * @Route("/mobile/{id}", name= "api_get_one_mobile", methods = {"GET"})
     * @return JsonResponse
     */
    public function getOneMobile(Mobile $mobile, MobileRepository $mobileRepository)
    {
        return new JsonResponse(
            $this->serializer->serialize($mobile, 'json'),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
}
