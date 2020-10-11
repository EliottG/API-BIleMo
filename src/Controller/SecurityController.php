<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController 
{
    /**
     * Login to get an authenticate token
     * @Route("/login_check", name="security", methods={"POST"})
     *  @SWG\Parameter(
     *   name="Logs",
     *   description="Fields to provide to sign in and get a token",
     *   in="body",
     *   required=true,
     *   type="string",
     *   @SWG\Schema(
     *     type="object",
     *     title="Login field",
     *     @SWG\Property(property="username", type="string", default="Client1"),
     *     @SWG\Property(property="password", type="string", default="password")
     *  
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="OK",
     *     @SWG\Schema(
     *      type="string",
     *      title="Token",
     *      @SWG\Property(property="token", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request - Invalid JSON",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Bad credentials",
     * )
     * @SWG\Tag(name="Authentication")
     * 
     * @return JsonResponse
     */
    public function login_check()
    {
    }
}
