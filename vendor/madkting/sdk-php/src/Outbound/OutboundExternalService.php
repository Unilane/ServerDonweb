<?php

namespace Madkting\Outbound;

use Madkting\AbstractService;
use Madkting\Request;

class OutboundExternalService extends AbstractService
{
    /**
     * Summary of search
     * @param mixed $params
     * @return mixed
     */
    public function search($params = null)
    {
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('collection_default');
        $response = $request->get($uri, $params);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * Summary of get
     * @param mixed $params
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function get($params)
    {
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('individual_default');
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk key not found');
        }
        if (!isset($params['product_pk'])) {
            throw new \InvalidArgumentException('product_pk key not found');
        }
        $response = $request->get($uri, $params);
        return json_decode($response->getBody()->getContents());
    }


    /**
     * Summary of post
     * @param mixed $params
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function post($params)
    {
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk key not found');
        }
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('collection_default');
        $response = $request->post($uri, array(
            'shop_pk' => $params['shop_pk'],
            'marketplace_pk' => $params['marketplace_pk'],
            'order_pk' => $params['order_pk']
        ),
        $params['data']);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * Summary of put
     * @param mixed $params
     * @throws \InvalidArgumentException
     * @return string
     */
    public function put($params)
    {
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk key not found');
        }
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('collection_default');
        $response = $request->put($uri, array(
            'shop_pk' => $params['shop_pk'],
            'marketplace_pk' => $params['marketplace_pk'],
            'order_pk' => $params['order_pk']
        ), $params['data']);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * Summary of delete
     * @param mixed $params
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function delete($params)
    {
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk key not found');
        }
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('individual_default');
        $response = $request->delete($uri, array(
            'shop_pk' => $params['shop_pk'],
            'marketplace_pk' => $params['marketplace_pk'],
            'order_pk' => $params['order_pk']
        ));
        return json_decode($response->getBody()->getContents());
    }
}