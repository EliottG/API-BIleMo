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
     * Get all mobiles
     * @Route("/mobiles", name="api_get_all_mobiles", methods = {"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Mobile::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found",
     * )
     * @SWG\Tag(name="Mobile")
     * @nSecurity(name="Bearer")
     * 
     * @return JsonResponse
     */
    public function getAllMobiles(MobileRepository $mobileRepository, Request $request)
    {
        
        $mobiles = $this->serializer->serialize($mobileRepository->findAll(), 'json');

        
        $response = new JsonResponse(
            $mobiles,
            200,
            [],
            true
        );
        //dd($response);
       // $response->setEtag(md5($mobiles));
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setVary(['authorization']);
        // $response->isNotModified($request);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
    /** 
     * Get one specified mobile
     * @Route("/mobile/{id}", name= "api_get_one_mobile", methods = {"GET"})
     *  * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Mobile::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="NOT FOUND",
     * )
     * @SWG\Parameter(
     *    name="id",
     *    in="path",
     *    type="integer",
     *    description ="ID of the mobile",
     *    required=true
     * )
     * @SWG\Tag(name="Mobile")
     * @nSecurity(name="Bearer")
     * @return JsonResponse
     */
    public function getOneMobile(Mobile $mobile, Request $request)
    {
        $response =  new JsonResponse(
            $this->serializer->serialize($mobile, 'json'),
            JsonResponse::HTTP_OK,
            [],
            true
        );
        $response->setEtag(md5($response->getContent()));
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->setVary(['authorization']);
        $response->isNotModified($request);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
