<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IvnewsEnvTemplatePrepareCommand extends Command
{
    protected static $defaultName = 'ivnews:env-template-prepare';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyEnv = $_ENV["SYMFONY_DOTENV_VARS"];
        $symfonyEnv = explode(",", $symfonyEnv);

        $envList = [];
        foreach ($symfonyEnv  as $var) {
            $envList[$var] = $_ENV[$var];
        }
        $envList["APP_ENV"] = "****";

        file_put_contents("variable.json", json_encode($envList, JSON_PRETTY_PRINT));
        return 0;
    }
}
