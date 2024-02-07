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
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\SalesChannels\Controller\Adminhtml\AbstractProxyController;

class OrderRedirect extends AbstractProxyController
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

    public function __construct(
        Context $context,
        RedirectFactory $redirectFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

    }

    public function execute()
    {
        $commerceOrderId = $this->getRequest()->getParam('commerce_order_id');
        $criteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::INCREMENT_ID, $commerceOrderId)->create();
        $orders = $this->orderRepository->getList($criteria)->getItems();
        if (empty($orders)) {
            throw new NotFoundException(__('The requested order does not exist.'));
        }
        $order = reset($orders);
        return $this->redirectFactory->create()->setPath('sales/order/view', ['order_id' => $order->getEntityId()]);
    }
}
