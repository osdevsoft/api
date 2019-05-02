<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\AMQP\AMQPInterface;

final class UpdateEntityCommandHandler implements CommandHandler
{
    private $useCase;
    private $amqp;

    public function __construct(
        UpdateEntityUseCase $useCase,
        AMQPInterface $amqp
    )
    {
        $this->useCase = $useCase;
        $this->amqp = $amqp;
    }

    public function handle(UpdateEntityCommand $command, $forceExecution = false)
    {
        try {
            if(
                !$forceExecution
                && (($queue = $command->getQueue() ) !== null)
            ) {

                 $this->amqp->publish($queue, serialize($command));

            } else {

                $this->useCase->execute(
                    $command->entity(),
                    $command->uuid(),
                    $command->data()
                );
            }
        } catch(\Exception $e) {
            dd($e->getMessage());
        }

         return $command->uuid();
    }
}
