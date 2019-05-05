<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\AMQP\AMQPInterface;

final class InsertEntityCommandHandler implements CommandHandler
{
    private $useCase;
    private $amqp;

    public function __construct(
        InsertEntityUseCase $useCase,
        AMQPInterface $amqp
    )
    {
        $this->useCase = $useCase;
        $this->amqp = $amqp;
    }

    public function handle(InsertEntityCommand $command, $forceExecution = false)
    {
        try {

            $queue = $command->getQueue();

            if(
                !$forceExecution
                && ($queue !== null)
            ) {

                 $this->amqp->publish($queue, $command->getPayload());

            } else {

                $this->useCase->execute(
                    $command->entity(),
                    $command->uuid(),
                    $command->data()
                );
                $this->amqp->publish($queue . 'insert_entity_event_queue', $command->getPayload());

            }


        } catch(\Exception $e) {
            dd($e->getMessage());
        }

         return $command->uuid();
    }
}
