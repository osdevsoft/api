<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/")
 */
class HomeController extends BaseUIController
{

    public function __construct()
    {
    }

    /**
     *
     * @Route(
     *     "/",
     *     methods={"GET"}
     * )
     * @Route(
     *     "/api",
     *     methods={"GET"}
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Welcome page",
     *     )
     * )
     *
     * @SWG\Tag(name="Common")
     * @Security(name="Bearer")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle()
    {
        return $this->generateResponse("Welcome");
    }
}
