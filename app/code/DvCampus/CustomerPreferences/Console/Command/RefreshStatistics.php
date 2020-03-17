<?php

declare(strict_types=1);

namespace DvCampus\CustomerPreferences\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;
use Magento\Framework\App\Area;

class RefreshStatistics extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * RefreshStatistics constructor.
     * @param \Magento\Framework\App\State $state
     * @param \Psr\Log\LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Psr\Log\LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->logger = $logger;
    }

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
        try {
            $this->state->emulateAreaCode(
                Area::AREA_ADMINHTML,
                \Closure::fromCallable([$this, 'collectStatistics']),
                [$output]
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            return Cli::RETURN_FAILURE;
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param OutputInterface $output
     */
    private function collectStatistics(OutputInterface $output): void
    {
        $output->writeln('<info>Emulating adminhtml area</info>');
    }
}
