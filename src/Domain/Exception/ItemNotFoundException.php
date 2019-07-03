<?php

namespace Osds\Api\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class ItemNotFoundException extends BaseException
{
    public function setMessage($entity, $uuid)
    {
        $message = 'No item found for this search criteria';
        $this->logger->info($message);
        parent::setMessageAndCode($message, Response::HTTP_NOT_FOUND);
    }
}
