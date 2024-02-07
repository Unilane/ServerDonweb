<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\SalesChannels\Model\ConfigurationStatus;

/**
 * @api
 */
class Index extends Template
{
    /**
     * Config path used for frontend url
     */
    private const FRONTEND_URL_PATH = 'sales_channels/frontend_url';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Returns config for frontend url
     *
     * @return string
     */
    public function getFrontendUrl(): string
    {
        return (string) $this->_scopeConfig->getValue(
            self::FRONTEND_URL_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getConfigJson() : string
    {
        $config = [
            'graphqlEndpoint' => $this->_urlBuilder->getUrl('*/*/serviceproxy'),
            'configEndpoint' => $this->_urlBuilder->getUrl('*/*/config'),
            'orderRedirectEndpoint' => $this->_urlBuilder->getUrl('*/*/orderredirect'),
            'creditMemoRedirectEndpoint' => $this->_urlBuilder->getUrl('*/*/creditmemoredirect')
        ];
        return json_encode($config);
    }
}

