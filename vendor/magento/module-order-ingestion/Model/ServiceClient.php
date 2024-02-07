<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\OrderIngestion\Model;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\ServicesConnector\Api\ClientResolverInterface;
use Magento\ServicesConnector\Api\KeyValidationInterface;
use Magento\ServicesConnector\Exception\KeyNotFoundException;
use Magento\ServicesConnector\Exception\PrivateKeySignException;
use Psr\Http\Message\ResponseInterface;

//Todo: https://jira.corp.magento.com/browse/CHAN-5093 Should be extracted to a common extension
class ServiceClient implements ServiceClientInterface
{
    /**
     * Extension name for Services Connector
     */
    private const EXTENSION_NAME = 'Magento_OrderIngestion';
    private const FORBIDDEN = 'FORBIDDEN';
    private const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    private const HEADER_MAGENTO_ENVIRONMENT_ID = 'magento-environment-id';
    private const HEADER_X_SAAS_ID = 'x-saas-id';

    /**
     * @var ClientResolverInterface
     */
    private $clientResolver;

    /**
     * @var KeyValidationInterface
     */
    private $keyValidator;

    /**
     * Config
     */
    private $config;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var OrderIngestionLoggerInterface
     */
    private $logger;

    /**
     * @var int[]
     */
    private $successfulResponseCodes = [200, 201, 202, 204];

    /**
     * @param ClientResolverInterface $clientResolver
     * @param KeyValidationInterface $keyValidator
     * @param Config $config
     * @param Json $serializer
     * @param OrderIngestionLoggerInterface $logger
     */
    public function __construct(
        ClientResolverInterface $clientResolver,
        KeyValidationInterface $keyValidator,
        Config $config,
        Json $serializer,
        OrderIngestionLoggerInterface $logger
    ) {
        $this->clientResolver = $clientResolver;
        $this->keyValidator = $keyValidator;
        $this->config = $config;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function request(
        array $headers,
        string $path,
        string $httpMethod = Http::METHOD_POST,
        string $data = '',
        string $requestContentType = 'json'
    ): array {
        try {
            $client = $this->clientResolver->createHttpClient(
                self::EXTENSION_NAME,
                $this->config->getEnvironmentType()
            );
            $options = $this->buildOptions($headers, $data, $path);

            if ($this->isApiKeyValid()) {
                $response = $client->request($httpMethod, $path, $options);
                $isSuccessful = in_array($response->getStatusCode(), $this->successfulResponseCodes, true);
                $result = $this->buildResponse($response->getStatusCode(),'', '', $isSuccessful);
                if ($isSuccessful) {
                    if ($requestContentType === 'json') {
                        try {
                            $result = array_merge(
                                $result,
                                $this->serializer->unserialize($response->getBody()->getContents())
                            );
                        } catch (InvalidArgumentException $e) {
                            $result = $this->buildResponse(500,'', '', false);
                            $this->logger->error(
                                'An error occurred.',
                                $this->requestMeta($path, $data, $options, $response),
                            );
                        }
                    } else {
                        $result = array_merge(
                            $result,
                            [
                                'content_body' => $response->getBody()->getContents(),
                                'content_disposition' => $response->getHeaderLine('Content-Disposition'),
                                'content_length' => $response->getHeaderLine('Content-Length'),
                                'content_type' => $response->getHeaderLine('Content-Type')
                            ]
                        );
                    }
                } else {
                    $result = $this->buildResponse(500);
                    $this->logger->error(
                        'An error occurred.',
                        $this->requestMeta($path, $data, $options, $response),
                    );
                }
            } else {
                $result = $this->buildResponse(403,self::FORBIDDEN, 'Magento API Key is invalid');
                $this->logger->error('API Key Validation failed.');
            }
        }
        catch (KeyNotFoundException $e) {
            $result = $this->buildResponse(403,self::FORBIDDEN, 'Magento API Key not found');
            $this->logger->error('API Key Validation failed.', [
                'exception' => $e->getMessage()
            ]);
        } catch (GuzzleException | InvalidArgumentException | PrivateKeySignException $e) {
            $result = $this->buildResponse(500,self::INTERNAL_SERVER_ERROR, 'An error occurred');
            $this->logger->error("An unexpected exception occured whilst handling a request.", [
                'exception' => $e->getMessage()
            ]);
        }

        return $result;
    }

    /**
     * Validate the API Gateway Key
     *
     * @return bool
     * @throws KeyNotFoundException
     * @throws InvalidArgumentException
     * @throws PrivateKeySignException
     */
    private function isApiKeyValid(): bool
    {
        return $this->keyValidator->execute(
            self::EXTENSION_NAME,
            $this->config->getEnvironmentType()
        );
    }

    private function requestMeta(
        string $path,
        string $data,
        array $options,
        ResponseInterface $response
    ): array {
        return [
            'request' => [
                'path' => $path,
                'headers' => $options['headers'],
                'method' => Http::METHOD_POST,
                'body' => $data,
            ],
            'response' => [
                'body' => $response->getBody()->getContents(),
                'statusCode' => $response->getStatusCode(),
            ],
        ];
    }

    private function buildOptions(array $headers, string $data, string $path): array {
        $servicesEnvironmentIdHeader = $this->getServicesEnvironmentIdHeader($path);
        return [
            'headers' => array_merge(
                $headers,
                [
                    $servicesEnvironmentIdHeader => $this->config->getServicesEnvironmentId()
                ]
            ),
            'body' => $data
        ];
    }

    private function getServicesEnvironmentIdHeader(string $path): string {
        if (strpos($path, 'salesorders') !== false) {
            return self::HEADER_MAGENTO_ENVIRONMENT_ID;
        }
        if (strpos($path, 'sales-channels') !== false) {
            return self::HEADER_X_SAAS_ID;
        }
        return '';
    }

    private function buildResponse(int $status, string $statusText = '', string $message = '', bool $isSuccessful = true): array
    {
        return [
            'status' => $status,
            'statusText' => $statusText,
            'message' => $message,
            'is_successful' => $isSuccessful,
        ];
    }
}
