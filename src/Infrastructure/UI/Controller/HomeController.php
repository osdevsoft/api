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

    protected $request;

    public function __construct()
    {
    }

    /**
     *
     * @Route(
     *     "",
     *     methods={"GET"}
     * )
     * @Route(
     *     "/api",
     *     methods={"GET"}
     * )
     *
     * Check API status
     *
     * @SWG\Tag(name="Common")
     * @Security(name="Bearer")
     */

    public function handle()
    {
        return $this->generateResponse("Welcome");
    }
}
