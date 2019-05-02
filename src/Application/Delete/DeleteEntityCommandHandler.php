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
    )
    {
        $this->useCase = $useCase;
        $this->amqp = $amqp;
    }

    public function handle(DeleteEntityCommand $command)
    {
        try {
            if( ($queue = $command->getQueue() ) !== null) {

                 $this->amqp->publish($queue, $command->getPayload());

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
