<?php namespace Osds\Api\Domain\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends Exception implements ApiExceptionInterface
{
    public function __construct(
        array $message = [],
        $code = Response::HTTP_BAD_REQUEST,
        Exception $previous = null
    ) {
        parent::__construct(json_encode($message), $code, $previous);
    }
}
