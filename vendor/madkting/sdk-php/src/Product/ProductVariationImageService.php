<?php

namespace Madkting\Product;

use Madkting\AbstractService;
use Madkting\Request;

class ProductVariationImageService extends AbstractService
{
    public function search($params = null)
    {
        if (!isset($params['product_pk'])) {
            throw new \InvalidArgumentException('product_pk key not found');
        }
        if (!isset($params['variation_pk'])) {
            throw new \InvalidArgumentException('variation_pk key not found');
        }
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
        if (!isset($params['product_pk'])) {
            throw new \InvalidArgumentException('product_pk key not found');
        }
        if (!isset($params['variation_pk'])) {
            throw new \InvalidArgumentException('variation_pk key not found');
        }
        if (!isset($params['image_pk'])) {
            throw new \InvalidArgumentException('image_pk key not found');
        }
        $response = $request->get($uri, $params);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * Assign image in variation
     * Keys in $params
     * @param int $shop_pk
     * @param int $product_pk
     * @param int $variation_pk
     * @param list $images [{pk=>int}]
     * @return string - url feed location
     */
    public function post($params)
    {
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk key not found');
        }
        if (!isset($params['product_pk'])) {
            throw new \InvalidArgumentException('product_pk key not found');
        }
        if (!isset($params['variation_pk'])) {
            throw new \InvalidArgumentException('variation_pk key not found');
        }
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('collection_default');
        $response = $request->post($uri, array('shop_pk' => $params['shop_pk'], 'product_pk' => $params['product_pk'], 'variation_pk' => $params['variation_pk']), $params['images']);
        $location = $response->getHeader('Location')[0];
        return $location;
    }

    public function put($params)
    {
        throw new \InvalidArgumentException('Invalid Method');
    }

    /**
     *
     * @param list $params['images']
     *      [
     *          {"pk" => int},
     *          {"pk" => int}
     *      ]
     * @return string - url feed location
     */
    public function delete($params)
    {
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk key not found');
        }
        if (!isset($params['product_pk'])) {
            throw new \InvalidArgumentException('product_pk key not found');
        }
        if (!isset($params['variation_pk'])) {
            throw new \InvalidArgumentException('variation_pk key not found');
        }
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('collection_default');
        $response = $request->delete($uri, array('shop_pk' => $params['shop_pk'], 'product_pk' => $params['product_pk'], 'variation_pk' => $params['variation_pk']), $params['images']);
        $location = $response->getHeader('Location')[0];
        return $location;
    }
}