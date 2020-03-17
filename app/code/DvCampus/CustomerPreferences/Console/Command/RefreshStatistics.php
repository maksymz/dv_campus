<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;

class RefreshStatistics extends \Symfony\Component\Console\Command\Command
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName('dvcampus:customer-preferences:refresh-statistics')
            ->setDescription('DV Campus Refresh Statistics')
            ->setHelp(<<<'EOF'
                Extended command description goes here.
                Command: <info>%command.full_name%</info>
                EOF);
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Running a demo command</info>');
        return Cli::RETURN_SUCCESS;
    }
}
