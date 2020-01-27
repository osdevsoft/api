<?php

namespace Osds\Api\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class EntityNotFoundException extends BaseException
{

    public function setMessage($message, $error)
    {
        $this->logger->error($error->getMessage(), [$error->getFile(), $error->getLine()]);
        parent::setMessageAndCode($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
