<?php

namespace Magento\OrderIngestion\Block\Adminhtml\Order;

use Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface;
use Magento\OrderIngestion\Service\GraphQlService;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ConfigInterface;
use Magento\Sales\Model\Order;

class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    /**
     * @var GraphQlService
     */
    private $graphQlService;

    /**
     * @var ExternalOrderRepositoryInterface
     */
    private $externalOrderRepository;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConfigInterface $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        ConfigInterface $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        GraphQlService $graphQlService,
        ExternalOrderRepositoryInterface $externalOrderRepository,
        array $data = []
    ) {
        $this->graphQlService = $graphQlService;
        $this->_reorderHelper = $reorderHelper;
        $this->_coreRegistry = $registry;
        $this->_salesConfig = $salesConfig;
        $this->externalOrderRepository = $externalOrderRepository;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        if ($this->isExternalOrder($this->getOrder())) {
            $this->updateButton(
                'order_cancel',
                'id',
                'cancel-external-order',
            );
        }
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->isExternalOrder($this->getOrder())) {
            $this->addChild(
                'cancellation_reasons',
                \Magento\OrderIngestion\Block\Adminhtml\Order\CancelReasons::class,
                [
                    'cancellation_reasons' => $this->getCancellationReasons(),
                    'cancellation_url' => $this->getUrl('orderingestion/order/cancel')
                ]);
        }
    }

    protected function _toHtml()
    {
        $cancellationReasonsBlock = $this->getChildBlock('cancellation_reasons');
        return parent::_toHtml() . ($cancellationReasonsBlock ? $cancellationReasonsBlock->toHtml() : '');
    }

    private function getCancellationReasons(): array{
        return $this->graphQlService->getCancellationReasons();
    }

    private function isExternalOrder(OrderInterface $order): bool{
        $poNumber = $order->getPayment()->getPoNumber();
        if (null === $poNumber) {
            return false;
        }
        return $this->externalOrderRepository->getByExternalOrderId($poNumber) !== null;
    }
}
