<?php

namespace Magento\SalesChannels\Cron;

use Magento\SalesChannels\Model\Logging\ChannelManagerLoggerInterface;
use Magento\SalesChannels\Model\ModuleVersionReader;
use Magento\SalesChannels\Service\GraphQlService;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Store\Model\StoreManagerInterface;

class UpdateMerchantInstanceInfo
{
    /**
     * @var GraphQlService
     */
    private GraphQlService $graphQlService;

    /**
     * @var  ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ChannelManagerLoggerInterface
     */
    private ChannelManagerLoggerInterface $logger;

    /**
     * @var ModuleVersionReader
     */
    private ModuleVersionReader $moduleVersionReader;

    /**
     * @param GraphQlService $graphQlService
     * @param ProductMetadataInterface $productMetadata
     * @param StoreManagerInterface $storeManager
     * @param ChannelManagerLoggerInterface $logger
     * @param ModuleVersionReader $moduleVersionReader
     */
    public function __construct(
        GraphQlService           $graphQlService,
        ProductMetadataInterface $productMetadata,
        StoreManagerInterface    $storeManager,
        ChannelManagerLoggerInterface $logger,
        ModuleVersionReader $moduleVersionReader
    ) {
        $this->graphQlService = $graphQlService;
        $this->productMetadata = $productMetadata;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->moduleVersionReader = $moduleVersionReader;
    }

    public function execute(): void
    {
        $this->logger->info('Starting sending updated info (version, edition, url) to channel manager');
        $result = $this->graphQlService->updateMerchantInstanceInfo(
            $this->productMetadata->getVersion(),
            $this->productMetadata->getEdition(),
            $this->storeManager->getStore()->getBaseUrl(),
            $this->moduleVersionReader->getVersion()
        );

        if ($result) {
            $this->logger->info("Sending updated instance info to channel manager done successfully");
        } else {
            $this->logger->error("Failed to send updated instance info to channel");
        }
    }
}
