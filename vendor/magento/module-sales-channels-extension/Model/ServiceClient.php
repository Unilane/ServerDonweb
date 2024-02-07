<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\SalesChannels\Model;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\SalesChannels\Model\Logging\ChannelManagerLoggerInterface;
use Magento\ServicesConnector\Api\ClientResolverInterface;
use Magento\ServicesConnector\Api\KeyValidationInterface;
use Magento\ServicesConnector\Exception\KeyNotFoundException;
use Magento\ServicesConnector\Exception\PrivateKeySignException;
use Psr\Http\Message\ResponseInterface;

class ServiceClient implements ServiceClientInterface
{
    /**
     * Extension name for Services Connector
     */
    private const EXTENSION_NAME = 'Magento_SalesChannels';
    private const FORBIDDEN = 'FORBIDDEN';
    private const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';

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
     * @var ChannelManagerLoggerInterface
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
     * @param ChannelManagerLoggerInterface $logger
     */
    public function __construct(
        ClientResolverInterface $clientResolver,
        KeyValidationInterface $keyValidator,
        Config $config,
        Json $serializer,
        ChannelManagerLoggerInterface $logger
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
            $options = $this->buildOptions($headers, $data);
            if ($this->isApiKeyValid()) {
                $response = $client->request($httpMethod, $path, $options);
                $isSuccessful = in_array($response->getStatusCode(), $this->successfulResponseCodes, true);
                $result = [
                    'is_successful' => $isSuccessful,
                    'status' => $response->getStatusCode()
                ];
                if ($isSuccessful) {
                    if ($requestContentType === 'json') {
                        try {
                            $result = array_merge(
                                $result,
                                $this->serializer->unserialize($response->getBody()->getContents())
                            );
                        } catch (InvalidArgumentException $e) {
                            $result = [
                                'is_successful' => false,
                                'status' => 500
                            ];
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
                    $result = [
                        'status' => 500
                    ];
                    $this->logger->error(
                        'An error occurred.',
                        $this->requestMeta($path, $data, $options, $response),
                    );
                }
            } else {
                $result = [
                    'status' => 403,
                    'statusText' => self::FORBIDDEN,
                    'message' => 'Magento API Key is invalid'
                ];
                $this->logger->error('API Key Validation failed.');
            }
        }
        catch (KeyNotFoundException $e) {
            $result = [
                'status' => 403,
                'statusText' => self::FORBIDDEN,
                'message' => 'Magento API Key not found'
            ];
            $this->logger->error('API Key Validation failed.', [
                'exception' => $e->getMessage()
            ]);
        } catch (GuzzleException | InvalidArgumentException | PrivateKeySignException $e) {
            $result = [
                'status' => 500,
                'statusText' => self::INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred'
            ];
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

    private function buildOptions(array $headers, string $data): array {
        return [
            'headers' => array_merge(
                $headers,
                [
                    'x-saas-id' => $this->config->getServicesEnvironmentId()
                ]
            ),
            'body' => $data
        ];
    }
}
