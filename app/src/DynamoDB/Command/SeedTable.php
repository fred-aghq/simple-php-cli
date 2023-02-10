<?php

namespace App\DynamoDB\Command;

use App\DynamoDB\Service\DynamoDbService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('dev:seed', description: 'Seeds a table (if a matching App\\Fixture class is found)')]
class SeedTable extends Command
{
    protected function configure()
    {
        $this
            ->addOption('table', null, InputOption::VALUE_REQUIRED)
            ->addOption('count', null, InputOption::VALUE_REQUIRED)
            ->addOption('drop', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getOption('table');
        $count = $input->getOption('count');

        if (!$count) { throw new \LogicException('--count must be an integer greater than 1, e.g. --count=10'); }

        $progress = new ProgressBar($output, $count);


        $fixtureClass = 'App\\DynamoDB\\Fixture\\' . $table . 'Fixture';

        if (!class_exists($fixtureClass)) {
            throw new \InvalidArgumentException('fixture class for table ' . $table . ' not found.');
        }

        $fixture = new $fixtureClass();

        $service = new DynamoDbService();

        //@TODO: implement drop table option / destructive process confirmation

        $progress->start($count, -1);

        foreach ($fixture->generate($count) as $row) {
            $service->putItem($table, $row);
            $progress->advance();
        }

        $progress->finish();
        return Command::SUCCESS;
    }
}
