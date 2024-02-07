<?php
 /**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */
declare(strict_types=1);

namespace Magento\SalesChannels\Controller\Adminhtml;

use Magento\Backend\App\AbstractAction;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * Abstract controller
 */
abstract class AbstractProxyController extends AbstractAction implements
    CsrfAwareActionInterface,
    HttpGetActionInterface,
    HttpPostActionInterface
{
    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request) :? InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function _processUrlKeys()
    {
        $request = $this->getRequest();
        $isValid = true;
        if (!$this->_auth->isLoggedIn()) {
            $isValid = false;
        } elseif ($this->_backendUrl->useSecretKey()) {
            $isValid = $this->_validateSecretKey();
        }

        if (!$isValid && $request->getParam('isAjax')) {
            $request->setForwarded(true)
                ->setRouteName('adminhtml')
                ->setControllerName('auth')
                ->setActionName('deniedJson')
                ->setDispatched(false);
        } elseif (!$isValid) {
            $error = json_encode([
                'errors' => [
                    [
                        'message' => 'Authentication failed'
                    ]
                ]
            ]);
            $this->getResponse()->representJson($error);
        }
        return true;
    }
}
