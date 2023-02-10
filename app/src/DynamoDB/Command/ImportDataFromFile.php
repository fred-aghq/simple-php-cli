<?php

namespace App\DynamoDB\Command;

use App\DynamoDB\Service\DynamoDbService;
use App\Trait\ConfirmExecutionTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'dev:import:data', description: 'Imports a dynamoDB schema JSON file')]
class ImportDataFromFile extends Command
{
    use ConfirmExecutionTrait;
    protected DynamoDbService $service;

    public function __construct(string $name = null)
    {
        $this->service = new DynamoDbService();
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->addOption('path',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'The path to the file to import');
//            ->addOption('drop',
//                null,
//                InputOption::VALUE_NONE,
//                'Drop the table if it already exists');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->confirmExecution($input, $output)) {
            return Command::FAILURE;
        }

        $filepaths = $input->getOption('path');

        foreach ($filepaths as $filepath) {
            if (!file_exists($filepath)) {
                throw new \InvalidArgumentException('File ' . $filepath . ' not found.');
            }

            $data = $this->service->loadFile($filepath);
            foreach($data as $value) {
                var_dump($value);
            }
        }

        return Command::SUCCESS;
    }
}
