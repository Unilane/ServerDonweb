<?php

namespace Madkting\Hook;

use Madkting\AbstractService;

class HookService extends AbstractService
{
    public function search($params = null)
    {
        throw new \BadMethodCallException('This method is not valid');
    }

    public function get($params)
    {
        throw new \BadMethodCallException('This method is not valid');
    }

    public function post($params)
    {
        throw new \BadMethodCallException('This method is not valid');
    }

    public function put($params)
    {
        throw new \BadMethodCallException('This method is not valid');
    }

    public function delete($params)
    {
        throw new \BadMethodCallException('This method is not valid');
    }

    /**
     * Check if the current request has the specific headers
     * defined by madkting
     * http://api.software.madkting.com/doc
     * - User-Agent: mad-hookshot/random$
     * - x-madkting-event: nombre del evento$
     * - x-madkting-signature: token secreto para los webhooks$
     * - x-madkting-delivery: id del hook base64 random
     * - Location
     * @return array with hook type, location and body
     */
    public function detect()
    {
        $headers = $this->_getallheaders();
        $headers_lower = array();
        foreach ($headers as $key => $value) {
            $headers_lower[strtolower($key)] = $value;
        }
        if (!isset($headers_lower['user-agent']) || strpos($headers_lower['user-agent'], 'mad-hookshot') !== 0) {
            throw new \Exception('No hook detected ("user-agent")');
        }
        if (!isset($headers_lower['x-madkting-event']) || empty($headers_lower['x-madkting-event'])) {
            throw new \Exception('No hook detected ("x-madkting-event")');
        }
        if (!isset($headers_lower['x-madkting-signature']) || empty($headers_lower['x-madkting-signature'])) {
            throw new \Exception('No hook detected ("x-madkting-signature")');
        }
        if (!isset($headers_lower['x-madkting-delivery']) || empty($headers_lower['x-madkting-delivery'])) {
            throw new \Exception('No hook detected ("x-madkting-delivery")');
        }
        $body = file_get_contents('php://input');
        if ($body) {
            $cont_type = $headers_lower['content-type'];
            if ($cont_type == 'application/json') {
                $body = json_decode($body);
            }
        }
        $data = array(
            'event' => $headers_lower['x-madkting-event'],
            'location' => isset($headers_lower['location']) ? $headers_lower['location'] : null,
            'body' => !empty($body) ? $body : null
        );
        return $data;
    }

    /**
     * Source - https://github.com/ralouphie/getallheaders
     * PHP getallheaders() polyfill. Compatible with PHP >= 5.3.
     */
    private function _getallheaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        /**
         * Get all HTTP header key/values as an associative array for the current request.
         *
         * @return string[string] The HTTP header key/value pairs.
         */
        $headers = array();
        $copy_server = array(
            'CONTENT_TYPE' => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5' => 'Content-Md5',
        );
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }
        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        return $headers;
    }
}