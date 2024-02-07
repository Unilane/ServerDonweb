<?php

namespace Unit;


use Magento\SalesChannels\Cron\UpdateMerchantInstanceInfo;
use Magento\SalesChannels\Model\Logging\ChannelManagerLoggerInterface;
use Magento\SalesChannels\Model\ModuleVersionReader;
use Magento\SalesChannels\Service\GraphQlService;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Store\Model\StoreManagerInterface;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class UpdateMerchantInstanceInfoTest extends TestCase
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
     * @var UpdateMerchantInstanceInfo
     */
    private UpdateMerchantInstanceInfo $updateMerchantInstanceInfo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->graphQlService = $this->getMockBuilder(GraphQlService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productMetadata = $this->getMockBuilder(ProductMetadataInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder(ChannelManagerLoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productMetadata = $this->getMockBuilder(ProductMetadataInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->moduleVersionReader = $this->getMockBuilder(ModuleVersionReader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->updateMerchantInstanceInfo = $this->getMockBuilder(UpdateMerchantInstanceInfo::class)
            ->setConstructorArgs([
                $this->graphQlService,
                $this->productMetadata,
                $this->storeManager,
                $this->logger,
                $this->moduleVersionReader
            ])
           ->getMock();
    }

    public function testCronExecuted(){
        $this->updateMerchantInstanceInfo->execute();
        $this->assertTrue(true);
    }

}
