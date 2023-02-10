<?php
namespace App\Trait;

use App\Helper\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

trait ConfirmExecutionTrait
{
    public function confirmExecution(InputInterface $input, OutputInterface $output, $abortIfProd = true)
    {
        if (!$this instanceof Command) {
            throw new \LogicException('This trait is only for Symfony console component commands');
        }

        if ($abortIfProd && App::isProd()) {
            throw new \RuntimeException('This command cannot be run on production. Aborting.');
        }

        $output->writeLn('**************************************************************');
        $output->writeLn('');
        $output->writeLn('THIS COMMAND IS ONLY INTENDED FOR DEV PURPOSES');
        $output->writeLn('BE AWARE THAT DESTRUCTIVE OR UNEXPECTED CHANGES MAY OCCUR.');
        $output->writeLn('');
        $output->writeLn('**************************************************************');


        $questionHelper = $this->getHelper('question');

        $question = new ConfirmationQuestion('Continue? [y/N]: ', false);

        $confirmation = $questionHelper->ask($input, $output, $question);

        if ($confirmation) {
            return true;
        }

        throw new \LogicException('Aborting.');
    }
}
