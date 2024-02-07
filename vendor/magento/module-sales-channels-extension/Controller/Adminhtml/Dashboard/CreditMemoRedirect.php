<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\CreditmemoRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\SalesChannels\Controller\Adminhtml\AbstractProxyController;
use Magento\SalesChannels\Model\Logging\ChannelManagerLoggerInterface;
use Magento\SalesChannels\Uuid\CreditMemoResource;

class CreditMemoRedirect extends AbstractProxyController
{
    const ADMIN_RESOURCE = 'Magento_SalesChannels::saleschannelsdashboard';

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CreditMemoResource
     */
    private $creditMemoResource;

    /**
     * @var CreditmemoRepository
     */
    private $creditmemoRepository;

    /**
     * @var ChannelManagerLoggerInterface $logger
     */
    private $logger;

    public function __construct(
        Context                       $context,
        RedirectFactory               $redirectFactory,
        OrderRepositoryInterface      $orderRepository,
        SearchCriteriaBuilder         $searchCriteriaBuilder,
        ChannelManagerLoggerInterface $logger,
        CreditMemoResource            $creditMemoResource
    )
    {
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->creditMemoResource = $creditMemoResource;
        $this->logger = $logger;
    }

    public function execute()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if (!empty($creditmemoId)) {
            $entityId = $this->creditMemoResource->getEntityId($creditmemoId);
            if ($entityId) {
                return $this->redirectFactory->create()->setPath('sales/order_creditmemo/view', ['creditmemo_id' => $entityId]);
            }
            $this->messageManager->addErrorMessage(sprintf('Could not found any credit memo with id %s', $entityId));
            return $this->redirectFactory->create()->setPath('sales/order');
        }

        $commerceOrderId = $this->getRequest()->getParam('commerce_order_id');
        $criteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::INCREMENT_ID, $commerceOrderId)->create();
        $orders = $this->orderRepository->getList($criteria)->getItems();

        if (empty($orders)) {
            $this->logger->error('The requested order does not exist.');
            $this->messageManager->addErrorMessage(sprintf('Could not found any order with id %s', $commerceOrderId));
            return $this->redirectFactory->create()->setPath('sales/order');
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = reset($orders);
            if (!$order->canCreditmemo()) {
                $this->logger->error(\Safe\sprintf('Can not create credit memo for order %s. Make sure order have invoice.', $commerceOrderId));

                $this->messageManager->addErrorMessage('This order has not been invoiced. To process the refund, generate the invoice, and then issue the credit memo.');
               return $this->redirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
            }

        return $this->redirectFactory->create()->setPath('sales/order_creditmemo/start', ['order_id' => $order->getEntityId()]);
    }
}
