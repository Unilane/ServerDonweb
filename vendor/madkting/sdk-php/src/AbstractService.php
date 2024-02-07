<?php

namespace Madkting;

/**
 * Default AWS client implementation
 */
abstract class AbstractService
{
    protected $uri;
    protected $credentials;
    protected $config;

    public function __construct($uri, Credentials\Credentials $credentials, $config)
    {
        $this->uri = $uri;
        $this->credentials = $credentials;
        $this->config = $config;
    }

    /**
     *
     * @param string $endpoint [collection_default, individual_default, ...]
     * @return string
     */
    public function getEndpoint($endpoint = 'collection_default')
    {
        $uri = $this->config['endpoints'][$endpoint];
        return str_replace('{host}', $this->uri, $uri);
    }

    abstract public function search($params = null);

    abstract public function get($params);

    abstract public function post($params);

    abstract public function put($params);

    abstract public function delete($params);
}