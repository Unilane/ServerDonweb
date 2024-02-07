<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\OrderIngestion\Controller\Adminhtml\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page;

/**
 * Class Index
 */
class Cancel extends \Magento\Sales\Controller\Adminhtml\Order
{
    const ADMIN_RESOURCE = 'Magento_Sales::cancel';

    /**
     * @return ResponseInterface|ResultInterface|Page
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $cancelReasonCode = $this->getRequest()->getParam('cancellation_reason');
        $cancelReasonText = $this->getRequest()->getParam('cancellation_label');
        if (null === $cancelReasonCode) {
            $this->messageManager->addErrorMessage(__("Cancellation reason is a mandatory parameter"));
            return $this->resultRedirectFactory->create()->setPath("sales/*/view", ["order_id" => $orderId]);

        }
        $order = $this->orderRepository->get($orderId);
        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        $additionalInformation['cancel_reason_code'] = $cancelReasonCode;
        $additionalInformation['cancel_reason_text'] = $cancelReasonText;
        $order->getPayment()->setAdditionalInformation($additionalInformation);
        $this->orderRepository->save($order);
        $this->orderManagement->cancel($order->getEntityId());
        $this->messageManager->addSuccessMessage(__('You canceled the order.'));

        return $this->resultRedirectFactory->create()->setPath('sales/*/view', ["order_id" => $orderId]);
    }
}
