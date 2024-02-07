<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Service;

use Magento\Framework\App\Request\Http;
use Magento\SalesChannels\Model\Logging\ChannelManagerLoggerInterface;
use Magento\SalesChannels\Model\ServiceClientInterface;

class GraphQlService
{
    const SALESCHANNELS_GRAPHQL_PATH = '/sales-channels/graphql';

    private ServiceClientInterface $serviceClient;

    private ChannelManagerLoggerInterface $logger;

    public function __construct(
        ServiceClientInterface  $serviceClient,
        ChannelManagerLoggerInterface $logger
    )
    {
        $this->serviceClient = $serviceClient;
        $this->logger = $logger;
    }

    /**
     * @param string $version
     * @param string $edition
     * @param string $url
     * @return bool
     */
    public function updateMerchantInstanceInfo(
        string $version, string $edition, string $url, string $extensionVersion
    ): bool
    {
        $query = <<<QUERY
{
	"query": "mutation updateMerchantInstanceInfo(\$merchantInstanceInfo:MerchantInstanceInfoRequest!){updateMerchantInstanceInfo(merchantInstanceInfo:\$merchantInstanceInfo){submissionStatus{status message}userErrors{message path}}}",
	"variables": {
		"merchantInstanceInfo": {
			"version": "$version",
			"edition": "$edition",
			"baseUrl": "$url",
			"extensionVersion": "$extensionVersion"
		}
	}
}
QUERY;

        $result = $this->serviceClient->request(
            ['Content-Type' => 'application/json'],
            self::SALESCHANNELS_GRAPHQL_PATH,
            Http::METHOD_POST,
            $query
        );

        if ($result['status'] !== 200) {
            $this->logGraphqlFailed($result);
            return false;
        }

        if (isset($result['errors']) && !empty($result['errors'])) {
            if (is_array($result['errors'])) {
                $message = $result['errors'][0]['message'];
            } else {
                $message = $result['errors']['message'];
            }

            $this->logger->error(sprintf("Graphql error code %s, message: %s",
                $result['status'],
                $message),
                $result['errors']);
            return false;
        }

        if (isset($result['data']['updateMerchantInstanceInfo']['submissionStatus']['status']) &&
            $result['data']['updateMerchantInstanceInfo']['submissionStatus']['status'] === 'KO') {
            $this->logger->error(
                sprintf(
                    "Error sending merchant instance updated info to sales channels service: %s ",
                    $result['data']['updateMerchantInstanceInfo']['submissionStatus']['message']
                )
            );
            return false;
        }

        $this->logger->info(
            sprintf(
                "Send update of merchant instance info to sales channels service: %s ",
                $result['data']['updateMerchantInstanceInfo']['submissionStatus']['message']
            )
        );
        return true;
    }

    /**
     * @param array $result
     * @return void
     */
    public function logGraphqlFailed(array $result): void
    {
        $message = isset($result['message']) ? $result['message'] : "Internal error";
        $this->logger->error(sprintf("Graphql error code %s, message: %s", $result['status'], $message));
    }
}

