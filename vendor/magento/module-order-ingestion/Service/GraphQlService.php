<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Service;

use Magento\Framework\App\Request\Http;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Model\ServiceClientInterface;

//TODO:Tech debt https://jira.corp.magento.com/browse/CHAN-5122
class GraphQlService
{
    const SALESORDERS_GRAPHQL_PATH = '/salesorders/graphql';
    const SALESCHANNELS_GRAPHQL_PATH = '/sales-channels/graphql';

    private ServiceClientInterface $serviceClient;

    private OrderIngestionLoggerInterface $logger;

    public function __construct(
        ServiceClientInterface\Proxy  $serviceClient,
        OrderIngestionLoggerInterface $logger
    )
    {
        $this->serviceClient = $serviceClient;
        $this->logger = $logger;
    }

    /**
     * @param string $storeViewCode
     * @param string $salesChannel
     * @param string $createdAfter
     * @param string $createdBefore
     * @param string $orderState
     */
    public function getOrdersServiceOrders(
        string $storeViewCode,
        string $salesChannel,
        string $createdAfter,
        string $createdBefore,
        string $orderState = 'NEW'
    ): array
    {
        $query = <<<QUERY
      {"query":"query {
      getOrdersByDate(
        storeViewCode: \\"$storeViewCode\\"
        salesChannel: \\"$salesChannel\\"
        createdAfter: \\"$createdAfter\\"
        createdBefore: \\"$createdBefore\\"
        orderState: $orderState )
      {
        orders {
          orderId {
            id
          }
          externalId {
            id
            salesChannel
          }
          createdAt
          updatedAt
          state
          status
          storeViewCode
          customerEmail
          customerNote
          shipping {
            shippingAddress {
              phone
              region
              postcode
              street
              city
              country
              firstname
              lastname
            }
            shippingMethodName
            shippingMethodCode
            shippingAmount
            shippingTax
          }
          payment {
            billingAddress {
              phone
              region
              postcode
              street
              city
              country
              firstname
              lastname
            }
            paymentMethodName
            paymentMethodCode
            totalAmount
            taxAmount
            currency
          }
          items {
            itemId {
              id
            }
            sku
            name
            qty
            unitPrice
            itemPrice
            discountAmount
            taxAmount
            totalAmount
            weight
            createdAt
            additionalInformation {
              name
              value
            }
          }
          isVirtual
          additionalInformation {
            name
            value
          }
        }
      }
    }"
    }
QUERY;

        $result = $this->serviceClient->request(
            ['Content-Type' => 'application/json'],
            self::SALESORDERS_GRAPHQL_PATH,
            Http::METHOD_POST,
            $query
        );

        if ($result['status'] !== 200) {
            $this->logGraphqlFailed($result);
            return [];
        }

        if (isset($result['errors']) && empty($result['errors'])) {
            $this->logger->error(sprintf("Graphql error code %s, message %s", $result['status'], $result['message']), $result['errors']);
            return [];
        }

        return $result['data']['getOrdersByDate']['orders'] ?? [];
    }

    /**
     * @param string $ordersIngested
     * @return bool
     */
    public function notifyOrdersCreated(
        string $ordersIngested
    ): bool
    {
        $query = <<<QUERY
{
	"query": "mutation notifyOrdersIngested(\$ordersIngestionNotification:OrdersIngestionRequest!){notifyOrdersIngested(ordersIngestionNotification:\$ordersIngestionNotification){submissionStatus{status message}userErrors{message path}}}",
	"variables": {
		"ordersIngestionNotification": {
			"ordersIngested": $ordersIngested
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

        if (isset($result['errors'])) {
            if (empty($result['errors'])) {
                $this->logger->error(sprintf("Graphql error code %s, message %s", $result['status'], $result['message']), $result['errors']);
            }
            else {
                $this->logger->info(
                    sprintf(
                        "Error notifying result to sales channels service: %s ",
                        json_encode($result['errors'])
                    )
                );
            }
            return false;
        }

        if (isset($result['data']['notifyOrdersIngested']['submissionStatus']['status']) &&
            $result['data']['notifyOrdersIngested']['submissionStatus']['status'] === 'KO') {
            $this->logger->error(
                sprintf(
                    "Error notifying result to sales channels service: %s ",
                    $result['data']['notifyOrdersIngested']['submissionStatus']['message'] ?? ''
                )
            );
            return false;
        }

        $this->logger->info(
            sprintf(
                "Orders creation notified to sales channels service: %s ",
                $result['data']['notifyOrdersIngested']['submissionStatus']['message'] ?? ''
            )
        );
        return true;
    }

    public function getCancellationReasons(): array
    {
        $query = <<<QUERY
{
	"query": "query getOrderCancelReasons{getOrderCancelReasons{reasons{code label  }}}",
	"variables": {}
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

        if (isset($result['errors']) && empty($result['errors'])) {
            $this->logger->error(sprintf("Graphql error code %s, message %s", $result['status'], $result['message']), $result['errors']);
            return false;
        }

        return $result['data']['getOrderCancelReasons']['reasons'] ?? [];
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
