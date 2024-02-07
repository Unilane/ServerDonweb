<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Test\Integration\Job;

use Magento\Framework\ObjectManagerInterface;
use Magento\OrderIngestion\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config|mixed
     */
    private $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->config = $this->objectManager->get(Config::class);

    }

    public function testGetCarriers()
    {
        $expectedCarriers = [
            'base' =>
                ['main_website_store' =>
                    ["default" =>
                        [
                            [
                                'code' => 'dhl',
                                'label' => 'DHL',
                            ],
                            [
                                'code' => 'fedex',
                                'label' => 'Federal Express',
                            ],
                            [
                                'code' => 'ups',
                                'label' => 'United Parcel Service',
                            ],
                            [
                                'code' => 'usps',
                                'label' => 'United States Postal Service',
                            ],
                        ],
                    ]
                ]
        ];

        $carriers = $this->config->getCarriers();

        $this->assertEquals($expectedCarriers, $carriers);
    }

    public function testGetStoreViews()
    {
        $storeViews = $this->config->getStoreViews();
        $this->assertCount(1, $storeViews);
        $this->assertEquals("default", $storeViews[0]["code"]);
    }
}
