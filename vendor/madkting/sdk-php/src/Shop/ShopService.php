<?php

namespace Madkting\Shop;

use Madkting\AbstractService;
use Madkting\Request;

class ShopService extends AbstractService
{
    public function search($params = null)
    {
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('collection_default');
        $response = $request->get($uri, $params);
        return json_decode($response->getBody()->getContents());
    }

    public function get($params)
    {
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('individual_default');
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk key not found');
        }
        $response = $request->get($uri, $params);
        return json_decode($response->getBody()->getContents());
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
}