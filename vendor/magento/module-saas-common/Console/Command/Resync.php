<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSCommon\Console\Command;

use Magento\Framework\Console\Cli;
use Magento\SaaSCommon\Model\ResyncOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\SaaSCommon\Model\ResyncManager;
use Magento\SaaSCommon\Model\ResyncManagerPool;

/**
 * CLI command for Saas feed data resync
 */
class Resync extends Command
{
    private const NO_REINDEX_OPTION = 'no-reindex';
    private const CLEANUP_FEED = 'cleanup-feed';
    private const FEED_OPTION = 'feed';
    private const DRY_RUN = 'dry-run';

    /**
     * @var ResyncManagerPool
     */
    private $resyncManagerPool;

    /**
     * @var ResyncManager
     */
    private $resyncManager;

    /**
     * @var string
     */
    private $feedNames = [];

    /**
     * @var ResyncOptions
     */
    private ResyncOptions $resyncOptions;

    /**
     * @param ResyncManagerPool $resyncManagerPool
     * @param string $name
     * @param array $feedNames
     */
    public function __construct(
        ResyncManagerPool $resyncManagerPool,
        ResyncOptions $resyncOptions,
        $name = '',
        $feedNames = []
    ) {
        parent::__construct($name);
        $this->resyncManagerPool = $resyncManagerPool;
        $this->feedNames = $feedNames;
        $this->resyncOptions = $resyncOptions;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Re-syncs feed data to SaaS service.');
        $this->addOption(
            self::NO_REINDEX_OPTION,
            null,
            InputOption::VALUE_NONE,
            'Run re-submission of feed data to SaaS service only. Does not re-index.'
        );
        $this->addOption(
            self::FEED_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Feed name to fully re-sync to SaaS service. Available feeds: ' . implode(', ', $this->feedNames)
        );
        $this->addOption(
            self::CLEANUP_FEED,
            null,
            InputOption::VALUE_NONE,
            'Force to cleanup feed indexer table before sync.'
        );
        $this->addOption(
            self::DRY_RUN,
            null,
            InputOption::VALUE_NONE,
            'Dry run. Data will not be exported, but payload will be saved to log var/log/saas-export.log'
        );

        parent::configure();
    }

    /**
     * Execute the command to re-sync SaaS data
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $returnStatus = null;
        $feed = $input->getOption(self::FEED_OPTION);
        if ($input->getOption(self::DRY_RUN)) {
            $this->resyncOptions->setIsDryRun(true);
        }
        if (isset($this->feedNames[$feed])) {
            $feedName = $this->feedNames[$feed];
            $this->resyncManager = $this->resyncManagerPool->getResyncManager($feed);
            if ($input->getOption(self::NO_REINDEX_OPTION)) {
                try {
                    $output->writeln('<info>Re-submitting ' . $feedName . ' feed data...</info>');
                    $this->resyncManager->executeResubmitOnly();
                    $output->writeln('<info>' . $feedName . ' feed data re-submit complete.</info>');
                    $returnStatus = Cli::RETURN_SUCCESS;
                } catch (\Exception $ex) {
                    $output->writeln(
                        '<error>An error occurred re-submitting ' . $feedName . ' feed data to SaaS service.</error>'
                    );
                    $returnStatus = Cli::RETURN_FAILURE;
                }
            } else {
                try {
                    $output->writeln('<info>Executing full re-sync of ' . $feedName . ' feed data...</info>');
                    if ($input->getOption(self::CLEANUP_FEED)) {
                        $this->resyncManager->truncateIndexTable();
                    }
                    $this->resyncManager->executeFullResync();
                    $output->writeln('<info>' . $feedName . ' feed data full re-sync complete.</info>');
                    $returnStatus = Cli::RETURN_SUCCESS;
                } catch (\Exception $ex) {
                    $output->writeln('<error>An error occurred re-syncing ' . $feedName
                        . ' feed data to SaaS service: ' . $ex->getMessage() .'.</error>');
                    $returnStatus = Cli::RETURN_FAILURE;
                }
            }
        } else {
            $output->writeln(
                '<error>Resync option --feed is required. Available feeds: '
                . implode(', ', array_keys($this->feedNames))
                . '</error>'
            );
            $returnStatus = Cli::RETURN_FAILURE;
        }

        return $returnStatus;
    }
}
