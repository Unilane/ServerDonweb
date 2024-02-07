<?php

namespace Madkting;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Default Request client implementation
 */
class Request
{
    private $credentials;
    private $headers = array('Accept' => 'application/json');
    private $followRedirects = true;
    private $guzzleClient;

    public function __construct(Credentials\Credentials $credentials)
    {
        $this->credentials = $credentials;
        $this->addHeaders(array('Authorization' => $credentials->getSecurityToken()));
        $this->guzzleClient = new Client();
    }

    public function setFollowRedirects($bool)
    {
        $this->followRedirects = (bool) $bool;
    }


    private function buildRequest($type, $uri, $params = null, $body = null): ResponseInterface
    {
        $uri = $this->_replace_params_uri($uri, $params);

        $options = [
            'headers' => $this->headers,
            'allow_redirects' => $this->followRedirects
        ];

        if (!empty($body)) {
            $options['json'] = $body;
        }

        return $this->guzzleClient->request($type, $uri, $options);
    }

    /**
     * @param $uri
     * @param $params
     * @return array|mixed|string|string[]
     */
    private function _replace_params_uri($uri, $params)
    {
        $query = array();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $uri_old = $uri;
                $uri = str_replace('{' . $key . '}', $value, $uri);
                if ($uri_old == $uri) {
                    $query[$key] = $value;
                }
            }
        }
        if (!empty($query)) {
            if (strpos($uri, '?') === false) {
                $uri .= '?' . http_build_query($query);
            } else {
                $uri .= '&' . http_build_query($query);
            }
        }
        return $uri;
    }

    public function addHeaders(array $headers): void
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    private function _check_error(ResponseInterface $response, $request = null): void
    {
        if ($response->getStatusCode() >= 400) {
            if ($response->getBody()) {
                $body = json_decode($response->getBody()->getContents());
                if (is_object($body)) {
                    throw new Exception\MadktingException(
                        'You have validations errors, show ´body´ of response for more details',
                        array(
                            'request' => $request,
                            'response' => $response,
                            'result' => $body
                        )
                    );
                } elseif (is_array($body)) {
                    throw new Exception\MadktingException(
                        'You have validations errors, show ´body´ of response for more details',
                        array(
                            'request' => $request,
                            'response' => $response,
                            'result' => $body
                        )
                    );
                } else {
                    throw new Exception\MadktingException(
                        $body,
                        array(
                            'request' => $request,
                            'response' => $response,
                            'result' => $body
                        )
                    );
                }
            } else {
                throw new \Exception('Unknown error');
            }
        }
    }

    /**
     * GET Verb
     * @param string $uri
     * @param array $params
     * @return ResponseInterface
     */
    public function get($uri, array $params = null)
    {
        $response = $this->buildRequest('GET', $uri, $params);
        $this->_check_error($response);
        return $response;
    }

    /**
     * POST Verb
     * @param string $uri
     * @param array $params
     * @param array $data
     * @return ResponseInterface
     */
    public function post($uri, array $params = null, $data = null)
    {
        $response = $this->buildRequest('POST', $uri, $params, $data);
        $this->_check_error($response);
        return $response;
    }


    /**
     * PUT Verb
     * @param string $uri
     * @param array $params
     * @param array $data
     * @return ResponseInterface
     */
    public function put($uri, array $params = null, $data = null)
    {
        $response = $this->buildRequest('PUT', $uri, $params, $data);
        $this->_check_error($response);
        return $response;
    }


    /**
     * DELETE Verb
     * @param string $uri
     * @param array $params
     * @param array $data
     * @return ResponseInterface
     */
    public function delete($uri, array $params = null, $data = null)
    {
        $response = $this->buildRequest('DELETE', $uri, $params, $data);
        $this->_check_error($response);
        return $response;
    }
}