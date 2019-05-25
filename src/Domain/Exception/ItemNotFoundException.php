<?php

namespace Osds\Api\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class ItemNotFoundException extends BaseException
{
    public function setMessage($entity, $uuid)
    {
        $message = 'Item with uuid ' . $uuid . ' not found for entity ' . $entity;
        $this->logger->info($message);
        parent::setMessageAndCode($message, Response::HTTP_NOT_FOUND);

    }

}
