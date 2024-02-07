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

use Madkting\Connect\Model\ProductTaskQueueFactory;
use Madkting\Connect\Model\Task\QueueUpdatesFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateProducts
 * @package Madkting\Connect\Console\Command
 */
class UpdateProducts extends Command
{
    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;

    /**
     * @var QueueUpdatesFactory
     */
    protected $queueUpdatesFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * RunNextTasksCommand constructor
     *
     * @param State $state
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param QueueUpdatesFactory $queueUpdatesFactory
     * @param null $name
     */
    public function __construct(
        State $state,
        ProductTaskQueueFactory $productTaskQueueFactory,
        QueueUpdatesFactory $queueUpdatesFactory,
        $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
        $this->queueUpdatesFactory = $queueUpdatesFactory;
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('madkting:update-products')->setDescription('Update all the products created in Yuju depending on the sent attributes.');
        $this->addArgument('attributes', InputArgument::IS_ARRAY, 'Attributes to update, if it is empty, all attributes will be updated.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            /* Set area code */
            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            $output->write('Adding products to tasks queue... ');
            $startTime = microtime(true);
            $attributes = $input->getArgument('attributes');
            $response = $this->queueUpdatesFactory->create()->execute($attributes);
            $resultTime = microtime(true) - $startTime;

            if (empty($response['success']) && empty($response['error'])) {
                $output->writeln('There are no products to update.');
            } else {
                /* Success count */
                if (!empty($response['success'])) {
                    $taskStr = $response['success'] > 1 ? 'products' : 'product';
                    $output->writeln('<info>Updating stock of ' . $response['success'] . ' ' . $taskStr . gmdate('H:i:s', $resultTime) . '.</info>');
                }

                /* Error count */
                if (!empty($response['error'])) {
                    $taskStr = $response['error'] > 1 ? 'products' : 'product';
                    $output->writeln('<fg=red>There was an error adding ' . $response['error'] . ' ' . $taskStr . ' to tasks queue.</>');
                }
            }
        } catch (\Exception $e) {
            $output->writeln('<fg=red>There was an error!</>');
            throw $e;
        }
    }
}
