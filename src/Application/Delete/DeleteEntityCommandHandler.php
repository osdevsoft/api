<?php

namespace Osds\Api\Application\Delete;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\AMQP\AMQPInterface;

final class DeleteEntityCommandHandler implements CommandHandler
{
    private $useCase;
    private $amqp;

    public function __construct(
        DeleteEntityUseCase $useCase,
        AMQPInterface $amqp
    ) {
        $this->useCase = $useCase;
        $this->amqp = $amqp;
    }

    public function handle(DeleteEntityCommand $command, $forceExecution = false)
    {
        try {
            $queue = $command->getQueue();
            if (!$forceExecution
                && ($queue !== null)
            ) {
                 $this->amqp->publish($queue, $command->getPayload());
            } else {
                $this->useCase->execute(
                    $command->entity(),
                    $command->uuid()
                );
                $this->amqp->publish('delete_completed', $command->getPayload());

            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

         return $command->uuid();
    }
}
