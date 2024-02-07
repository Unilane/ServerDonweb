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

use Madkting\Connect\Model\Task\ConsultFeedFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConsultUnfinishedFeedsCommand
 * @package Madkting\Connect\Console\Command
 */
class ConsultUnfinishedFeedsCommand extends Command
{
    /**
     * @var ConsultFeedFactory
     */
    protected $consultFeedFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * ConsultUnfinishedFeedsCommand constructor
     *
     * @param State $state
     * @param ConsultFeedFactory $consultFeedFactory
     * @param null $name
     */
    public function __construct(
        State $state,
        ConsultFeedFactory $consultFeedFactory,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->consultFeedFactory = $consultFeedFactory;
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('madkting:consult-unfinished-feeds')->setDescription('Consult Madkting\'s unfinished feeds');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            /* Set area code */
            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            $output->write('Consulting feeds... ');
            $startTime = microtime(true);
            $response = $this->consultFeedFactory->create()->execute();
            $resultTime = microtime(true) - $startTime;

            if (empty($response['success']) && empty($response['error'])) {
                $output->writeln('There are no unfinished feeds.');
            } else {
                /* Success count */
                if (!empty($response['success'])) {
                    $taskStr = $response['success'] > 1 ? 'feeds' : 'feed';
                    $output->writeln('<info>' . $response['success'] . ' ' . $taskStr . ' processed in ' . gmdate('H:i:s', $resultTime) . '.</info>');
                }

                /* Error count */
                if (!empty($response['error'])) {
                    $taskStr = $response['error'] > 1 ? 'feeds' : 'feed';
                    $output->writeln('<fg=red>There was an error processing ' . $response['error'] . ' ' . $taskStr . ' please check the log file for more info.</>');
                }
            }
        } catch (\Exception $e) {
            $output->writeln('<fg=red>There was an error!</>');
            throw $e;
        }
    }
}
