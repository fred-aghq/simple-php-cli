<?php
namespace App\Command;
use App\Helper\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'env')]
class CurrentEnv extends Command
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeLn('Current environment is set to: ' . App::env());
        return Command::SUCCESS;
    }
}
