<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Illuminate\Http\Request;

use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/api/status")
 */
class CheckStatusController extends BaseUIController
{

    protected $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
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
     * @SWG\Tag(name="Common")
     * @Security(name="Bearer")
     */

    public function handle()
    {
        return $this->generateResponse("Staying alive");
    }
}
