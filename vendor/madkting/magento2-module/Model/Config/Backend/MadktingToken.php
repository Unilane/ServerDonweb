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

namespace Madkting\Connect\Model\Config\Backend;

use Madkting\Connect\Model\Config;
use Madkting\MadktingClient;
use Magento\Config\Model\Config\Backend\Encrypted;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Message\Manager;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class MadktingToken
 * @package Madkting\Connect\Observer\Backend
 */
class MadktingToken extends Encrypted
{
    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @var Manager
     */
    protected $messageManager;

    /**
     * MadktingToken constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param EncryptorInterface $encryptor
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Config $madktingConfig
     * @param Manager $messageManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $encryptor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Config $madktingConfig,
        Manager $messageManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $encryptor,
            $resource,
            $resourceCollection,
            $data
        );
        $this->madktingConfig = $madktingConfig;
        $this->messageManager = $messageManager;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $token = (string)$this->getValue();
        if (!preg_match('/^\*+$/', $token) && !empty($token)) {
            try {
                $client = new MadktingClient(['token' => $token]);
                $client->testToken();
            } catch (\Exception $e) {
                $this->setValue('');
                $this->messageManager->addErrorMessage(__('Invalid Yuju API token'));
            }
        }

        parent::beforeSave();
    }
}