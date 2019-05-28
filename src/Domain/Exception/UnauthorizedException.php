<?php

namespace Osds\Api\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends BaseException
{

    public function setMessage($email, $error)
    {
        $logMessage = 'LoginUser :: password missmatch for ' . $email;
        $this->logger->error($logMessage, [$error->getFile(), $error->getLine()]);

        $message = "User not authorized";
        parent::setMessageAndCode($message, Response::HTTP_UNAUTHORIZED);
    }

}


