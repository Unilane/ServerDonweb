<?php

namespace Magento\SalesChannels\Test\Unit;

use Magento\SalesChannels\Model\ModuleVersionReader;
use PHPUnit\Framework\TestCase;

class ModuleVersionReaderTest extends TestCase
{
    /**
     * @var ModuleVersionReader
     */
    private $moduleVersionReader;

    protected function setUp(): void
    {
        $this->moduleVersionReader = new ModuleVersionReader();
    }

    public function testGetVersion()
    {
        $version = $this->moduleVersionReader->getVersion();
        $this->assertNotEmpty($version);
        $this->assertNotEquals('UNKNOWN', $version);
    }

}
