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
 * @author Israel Calderón Aguilar <israel@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Model;

use Madkting\MadktingClient;
use Magento\Config\Model\Config\Factory as ConfigFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Madkting\Connect\Model
 */
class Config
{
    /**
     * Module name
     */
    const MODULE_NAME = 'Madkting_Connect';

    /**
     * Configuration paths
     */
    const GENERAL_PATH = 'madkting_general/';
    const GENERAL_CONNECTION_PATH = 'connection/';
    const SYNC_PATH = 'madkting_synchronization/';
    const SYNC_ORDERS_PATH = 'orders/';
    const SYNC_PRODUCTS_PATH = 'products/';
    const SYNC_TASKS_PATH = 'tasks_queue/';

    /**
     * The value in second or unix of 24 hours
     */
    const ONE_DAY = 86400;

    /**
     * Default task history lifetime values
     */
    const DEFAULT_TASK_SUCCESS_LIFETIME = '30';
    const DEFAULT_TASK_FAILURE_LIFETIME = '60';

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $config
     * @param Encryptor $encryptor
     * @param ConfigFactory $configFactory
     * @param ModuleListInterface $moduleList
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ScopeConfigInterface $config,
        Encryptor $encryptor,
        ConfigFactory $configFactory,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata
    ) {
        $this->config = $config;
        $this->encryptor = $encryptor;
        $this->configFactory = $configFactory;
        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Get Magento version number
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * Get module version number
     *
     * @return string
     */
    public function getModuleVersionNumber()
    {
        return $this->moduleList->getOne(self::MODULE_NAME)['setup_version'];
    }

    /**
     * Get Madkting API token
     *
     * @param int|null $store
     * @param string $scope
     * @return string|bool
     */
    public function getMadktingToken($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        /* Validate token */
        $token = $this->encryptor->decrypt($this->config->getValue(self::GENERAL_PATH . self::GENERAL_CONNECTION_PATH . 'api_token', $scope, $store));
        try {
            $client = new MadktingClient(['token' => $token]);
            $client->testToken();

            return $token;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Magento store selected to integrate with Madkting
     *
     * @param int|null $store
     * @param string $scope
     * @return int
     */
    public function getSelectedStore($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->config->getValue(self::GENERAL_PATH . self::GENERAL_CONNECTION_PATH . 'store_id', $scope, $store);
    }

    /**
     * Get if orders synchronization is enabled
     *
     * @param int|null $store
     * @param string $scope
     * @return bool
     */
    public function isSynchronizeOrdersEnabled($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->config->getValue(self::SYNC_PATH . self::SYNC_ORDERS_PATH . 'sync_orders', $scope, $store);
    }

    /**
     * Get start date for orders creation
     *
     * @param int|null $store
     * @param string $scope
     * @return mixed
     */
    public function getStartCreationOrderDate($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->config->getValue(self::SYNC_PATH . self::SYNC_ORDERS_PATH . 'start_creation_from', $scope, $store);
    }

    /**
     * Get if orders with shipping address lacking can be created
     *
     * @param int|null $store
     * @param string $scope
     * @return bool
     */
    public function isNoShippingAddressOrdersEnabled($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->config->getValue(self::SYNC_PATH . self::SYNC_ORDERS_PATH . 'shipping_address_lacking', $scope, $store);
    }

    /**
     * Get if orders with fulfillment by marketplace can be created
     *
     * @param int|null $store
     * @param string $scope
     * @return bool
     */
    public function isFulfillmentOrdersEnabled($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->config->getValue(self::SYNC_PATH . self::SYNC_ORDERS_PATH . 'create_fulfillment_orders', $scope, $store);
    }

    /**
     * Get if products permanent synchronization is enabled
     *
     * @param int|null $store
     * @param string $scope
     * @return bool
     */
    public function isPermanentSynchronizationEnabled($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'permanent_sync', $scope, $store);
    }

    /**
     * Get if upload disabled products is enabled
     *
     * @param int|null $store
     * @param string $scope
     * @return bool
     */
    public function isUploadDisabledProductsEnabled($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'upload_disabled', $scope, $store);
    }

    /**
     * Get disabled attributes while updating
     *
     * @param int|null $store
     * @param string $scope
     * @return array
     */
    public function getAttributesDisabledSynchronization($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        if (!array_key_exists('attributes_disabled_synchronization', $this->values)) {
            $string = $this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'attributes_disabled_sync', $scope, $store);
            $this->values['attributes_disabled_synchronization'] = !empty($string) ? explode(',', $string) : [];
        }

        return $this->values['attributes_disabled_synchronization'];
    }

    /**
     * Get stock quantity to set no-manage-stock products
     *
     * @param int|null $store
     * @param string $scope
     * @return int
     */
    public function getNoManagedStock($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        if (!array_key_exists('no_managed_stock', $this->values)) {
            $this->values['no_managed_stock'] = (int)$this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'no_managed_stock', $scope, $store);
        }

        return $this->values['no_managed_stock'];
    }

    /**
     * Get selected stocks to be used
     *
     * @param int|null $store
     * @param string $scope
     * @return array
     */
    public function getSelectedStocks($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        if (!array_key_exists('selected_stocks', $this->values)) {
            $string = $this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'selected_stocks', $scope, $store);
            $this->values['selected_stocks'] = !empty($string) ? explode(',', $string) : [];
        }

        return $this->values['selected_stocks'];
    }

    /**
     * Get the last date products was synchronized
     *
     * @param int|null $store
     * @param string $scope
     * @return int
     */
    public function getLastSyncDate($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return (int)$this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'last_sync_date', $scope, $store);
    }

    /**
     * Get the date to enable synchronization again
     *
     * @param int|null $store
     * @param string $scope
     * @return int
     */
    public function getNextSyncDate($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return ((int)$this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'last_sync_date', $scope, $store) + self::ONE_DAY);
    }

    /**
     * Get the time left to enable synchronization again
     *
     * @return int
     */
    public function getSyncTimeLeft()
    {
        return $this->getNextSyncDate() - time();
    }

    /**
     * Save current time stamp in unix format
     *
     * @return void
     */
    public function setLastSyncDate()
    {
        $configuration = $this->configFactory->create();
        $configuration->setDataByPath(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'last_sync_date', time());
        $configuration->save();
    }

    /**
     * Get Max time in seconds to get products
     *
     * @param int|null $store
     * @param string $scope
     * @return int
     */
    public function getMaxTimeGetProducts($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        if (!array_key_exists('max_time_get_products', $this->values)) {
            $this->values['max_time_get_products'] = (int)$this->config->getValue(self::SYNC_PATH . self::SYNC_PRODUCTS_PATH . 'max_time_get_products', $scope, $store);
        }

        return $this->values['max_time_get_products'];
    }

    /**
     * Get tasks queue success history lifetime
     *
     * @param int|null $store
     * @param string $scope
     * @return int
     */
    public function getTasksSuccessHistoryLifetime($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $days = (int)$this->config->getValue(self::SYNC_PATH . self::SYNC_TASKS_PATH . 'success_lifetime', $scope, $store);

        return !empty($days) ? $days : self::DEFAULT_TASK_SUCCESS_LIFETIME;
    }

    /**
     * Get tasks queue failure history lifetime
     *
     * @param int|null $store
     * @param string $scope
     * @return int
     */
    public function getTasksFailureHistoryLifetime($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        $days = (int)$this->config->getValue(self::SYNC_PATH . self::SYNC_TASKS_PATH . 'failure_lifetime', $scope, $store);

        return !empty($days) ? $days : self::DEFAULT_TASK_FAILURE_LIFETIME;
    }

    /**
     * Get store country ID
     *
     * @param int|null $store
     * @param string $scope
     * @return string
     */
    public function getStoreCountryId($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getValue('general/store_information/country_id', $store, $scope);
    }

    /**
     * Get countries that require region
     *
     * @param int|null $store
     * @param string $scope
     * @return array
     */
    public function getRequiredRegionCountries($store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        if (!array_key_exists('required_region_countries', $this->values)) {
            $string = $this->getValue('general/region/state_required', $store, $scope);
            $this->values['required_region_countries'] = !empty($string) ? explode(',', $string) : [];
        }

        return $this->values['required_region_countries'];
    }

    /**
     * Get value by path
     *
     * @param $path
     * @param int|null $store
     * @param string $scope
     * @return mixed
     */
    public function getValue($path, $store = null, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->config->getValue($path, $scope, $store);
    }
}
