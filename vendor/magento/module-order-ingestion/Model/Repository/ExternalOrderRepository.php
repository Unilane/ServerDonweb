<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Model\Repository;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\OrderIngestion\Api\Data\ExternalOrderSearchResultsInterface;
use Magento\OrderIngestion\Api\Data\ExternalOrderSearchResultsInterfaceFactory;
use Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface;
use Magento\OrderIngestion\Model\ExternalOrderFactory;
use Magento\OrderIngestion\Model\ResourceModel\ExternalOrder;
use Magento\OrderIngestion\Model\ResourceModel\ExternalOrder\Collection as OrderCollection;
use Magento\OrderIngestion\Model\ResourceModel\ExternalOrder\CollectionFactory as ExternalOrderCollectionFactory;

class ExternalOrderRepository implements ExternalOrderRepositoryInterface
{
    /**
     * @var ExternalOrder
     */
    private $resource;

    /**
     * @var ExternalOrderFactory
     */
    private $externalOrderFactory;

    /**
     * @var ExternalOrderCollectionFactory
     */
    private $externalOrderCollectionFactory;

    /**
     * @var ExternalOrderSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /**
     * @param ExternalOrder $resource
     * @param ExternalOrderFactory $externalOrderFactory
     * @param ExternalOrderCollectionFactory $externalOrderCollectionFactory
     * @param ExternalOrderSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ExternalOrder                              $resource,
        ExternalOrderFactory                       $externalOrderFactory,
        ExternalOrderCollectionFactory             $externalOrderCollectionFactory,
        ExternalOrderSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface               $collectionProcessor
    )
    {
        $this->resource = $resource;
        $this->externalOrderFactory = $externalOrderFactory;
        $this->externalOrderCollectionFactory = $externalOrderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     *
     * @param ExternalOrderInterface $externalOrder
     * @return ExternalOrderInterface
     */
    public function save(ExternalOrderInterface $externalOrder): ExternalOrderInterface
    {
        $this->resource->save($externalOrder);
        return $externalOrder;
    }

    /**
     *
     * @param SearchCriteriaInterface $criteria
     * @return ExternalOrderSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): ExternalOrderSearchResultsInterface
    {
        /** @var OrderCollection $collection */
        $collection = $this->externalOrderCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var ExternalOrderSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrderId(string $orderId): ?ExternalOrderInterface
    {
        $order = $this->externalOrderFactory->create();
        $this->resource->load($order, $orderId, ExternalOrderInterface::ORDER_ID_COLUMN);
        if (!$order->getId()) {
            return null;
        }
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getByExternalOrderId(string $externalOrderId): ?ExternalOrderInterface
    {
        $order = $this->externalOrderFactory->create();
        $this->resource->load($order, $externalOrderId, ExternalOrderInterface::EXTERNAL_ORDER_ID_COLUMN);
        if (!$order->getId()) {
            return null;
        }
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function removeByOrderId($orderId)
    {
        $model = $this->get($orderId);
        $this->resource->delete($model);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($orderId)
    {
        $order = $this->externalOrderFactory->create();
        $this->resource->load($order, $orderId);
        if (!$order->getId()) {
            throw new NoSuchEntityException(
                __('The external order with the "%1" ID doesn\'t exist. Verify the ID and try again.', $orderId)
            );
        }
        return $order;
    }
}
