<?php

namespace Osds\Api\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class ErrorException extends BaseException
{

    public function setMessage($message, $error)
    {
        $logMessage = '[' . $error->getFile() . '::' . $error->getLine() . '] - ' . $error->getMessage();

        $this->logger->error($logMessage, [$error->getFile(), $error->getLine()]);
        parent::setMessageAndCode($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}


