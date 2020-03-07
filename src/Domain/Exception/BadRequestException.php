<?php

namespace Osds\Api\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends BaseException
{

    public function setMessage($message, $error)
    {
        $this->logger->error($message, [$error->getFile(), $error->getLine()]);
        parent::setMessageAndCode($message, Response::HTTP_BAD_REQUEST);
    }
}
