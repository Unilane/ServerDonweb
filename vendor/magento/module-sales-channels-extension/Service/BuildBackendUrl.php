<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\SalesChannels\Service;

use Magento\Framework\App\Action\Context;

class BuildBackendUrl
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $routePath
     * @param array $urlParameters my/controller/id/12/
     * @param array $query ?param=value&param2=value
     * @param string|null $fragment /#url-fragment
     * @return string
     */
    public function getUrl(
        string $routePath,
        array $urlParameters = [],
        array $query = [],
        ?string $fragment = null
    ): string {
        $params = $urlParameters;
        if ($query) {
            $params['_query'] = $query;
        }
        $url = $this->context->getUrl()->getUrl($routePath, $params);
        if ($fragment) {
            $url .= '#' . $fragment;
        }
        return $url;
    }
}
