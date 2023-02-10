<?php

namespace App\DynamoDB\Command;

use App\DynamoDB\Service\DynamoDbService;
use App\Helper\App;
use App\Trait\ConfirmExecutionTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'dev:import:schema')]
class ImportLocalTableSchema extends Command
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
                'The path to the file to import')
            ->addOption('drop',
                null,
                InputOption::VALUE_NONE,
                'Drop the table if it already exists');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->confirmExecution($input, $output)) {
            return Command::FAILURE;
        }

        $filepaths = $input->getOption('path');

        foreach ($filepaths as $filepath) {
            $clientDataSchema = $this->service->loadJsonFile(APP_PATH . '/' . $filepath);

            $tableName = $clientDataSchema['Table']['TableName'];

            if (!$tableName) {
                throw new \LogicException('Table.TableName not found in schema JSON');
            }

            if (
                $input->getOption('drop')
                && $this->service->tableExists($tableName)
            ) {
                $output->writeln('dropping table...');
                $this->service->dropTable($tableName);
            }

            try {
                $output->writeln('creating table ' . $tableName);
                $this->service->createTable($clientDataSchema['Table']);
            } catch (\Exception $exception) {
                $output->writeln('ERROR: ' . $exception->getMessage());
                $output->writeln('===================================');
                $output->writeln('The table likely already exists. Try re-running this command with the --drop flag');
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
