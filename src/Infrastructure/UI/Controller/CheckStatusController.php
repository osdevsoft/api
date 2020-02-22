<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/status")
 */
class CheckStatusController extends BaseUIController
{

    public function __construct() {
    }

    /**
     *
     * @Route(
     *     "/",
     *     methods={"GET"}
     * )
     *
     * Check API status
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the status of the API",
     *     )
     * )
     * @SWG\Tag(name="Common")
     * @Security(name="Bearer")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */

    public function handle()
    {
        return $this->generateResponse("Staying alive");
    }
}
