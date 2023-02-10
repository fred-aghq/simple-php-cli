<?php
namespace App\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'hello')]
class Hello extends Command
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeLn('Hello!');
        return Command::SUCCESS;
    }
}
