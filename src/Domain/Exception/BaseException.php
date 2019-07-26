<?php

namespace Osds\Api\Domain\Exception;

use Exception;
use Osds\Api\Infrastructure\Log\LoggerInterface;

abstract class BaseException extends Exception implements ApiExceptionInterface
{

    protected $logger;

    public function __construct()
    {
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setMessageAndCode(
        string $message = "",
        int $code = 0
    ) {
        $this->message = $message;
        $this->code = $code;
    }

    public function getResponse()
    {
        return [
            'error_code' => $this->getCode(),
            'error_message' => $this->getMessage()
        ];
    }
}