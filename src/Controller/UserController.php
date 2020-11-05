<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Form\UserType;
use Swagger\Annotations as SWG;
use Symfony\Component\Form\Form;
use App\Service\RequestValidator;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
use Nelmio\ApiDocBundle\Annotation\Security as nSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $serializer;
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * Get all users linked to a client
     * @Route("/users", name="api_get_all_users" , methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found",
     * )
     * @SWG\Tag(name="User")
     * @nSecurity(name="Bearer")
     * 
     * @return JsonResponse
     */
    public function getAllUsers(UserRepository $userRepository, ClientRepository $clientRepository, Request $request)
    {
        $client = $clientRepository->find($this->getUser());
        $page = $request->query->get('page',1);
        $limit = $request->query->get('limit', 5);
        $users = $userRepository->findAllFromClient($client);
        $offset = ($page - 1) * $limit;
        $numberOfPages = (int) ceil(count($users) / $limit);
        $collection = new CollectionRepresentation(
            array_slice($users, $offset, $limit));
        $paginated = new PaginatedRepresentation(
            $collection,
            'api_get_all_users',
            [],
            $page,
            $limit,
            $numberOfPages
        );
        return new JsonResponse(
            $this->serializer->serialize($paginated, 'json'),
            200,
            [],
            true
        );
    }
    /**
     * Get one specified user linked to a client
     * @Route("/user/{id}", name="api_get_one_user", methods={"GET"})
     * @IsGranted("USER_SHOW", subject="user")
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     *  @SWG\Response(
     *     response=401,
     *     description="JWT Token not found",
     * )
     * @SWG\Parameter(
     *    name="id",
     *    in="path",
     *    type="integer",
     *    description ="ID of the user",
     *    required=true
     * )
     * @SWG\Tag(name="User")
     * @nSecurity(name="Bearer")
     * 
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
     *     description="JWT Token not found",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="NOT FOUND"
     * )
     * @nSecurity(name="Bearer")
     * @SWG\Tag(name="User")
     * @return JsonResponse
     */
    public function createUser(Request $request, RequestValidator $validator)
    {
        try{            
        $user = $validator->validateRequest($request, User::class);
        }catch(\Exception $e){            
            return new JsonResponse(
                ['errors' => json_decode($e->getMessage())],
                Response::HTTP_BAD_REQUEST,
            );
        }

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
     *     description="JWT Token not found",
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
     * @nSecurity(name="Bearer")
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
