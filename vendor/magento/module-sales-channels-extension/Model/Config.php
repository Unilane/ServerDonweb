<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Model;

use Magento\Framework\App\Cache\Type\Config as CacheConfig;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\SalesChannels\Service\BuildBackendUrl;
use Magento\ServicesId\Model\MerchantRegistryProvider;
use Magento\ServicesId\Model\ServicesConfig;
use Magento\Shipping\Model\Config as ShippingModelConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

//Todo: https://jira.corp.magento.com/browse/CHAN-5093 Should be extracted to a common extension
class Config
{
    private const CONFIG_PATH_ENVIRONMENT = 'magento_saas/environment';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ServicesConfig
     */
    private $servicesConfig;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var TypeListInterface
     */
    private $typeList;

    /**
     * @var MerchantRegistryProvider
     */
    private $merchantRegistryProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\SalesChannels\Service\BuildBackendUrl
     */
    private $buildBackendUrl;

    /**
     * @var StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;

    /**
     * @var ShippingModelConfig
     */
    private $shippingConfig;

    /**
     * @var  ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     *
     */
    private  $timezone;

    /**
     * @var ModuleVersionReader
     */
    private ModuleVersionReader $moduleVersionReader;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ServicesConfig $servicesConfig
     * @param WriterInterface $configWriter
     * @param TypeListInterface $typeList
     * @param MerchantRegistryProvider $merchantRegistryProvider
     * @param StoreManagerInterface $storeManager
     * @param BuildBackendUrl $buildBackendUrl
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param ShippingModelConfig $shippingConfig
     * @param ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $date
     * @param ModuleVersionReader $moduleVersionReader
     */
    public function __construct(
        ScopeConfigInterface                        $scopeConfig,
        ServicesConfig                              $servicesConfig,
        WriterInterface                             $configWriter,
        TypeListInterface                           $typeList,
        MerchantRegistryProvider                    $merchantRegistryProvider,
        StoreManagerInterface                       $storeManager,
        BuildBackendUrl                             $buildBackendUrl,
        StockByWebsiteIdResolverInterface           $stockByWebsiteIdResolver,
        ShippingModelConfig                         $shippingConfig,
        ProductMetadataInterface                    $productMetadata,
        \Magento\Framework\Stdlib\DateTime\Timezone $date,
        ModuleVersionReader                         $moduleVersionReader
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->servicesConfig = $servicesConfig;
        $this->configWriter = $configWriter;
        $this->typeList = $typeList;
        $this->merchantRegistryProvider = $merchantRegistryProvider;
        $this->storeManager = $storeManager;
        $this->buildBackendUrl = $buildBackendUrl;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->shippingConfig = $shippingConfig;
        $this->productMetadata = $productMetadata;
        $this->timezone = $date;
        $this->moduleVersionReader = $moduleVersionReader;
    }

    /**
     * @return string
     */
    public function getServicesEnvironmentId(): ?string
    {
        return $this->servicesConfig->getEnvironmentId();
    }

    /**
     * @return string
     */
    public function getEnvironmentType(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_ENVIRONMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param array $config
     * @return void
     */
    public function saveConfig(array $config): void
    {
        $this->configWriter->save(
            self::CONFIG_PATH_ENVIRONMENT,
            $config['environment'],
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $this->typeList->cleanType(CacheConfig::TYPE_IDENTIFIER);
    }

    /**
     * @return string
     */
    public function getProjectId(): string
    {
        return (string)$this->servicesConfig->getProjectId();
    }

    /**
     * @return string
     */
    public function isApiKeySet(): string
    {
        return (string)$this->servicesConfig->isApiKeySet();
    }

    /**
     * Check is Magento Services configured.
     *
     * @return bool
     */
    public function isMagentoServicesConfigured(): bool
    {
        $merchantRegistryData = $this->merchantRegistryProvider->getMerchantRegistry();

        return $merchantRegistryData &&
            !isset($merchantRegistryData['error']) &&
            $this->servicesConfig->isApiKeySet() &&
            $this->servicesConfig->getEnvironmentId();
    }

    /**
     * Generates a fresh path and token to the products grid page.
     *
     * @return string
     */
    public function getProductsGridPath(): string
    {
        // If you prefix the routePath with "/" it will generate a relative url to the current context...
        return $this->buildBackendUrl->getUrl('catalog/product');
    }

    /**
     * Generates a fresh path and token to the commerce services connector page.
     *
     * @return string
     */
    public function getCommerceServicesConnectorPath(): string
    {
        // adminhtml becomes admin/admin cause reasons...
        return $this->buildBackendUrl->getUrl('adminhtml/system_config/edit/section/services_connector');
    }

    /**
     * Generates a fresh path and token to the product attribute creation form.
     *
     * @return string
     */
    public function getNewAttributePath(): string
    {
        // If you prefix the routePath with "/" it will generate a relative url to the current context...
        return $this->buildBackendUrl->getUrl('catalog/product_attribute/new');
    }

    /**
     * Generates an array of store views/data that is needed to uniquely identify a store view.
     *
     * @return array
     */
    public function getStoreViews(): array
    {
        return array_values(array_map(function ($view) {
            try {
                $group = $this->storeManager->getGroup($view->getStoreGroupId());
                $website = $this->storeManager->getWebsite($view->getWebsiteId());
                $stock = $this->stockByWebsiteIdResolver->execute((int)$website->getId());
                return [
                    'name' => $view->getName(),
                    'code' => $view->getCode(),
                    'group' => [
                        'code' => $group->getCode(),
                        'name' => $group->getName(),
                    ],
                    'website' => [
                        'code' => $website->getCode(),
                        'name' => $website->getName(),
                    ],
                    'stock' => [
                        'id' => $stock->getStockId(),
                        'name' => $stock->getName()
                    ]
                ];
            } catch (LocalizedException $e) {
                // TODO: getWebsite throws, figure out why, and whether it matters.
                return [];
            }
        }, $this->storeManager->getStores()));
    }

    /**
     * @return array
     */
    public function getCarriers() : array
    {
        $stores = $this->storeManager->getStores();
        $carriers = [];
        foreach ($stores as $store) {
            $website = $this->storeManager->getWebsite($store->getWebsiteId());
            $group = $this->storeManager->getGroup($store->getStoreGroupId());
            $carriers[$website->getCode()][$group->getCode()][$store->getCode()] = $this->getCarriersByStore((int)$store->getId());
        }
        return $carriers;
    }

    private function getCarriersByStore(int $storeId)
    {
        $carriers = $this->shippingConfig->getAllCarriers($storeId);
        $carriersOutput = [];
        foreach ($carriers as $carrier) {
            if($carrier->isTrackingAvailable()) {
                $carriersOutput[] = [
                    'code' => $carrier->getCarrierCode(),
                    'label' => $carrier->getConfigData('title')
                ];
            }
        }
        return $carriersOutput;
    }

    public function getInstanceInformation() : array
    {
        return [
            'url' => $this->storeManager->getStore()->getBaseUrl(),
            'version' => $this->productMetadata->getVersion(),
            'edition' => $this->productMetadata->getEdition(),
            'timezone' => $this->timezone->getConfigTimezone(),
            'dateformat' => $this->timezone->getDateFormat(),
            'extensionversion' => $this->moduleVersionReader->getVersion()
        ];
    }
}
