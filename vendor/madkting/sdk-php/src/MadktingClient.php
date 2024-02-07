<?php

namespace Madkting;

use Madkting\Credentials\Credentials;
use Madkting\Shop\ShopService;
use Madkting\Request;

/**
 * Default AWS client implementation
 */
class MadktingClient
{
    /** @var array */
    private $args;

    /** @var string */
    private $url = 'https://api.software.madkting.com';

    /** @var Credentials * */
    private $credentials;

    public function __construct(array $args)
    {
        $this->_set_credentials($args);
        $this->args = $args;
        if (isset($args['url']) or !empty($args['url'])) {
            $this->url = $args['url'];
        }
    }

    private function _set_credentials(array $args)
    {
        if (!isset($args['token']) or empty($args['token'])) {
            throw new \InvalidArgumentException('No token defined');
        }
        $this->credentials = new Credentials($args['token']);
    }

    public function __call($name, array $args)
    {
        $args = isset($args[0]) ? $args[0] : array();
        if (strpos($name, 'service') === 0) {
            return $this->_createService(substr($name, 7), $args);
        }

        throw new \BadMethodCallException("Unknown method: {$name}.");
    }

    public function _createService($name, array $args = array())
    {
        // Get information about the service from the manifest file.
        $manifest = manifest($name);
        $namespace = $manifest['namespace'];
        // // Instantiate the client class.
        $service = "Madkting\\{$namespace}\\{$name}Service";
        return new $service($this->url, $this->credentials, $this->mergeArgs($namespace, $manifest, $args));
    }

    private function mergeArgs($namespace, array $manifest, array $args = array())
    {
        // Merge provided args with stored, service-specific args.
        if (isset($this->args[$namespace])) {
            $args += $this->args[$namespace];
        }

        return $manifest + $args + $this->args;
    }

    public function exec($uri)
    {
        $request = new Request($this->credentials);
        $response = $request->get($uri);
        return json_decode($response->getBody()->getContents());
    }


    public function testToken()
    {
        $service = $this->serviceShop();
        $list = $service->search(array('page_size' => 1));
        return array('name' => "Validated account");
    }

    public static function getOrderStatusDictionary()
    {
        return statuses('order');
    }

    public static function getProductFieldsDictionary()
    {
        return fields();
    }

    public static function getMarketplacesList()
    {
        return marketplaces();
    }

    public static function getCategoriesList()
    {
        return categories();
    }

    public static function reloadStaticData()
    {
        reload_static_data();
    }
}