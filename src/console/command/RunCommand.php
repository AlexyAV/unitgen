<?php

namespace Unitgen\console\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Unitgen\config\Config;
use Unitgen\Controller;
use Unitgen\exceptions\UnitGenException;
use Unitgen\filter\factory\FilterFactory;
use Unitgen\source\SourceFileParser;
use Unitgen\source\SourcePathScanner;

/**
 * Class RunCommand
 *
 * @package Unitgen\console\command
 * @codeCoverageIgnore
 */
class RunCommand extends Command
{
    const COMMAND_NAME = 'run';

    protected function configure()
    {
        $this->addArgument(
            'config', InputArgument::REQUIRED, 'Path to config file'
        );

        $this->setName(self::COMMAND_NAME)
            ->setDescription('Initialize tests generation.')
            ->setHelp('This command will initialize test creation.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Start test generation.');

        $configPath = $input->getArgument('config');

        $start = -microtime(true);

        try {
            $generatorController = new Controller(
                new Config($configPath),
                new SourcePathScanner,
                new SourceFileParser,
                new FilterFactory
            );

            $generatorController->runGenerator();

        } catch (UnitGenException $error) {
            $io->error('Error: ' . $error->getMessage());
            return;
        }

        $this->printResultOutput($generatorController, $output);

        $output->writeln('Generated in:' . (microtime(true) + $start));
    }

    protected function printResultOutput(
        Controller$generatorController, OutputInterface $output
    ) {
        $output->writeln('<info>Generation completed successful.</info>');

        $output->writeln(str_repeat('-', 32));

        $output->writeln(
            '<options=bold>Number of files in source path: '
            . $generatorController->getSourceFilesCount() . '</>'
        );

        $output->writeln(
            '<options=bold>Number of generated test classes: '
            . $generatorController->getTargetClassesCount() . '</>'
        );

        $output->writeln(str_repeat('-', 32));
    }
}