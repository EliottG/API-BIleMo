<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Form\UserType;
use Symfony\Component\Form\Form;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security as nSecurity;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Validator\Constraints\Form as ConstraintsForm;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private $serializer;
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * @Route("/users", name="api_get_all_users" , methods={"GET"})
     * @return JsonResponse
     */
    public function getAllUsers(UserRepository $userRepository, ClientRepository $clientRepository)
    {
        $client = $clientRepository->find($this->getUser());   
        $users = $this->serializer->serialize($userRepository->findAllFromClient($client), 'json');
        return new JsonResponse(
            $users,
            200,
            [],
            true
        );
    }
    /**
     * @Route("/user/{id}", name="api_get_one_user", methods={"GET"})
     * @return JsonResponse
     */
    public function getOneUser(User $user)
    {
        if ($user->getClient() !== $this->getUser()) {
            throw $this->createNotFoundException();
        }
        $user = $this->serializer->serialize($user, 'json');
        return new JsonResponse(
            $user,
            200,
            [],
            true
        );
    }
    /**
     * Create a user
     * @Route("/user", name="api_create_user", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="CREATED",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT Token not found",
     * )
     * @nSecurity(name="Bearer")
     * @SWG\Tag(name="User")
     * @return JsonResponse
     */
    public function createUser(Request $request)
    {
        
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setClient($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $user = $this->serializer->serialize($user, 'json');
        return new JsonResponse(
            $user,
            Response::HTTP_CREATED,
            [],
            true
        );
    }
    
    /**
     * Delete a user
     * @Route("/user/{id}", name="api_delete_user", methods={"DELETE"}) 
     *  @SWG\Response(
     *     response=204,
     *     description="NO CONTENT",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT Token not found",
     * )
     *      @SWG\Response(
     *     response=404,
     *     description="NOT FOUND"
     * )
     * @SWG\Parameter(
     *    name="id",
     *    in="path",
     *    type="integer",
     *    description ="ID of the user",
     *    required=true
     * )
     * @SWG\Tag(name="User")
     * @IsGranted("USER_DELETE", subject="user")
     */
    public function deleteUser(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}
