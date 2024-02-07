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

namespace Madkting\Connect\Logger;

use Madkting\Connect\Model\Config;
use Madkting\Exception\MadktingException;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Monolog\Logger;

/**
 * Class MadktingLogger
 * @package Madkting\Connect\Log
 */
class MadktingLogger extends Logger
{
    /**
     * Default title for emails
     */
    const DEFAULT_TITLE = 'Madkting Error';

    /**
     * Template ID
     */
    const TEMPLATE_ID = 'madkting_error_email';

    /**
     * @var array
     */
    protected $senderInfo = ['name' => 'Madkting Support Magento 2', 'email' => 'magento-error@madkting.com'];

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * MadktingLogger constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Config $config,
        StoreManagerInterface $storeManager,
        $name,
        array $handlers = array(),
        array $processors = array()
    ) {
        parent::__construct($name, $handlers, $processors);
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }
    /**
     * @param \Throwable|\Exception $exception
     * @param string $message
     * @param array $context
     * @param bool $sendMail
     * @param bool $logCase
     * @return bool
     */
    public function exception($exception, $message, array $context = array(), $sendMail = false, $logCase = true)
    {
        /* Add exception string to context */
        $context['exception'][] = get_class($exception) . ': ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine();
        $context['exceptionString'] = $exception->__toString();

        /* If is Madkting exception complete $context and turn on send mail */
       if ($exception instanceof MadktingException) {
            $sendMail = true;
            $context['exception']['response'] = $exception->getResponse();
            $context['exception']['request'] = $exception->getRequest();
            $context['exception']['connectionError'] = $exception->isConnectionError();
            $context['exception']['result'] = $exception->getResult();
       }

       return $this->log(self::ERROR, $message, $context, $sendMail, $logCase);
    }

}
