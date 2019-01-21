<?php namespace Osds\Api\Domain\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception implements ApiExceptionInterface
{
    public function __construct($message = null)
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND);
    }
}
