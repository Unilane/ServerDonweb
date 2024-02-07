<?php

namespace Madkting\Order;

use Madkting\AbstractService;
use Madkting\Request;

class OrderService extends AbstractService
{
    private function _validate_pks($params)
    {
        if (!isset($params['shop_pk'])) {
            throw new \InvalidArgumentException('shop_pk must be defined');
        }
        if (!isset($params['marketplace_pk'])) {
            throw new \InvalidArgumentException('marketplace_pk must be defined');
        }
    }

    public function search($params = null)
    {
        $this->_validate_pks($params);
        $request = new Request($this->credentials);
        $uri = $this->getEndpoint('collection_default');
        $response = $request->get($uri, $params);
        return json_decode($response->getBody()->getContents());
    }

    public function get($params)
    {
        $this->_validate_pks($params);
        if (!isset($params['order_pk'])) {
            throw new \InvalidArgumentException('shop_pk must be defined');
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

    /**
     *
     * @param int $shop_pk
     * @param int $marketplace_pk
     * @param string $order_pk
     * @param array $params array(
     *      'status' => string required
     * )
     */
    public function setStatus($shop_pk, $marketplace_pk, $order_pk, $params)
    {
        $request = new Request($this->credentials);

        $action = $params['status'];
        if (in_array($action, array('canceled', 'canceled_feedback'))) {
            $uri = $this->getEndpoint('feedback');
            $response = $request->post($uri, array(
                'shop_pk' => $shop_pk,
                'marketplace_pk' => $marketplace_pk,
                'order_pk' => $order_pk
            ), array(
                'message' => $params['message'],
                'fulfilled' => $params['fulfilled'],
                'rating' => $params['rating'],
                'reason' => $params['reason'],
                'restock_item' => $params['restock_item']));
        } else {
            $uri = $this->getEndpoint($action);
            unset($params['status']);
            $response = $request->post($uri, array(
                'shop_pk' => $shop_pk,
                'marketplace_pk' => $marketplace_pk,
                'order_pk' => $order_pk
            ), $params);
        }
        return $response;
    }

    public function getUrlShippingLabel($shop_pk, $marketplace_pk, $order_pk, $format = 'pdf')
    {
        $request = new Request($this->credentials);
        $request->setFollowRedirects(false);
        $request->addHeaders(array('Accept' => 'application/' . $format));
        $uri = $this->getEndpoint('get_shipping_label');
        $response = $request->post($uri, array(
            'shop_pk' => $shop_pk,
            'marketplace_pk' => $marketplace_pk,
            'order_pk' => $order_pk,
        ));
        $location = $response->getHeader('Location')[0];
        return $location;
    }

    private function _progressDict($progress)
    {
        $status = array();
        foreach ($progress as $p) {
            $status[$p->name] = $p->status;
        }
        return $status;
    }

    public function getStatus($order)
    {
        $status = 'payment_required';
        if (is_object($order)) {
            if ($order->progress) {
                $progress = $this->_progressDict($order->progress);
                if (isset($progress['paid']) && $progress['paid'] == 'done') {
                    $status = 'paid';
                }
                if (isset($progress['ready_to_ship']) && $progress['ready_to_ship'] == 'done') {
                    $status = 'pending';
                }
                if (isset($progress['shipped']) && $progress['shipped'] == 'done') {
                    $status = 'shipped';
                }
                if (isset($progress['delivered']) && $progress['delivered'] == 'done') {
                    $status = 'delivered';
                }
                if (isset($progress['canceled']) && $progress['canceled'] == 'done') {
                    $status = 'canceled';
                }
                if (isset($progress['failed']) && $progress['failed'] == 'done') {
                    $status = 'failed_delivery';
                }
                if (isset($progress['refunded']) && $progress['refunded'] == 'done') {
                    $status = 'refunded';
                }
            }
        } else {
            throw new \Exception('Order must be a object');
        }
        return $status;
    }

    public function isPaid($order)
    {
        if (is_object($order)) {
            if ($order->progress) {
                $progress = $this->_progressDict($order->progress);
                if (isset($progress['paid']) && $progress['paid'] == 'done') {
                    return true;
                }
            }
        } else {
            throw new \Exception('Order must be a object');
        }
        return false;
    }
}