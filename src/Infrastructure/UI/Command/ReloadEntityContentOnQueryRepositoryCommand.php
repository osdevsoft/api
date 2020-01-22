<?php

namespace Osds\Api\Infrastructure\UI\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

use Osds\Api\Domain\Bus\Query\QueryHandler;
use Osds\Api\Application\Search\SearchEntityQuery;
use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Application\Replicate\ReplicateForQueryCommand;

class ReloadEntityContentOnQueryRepositoryCommand extends Command
{

    protected static $defaultName = 'osds:reload-entity-content';

    private $queryHandler;
    private $commandHandler;

    public function __construct(
        QueryHandler $queryHandler,
        CommandHandler $commandHandler
    ) {
        $this->queryHandler = $queryHandler;
        $this->commandHandler = $commandHandler;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('osds:reload-entity-content')
            ->setDescription('Reloads an entity content on the Read repository from the Write Repository (usually DB to ES)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $additionalFilters = [];

        $io = new SymfonyStyle($input, $output);
        $io->caution('Be careful!!! This is going to reset all the content of the Read Repository');
        $io->note('Read Repo probably Elastic Search');
        $io->title('Lets get on it');
        $entity = $io->ask('Insert the name of Entity you want to Reload', 'Admin');
//        $entity = $io->choice('Insert the name of Entity you want to Reload', ['user', 'post'], 'user');
        $io->newLine();

        $io->confirm('Sure to proceed?', true);

        $io->section('Truncating data...');
        #TODO: untrashiny
        exec('curl -XDELETE http://elk:9200/' . $entity);

        $io->newLine();
        $io->newLine();

        $queryFilters = ['page' => 1];
        $referencedEntities = $this->getItems(
            $entity,
            ['uuid' => 'null'],
            [],
            ['get_referenced_entities' => true]
        );
        if (isset($referencedEntities['referenced_entities'])
            && count($referencedEntities['referenced_entities']) > 0) {
            $additionalFilters['referenced_entities'] = implode(',', $referencedEntities['referenced_entities']);
        }

        while ($response = $this->getItems($entity, [], $queryFilters, $additionalFilters)) {
            if ($queryFilters['page'] == 1) {
                $io->section('Schema replication');
                $io->newLine();
                $io->newLine();
                $this->recreateStructure($entity, $response['schema']['fields']);

                $io->section('Starting Data replication');
                $io->text('Nice progress bar 8====D');
                $totalItems = $response['total_items'];
                $io->progressStart($totalItems);
            }
            if (count($response['items']) == 0) {
                break;
            }

            foreach ($response['items'] as $item) {
                $commandMessageObject = new ReplicateForQueryCommand(
                    $entity,
                    $item['uuid'],
                    $item,
                    'Osds\Api\Application\Insert\InsertEntityCommand'
                );
                #By now we can't enqueue it because we use ReplicateForQueryCommandHandler
                #$commandMessageObject->setQueue('insert');
                $this->commandHandler->handle($commandMessageObject);
                $io->progressAdvance(1);
            }
            $queryFilters['page']++;
        }

        $io->progressFinish();
        $io->success('Data for entity ' . strtoupper($entity) . ' reloaded properly.');
        $io->error('There was an error while reloading data.');
    }

    private function getItems($entity, $searchFields = [], $queryFilters = [], $additionalRequests = [])
    {
        $queryMessageObject = new SearchEntityQuery(
            $entity,
            $searchFields,
            $queryFilters,
            $additionalRequests
        );
        return $this->queryHandler->handle($queryMessageObject);
    }

    private function recreateStructure($entity, $schema)
    {
        #TODO: untrashiny (ES hardcoded)
        $mapping = [];
        $mapping['properties'] = [];
        foreach ($schema as $field) {
            $mapping['properties'][$field] = 'keyword';
        }
        $cmd = "curl -X PUT http://elk:9200/{$entity}/_mapping -d '" . json_encode($mapping) . "' -H 'Content-Type: application/json'";
        exec($cmd);
    }
}
