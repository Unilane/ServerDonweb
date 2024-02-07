<?php
/**
 * Madkting Software (http://www.madkting.com)
 *
 *                                      ..-+::moossso`:.
 *                                    -``         ``ynnh+.
 *                                 .d                 -mmn.
 *     .od/hs..sd/hm.   .:mdhn:.   yo                 `hmn. on     mo omosnomsso oo  .:ndhm:.   .:odhs:.
 *    :hs.h.shhy.d.mh: :do.hd.oh:  /h                `+nm+  dm   ys`  ````mo```` hn :ds.hd.yo: :oh.hd.dh:
 *    ys`   `od`   `h+ sh`    `do  .d`              `snm/`  +s hd`        hd     yy yo`    `sd oh`    ```
 *    hh     sh     +m hs      yy   y-            `+mno`    dkdm          +d     o+ no      ss ys    dosd
 *    y+     ss     oh hdsomsmnmy   ++          .smh/`      om ss.        dh     mn yo      oh sm      hy
 *    sh     ho     ys hs``````yy   .s       .+hh+`         ys   hs.      os     yh os      d+ od+.  ./m/
 *    od     od     od od      od   +y    .+so:`            od     od     od     od od      od  `syssys`
 *                                 .ys .::-`
 *                                o.+`
 *
 * @category Module
 * @package Madkting\Connect
 * @author Carlos Guillermo Jiménez Salcedo <guillermo@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Console\Command;

use Madkting\Connect\Model\Task\CleanTaskFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanFinishedTasksCommand
 * @package Madkting\Connect\Console\Command
 */
class CleanFinishedTasksCommand extends Command
{
    /**
     * @var CleanTaskFactory
     */
    protected $cleanTaskFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * CleanFinishedTasksCommand constructor
     *
     * @param State $state
     * @param CleanTaskFactory $cleanTaskFactory
     * @param null $name
     */
    public function __construct(
        State $state,
        CleanTaskFactory $cleanTaskFactory,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->cleanTaskFactory = $cleanTaskFactory;
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('madkting:clean-finished-tasks')->setDescription('Clean Madkting\'s finished tasks in queue');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            /* Set area code */
            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            $output->write('Cleaning tasks... ');
            $startTime = microtime(true);
            $response = $this->cleanTaskFactory->create()->execute();
            $resultTime = microtime(true) - $startTime;

            if (empty($response['success']) && empty($response['error'])) {
                $output->writeln('There are no tasks to clean.');
            } else {
                /* Success count */
                if (!empty($response['success'])) {
                    $taskStr = $response['success'] > 1 ? 'tasks' : 'task';
                    $output->writeln('<info>' . $response['success'] . ' ' . $taskStr . ' cleaned in ' . gmdate('H:i:s', $resultTime) . '.</info>');
                }

                /* Error count */
                if (!empty($response['error'])) {
                    $taskStr = $response['error'] > 1 ? 'tasks' : 'task';
                    $output->writeln('<fg=red>There was an error cleaning ' . $response['error'] . ' ' . $taskStr . ' please check the log file for more info.</>');
                }
            }
        } catch (\Exception $e) {
            $output->writeln('<fg=red>There was an error!</>');
            throw $e;
        }
    }
}
