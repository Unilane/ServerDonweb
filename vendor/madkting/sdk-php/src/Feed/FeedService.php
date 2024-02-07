<?php

namespace Madkting\Feed;

use Madkting\AbstractService;
use Madkting\Request;

class FeedService extends AbstractService
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
        if (!isset($params['feed_pk'])) {
            throw new \InvalidArgumentException('feed_pk must be defined');
        }
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('individual_default');
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