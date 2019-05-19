<?php

namespace Osds\Api\UI\Command;

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

    protected static $defaultName = 'osds:reload_entity_content';

    private $queryHandler;
    private $commandHandler;

    public function __construct(
        QueryHandler $queryHandler,
        CommandHandler $commandHandler
    )
    {
        $this->queryHandler = $queryHandler;
        $this->commandHandler = $commandHandler;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('osds:reload_entity_content')
            ->setDescription('Reloads an entity content on the Read repository from the Write Repository (usually DB to ES)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->caution('Be careful!!! This is going to reset all the content of the Read Repository');
        $io->note('Read Repo probably Elastic Search');
        $io->title('Lets get on it');
        $entity = $io->ask('Insert the name of Entity you want to Reload', 'user');
//        $entity = $io->choice('Insert the name of Entity you want to Reload', ['user', 'post'], 'user');
        $io->newLine();

        $io->confirm('Sure to proceed?', true);

        $io->section('Truncating data...');
        #TODO: untrashiny
        exec('curl -XDELETE http://elk:9200/' . $entity);

        $io->newLine();
        $io->newLine();

        $page = 1;

        while ($response = $this->getItems($entity, $page))
        {
            if ($page == 1) {
                $io->section('Schema replication');
                $this->recreateStructure($entity, $response['schema']['fields']);
                $io->newLine();
                $io->newLine();

                $io->section('Starting Data replication');
                $io->text('Nice progress bar 8====D');
                $totalItems = $response['total_items'];
                $io->progressStart($totalItems);
            }
            if(count($response['items']) == 0) break;

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
            $page++;
        }
        $io->progressFinish();
        $io->success('Data for entity ' . strtoupper($entity) . ' reloaded properly.');
        $io->error('There was an error while reloading data.');
    }

    private function getItems($entity, $page)
    {
        $queryMessageObject = new SearchEntityQuery(
            $entity,
            [],
            ['page' => $page]
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
        $cmd = "curl -X PUT http://elk:9200/{$entity}/_mapping -d '" . json_encode($mapping) . "'";
        exec($cmd);
    }
}