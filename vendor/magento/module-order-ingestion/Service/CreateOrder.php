<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\OrderIngestion\Model\Carrier;
use Magento\OrderIngestion\Model\Dto\CreateOrderResult;
use Magento\OrderIngestion\Model\ExternalOrder;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Model\Payment\Method;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\RateFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Directory\Model\CurrencyFactory;

class CreateOrder
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RateFactory
     */
    private $shippingRateFactory;

    /**
     * @var OrderIngestionLoggerInterface
     */
    private $logger;

    /** @var CurrencyFactory */
    protected $currencyFactory;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param ProductRepositoryInterface $productRepository
     * @param QuoteFactory $quoteFactory
     * @param QuoteManagement $quoteManagement
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param RateFactory $shippingRateFactory
     * @param OrderIngestionLoggerInterface $logger
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(
        StoreRepositoryInterface      $storeRepository,
        ProductRepositoryInterface    $productRepository,
        QuoteFactory                  $quoteFactory,
        QuoteManagement               $quoteManagement,
        CustomerFactory               $customerFactory,
        CustomerRepositoryInterface   $customerRepository,
        RateFactory                   $shippingRateFactory,
        OrderIngestionLoggerInterface $logger,
        CurrencyFactory               $currencyFactory
    )
    {
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->quoteFactory = $quoteFactory;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->shippingRateFactory = $shippingRateFactory;
        $this->logger = $logger;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Safe\Exceptions\StringsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Safe\Exceptions\JsonException
     */
    public function fromExternalOrder(ExternalOrder $externalOrder): CreateOrderResult
    {
        $order = \Safe\json_decode($externalOrder->getOrderData());
        $this->logger->info(\Safe\sprintf("Loading store %s", $externalOrder->getStoreViewCode()));

        try {
            $store = $this->loadStore($externalOrder->getStoreViewCode());

            if ($store === null) {
                $error = \Safe\sprintf("Cannot find Store code view %s", $order->storeViewCode);
                $this->logger->error($error);
                return new CreateOrderResult(
                    $externalOrder->getOrderId(),
                    '',
                    CreateOrderResult::FAIL,
                    $error
                );
            }
            $websiteId = $store->getWebsiteId();
            $this->logger->info(\Safe\sprintf("Loaded store with code %s for website %s", $store->getCode(), $websiteId));

            $this->logger->info(\Safe\sprintf("Loading or creating customer %s", $order->customerEmail));
            $customer = $this->loadOrCreateCustomer((string)$websiteId, $order, $store);

            // @var \Magento\Quote\Model\Quote $quote
            $quote = $this->quoteFactory->create();
            $quote->setStore($store);

            if (strcmp($store->getBaseCurrencyCode(), $order->payment->currency) !== 0) {
                $error = \Safe\sprintf(
                    'Order currency %s does not match store currency %s',
                    $order->payment->currency ?? '',
                    $store->getBaseCurrencyCode()
                );
                $this->logger->error($error);
                return new CreateOrderResult(
                    $externalOrder->getOrderId(),
                    '',
                    CreateOrderResult::FAIL,
                    $error
                );
            }

            $this->logger->info(\Safe\sprintf("Using currency %s for order %s.", $store->getBaseCurrencyCode(), $externalOrder->getOrderId()));
            $quote->setCurrency();
            $this->logger->info(\Safe\sprintf("Assigned Currency %s to order %s.", $store->getBaseCurrencyCode(), $externalOrder->getOrderId()));
            $this->logger->info(\Safe\sprintf("Trying to assign customer %s to order %s.", $customer->getEmail(), $externalOrder->getOrderId()));
            $quote->assignCustomer($this->customerRepository->getById($customer->getEntityId()));
            $this->logger->info(\Safe\sprintf("Assigned Customer %s to order %s.", $customer->getEmail(), $externalOrder->getOrderId()));

        } catch (\Throwable $e) {
            $error = \Safe\sprintf("Cannot import Order %s from the sales channel. Error: %s",
                $externalOrder->getOrderId(),
                $e->getMessage());
            $this->logger->error($error);
            return new CreateOrderResult(
                $externalOrder->getOrderId(),
                '',
                CreateOrderResult::FAIL,
                "An error occurred during creation of the Commerce order."
            );
        }

        $itemsPaymentInfo = [];
        foreach ($order->items as $item) {
            try {
                $this->logger->info(\Safe\sprintf('Adding item %s to order %s', $item->sku, $externalOrder->getOrderId()));
                // @var \Magento\Catalog\Api\Data\ProductInterface $product
                $product = $this->productRepository->get($item->sku);
                $product->setPrice($item->itemPrice);
                $product->setFinalPrice($item->totalAmount);
                $product->setSeparateByItem(true);
                $quoteItem = $quote->addProduct($product, $item->qty);
                $product->setSeparateByItem(false);
                $quote->save();

                $itemsPaymentInfo[$quoteItem->getId()] = [
                    'qty' => $item->qty,
                    'itemPrice' => (float)$item->itemPrice,
                    'unitPrice' => (float )$item->unitPrice,
                    'baseDiscount' => (float)$item->discountAmount,
                    'taxAmount' => (float)$item->taxAmount,
                    'totalAmount' => (float)$item->totalAmount,
                ];
            } catch (\Throwable $e) {
                $this->logger->error(\Safe\sprintf('Order %s was not created due to an invalid sku %s. Error: %s',
                    $externalOrder->getOrderId(),
                    $item->sku,
                    $e->getMessage()));

                return new CreateOrderResult(
                    $externalOrder->getOrderId(),
                    '',
                    CreateOrderResult::FAIL,
                    \Safe\sprintf('Product with SKU %s not found or out of stock', $item->sku)
                );
            }
        }

        try {
            $quote->getBillingAddress()->addData($this->generateAddress($order->payment->billingAddress));
            $quote->getShippingAddress()->addData($this->generateAddress($order->shipping->shippingAddress));

            $shippingRate = $this->shippingRateFactory->create();
            $shippingRate->setCode(Carrier\Method::CHANNEL_SHIPPING);
            $shippingRate->setMethod(Carrier\Method::CHANNEL_SHIPPING);
            $shippingRate->setPrice($order->shipping->shippingAmount);
            $shippingRate->setCarrierTitle(Carrier\Method::CARRIER_TITLE);
            $shippingRate->setMethodTitle($order->shipping->shippingMethodName);

            $quote->getShippingAddress()->addShippingRate($shippingRate);
            $quote->getShippingAddress()->collectShippingRates();
            $quote->getShippingAddress()->setShippingMethod(Carrier\Method::CHANNEL_SHIPPING);

            $quote->setPaymentMethod(Method::PAYMENT_METHOD_OFFLINE_CHANNEL);
            $quote->setInventoryProcessed(false);
            $quote->save();
            $quote->getPayment()->importData(['method' => Method::PAYMENT_METHOD_OFFLINE_CHANNEL])
                ->setPoNumber($externalOrder->getExternalOrderId());
            $quote->collectTotals()->save();

            $this->calculateOrderCharges($store, $quote, $itemsPaymentInfo, $order);

            $quote = $this->quoteManagement->submit($quote);
        } catch (\Throwable $e) {
            $error = \Safe\sprintf("Cannot import Order %s from the sales channel. Error: %s",
                $externalOrder->getOrderId(), $e->getMessage());
            $this->logger->error($error);
            return new CreateOrderResult(
                $externalOrder->getOrderId(),
                '',
                CreateOrderResult::FAIL,
                "Cannot create order in Commerce order system."
            );
        }

        $this->logger->info(\Safe\sprintf('Created order in Commerce store with id %s.', $quote->getIncrementId()));
        return new CreateOrderResult(
            $externalOrder->getOrderId(),
            $quote->getIncrementId(),
            CreateOrderResult::SUCCESS,
            '',
            $quote->getId()
        );
    }

    public function convert(StoreInterface $store, $amount)
    {
        /** @var string */
        $currencyCodeFrom = $store->getBaseCurrencyCode();
        $currencyCodeTo = $store->getDefaultCurrencyCode();

        /** @var float */
        $toBaseRate = 1.0000;
        /** @var float */
        $fromBaseRate = 1.0000;

        if ($currencyCodeFrom === $currencyCodeTo) {
            return $amount;
        }

        $baseCurrency = $this->currencyFactory->create()->load($currencyCodeFrom);

        if ($rate = $baseCurrency->getAnyRate($currencyCodeFrom)) {
            $toBaseRate = $rate;
        }

        if ($rate = $this->currencyFactory->create()->load($currencyCodeFrom)->getAnyRate($currencyCodeTo)) {
            $fromBaseRate = $rate;
        }

        $amount = ($amount / $toBaseRate) * $fromBaseRate;
        return round((float)$amount, 2, PHP_ROUND_HALF_UP);
    }

    /**
     * @param \stdClass $address
     * @return array
     */
    private function generateAddress(\stdClass $address): array
    {
        return [
            'firstname' => $address->firstname ?? '',
            'lastname' => $address->lastname ?? '',
            'street' => $address->street ?? '',
            'city' => $address->city ?? '',
            'country_id' => $address->country ?? '',
            'region' => $address->region ?? '',
            'postcode' => $address->postcode ?? '',
            'telephone' => $address->phone ?? '',
            'save_in_address_book' => 1
        ];
    }

    private function loadStore(string $storeViewCode): ?StoreInterface
    {
        try {
            return $this->storeRepository->getActiveStoreByCode($storeViewCode);
        } catch (StoreIsInactiveException|NoSuchEntityException $e) {
            $this->logger->error(
                \Safe\sprintf(
                    'Cannot find store with id %d. Reason: %s',
                    $storeViewCode,
                    $e->getMessage()
                )
            );
        }
        return null;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Safe\Exceptions\StringsException
     */
    private function loadOrCreateCustomer(string $websiteId, \stdClass $order, StoreInterface $store): \Magento\Customer\Model\Customer
    {
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($order->customerEmail ?? '');
        if (!$customer->getEntityId()) {
            $this->logger->info(\Safe\sprintf('Cannot find customer account. Creating a new account with email %s.', $order->customerEmail ?? ''));
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($order->shipping->shippingAddress->firstname ?? '')
                ->setLastname($order->shipping->shippingAddress->lastname ?? '')
                ->setEmail($order->customerEmail ?? '')
                ->setPassword($order->customerEmail ?? '');
            $customer->save();
        } else {
            $this->logger->info(\Safe\sprintf('Found customer with email %s', $order->customerEmail));
        }
        return $customer;
    }

    /**
     * @param StoreInterface $store
     * @param Quote $quote
     * @param array $itemsPaymentInfo
     * @param \stdClass $order
     * @return void
     */
    public function calculateOrderCharges($store, $quote, $itemsPaymentInfo, $order): void
    {
        $totalBaseTax = 0.0;
        $totalTax = 0.0;
        $baseSubtotal = 0.0;
        $subtotal = 0.0;

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllItems() as $item) {
            if (!isset($itemsPaymentInfo[$item->getId()]) || !$itemsPaymentInfo[$item->getId()]) {
                continue;
            }
            $orderItemChargesInfo = $itemsPaymentInfo[$item->getId()];
            $baseItemPrice = $orderItemChargesInfo['itemPrice'];
            $itemPrice = (float)$this->convert($store, $baseItemPrice);
            $rowBaseTotal = $orderItemChargesInfo['totalAmount'];
            $rowTotal = (float)$this->convert($store, $rowBaseTotal);

            $baseSubtotal += $baseItemPrice * $orderItemChargesInfo['qty'];
            $subtotal += $itemPrice * $orderItemChargesInfo['qty'];

            $this->calculateItemDiscount($orderItemChargesInfo['baseDiscount'], $store, $rowTotal, $item);
            $this->calculateItemTax($orderItemChargesInfo, $store, $rowTotal, $item);
            $this->calculateItemRowCosts($item, $rowTotal, $rowBaseTotal);

            $totalBaseTax += $item->getBaseTaxAmount();
            $totalTax += $item->getTaxAmount();
        }

        if ($totalBaseTax) {
            $this->calculateTotals($quote, $order, $store, $baseSubtotal, $totalBaseTax, $subtotal, $totalTax);
        }
    }

    /**
     * @param float $baseDiscount
     * @param StoreInterface $store
     * @param float $rowTotal
     * @param Quote\Item $item
     * @return void
     */
    public function calculateItemDiscount($baseDiscount, $store, $rowTotal, $item): void
    {
        $discount = 0.0;

        if ($baseDiscount > 0) {
            $discount = (float)$this->convert($store, $baseDiscount);
            $discountPercent = round((float)($discount / $rowTotal), 2, PHP_ROUND_HALF_UP);
            $item->setDiscountPercent($discountPercent);
        }

        $item->setDiscountAmount($discount);
        $item->setBaseDiscountAmount($baseDiscount);
    }

    /**
     * @param array $orderItemChargesInfo
     * @param StoreInterface $store
     * @param float $rowTotal
     * @param Quote\Item $item
     * @return void
     */
    public function calculateItemTax($orderItemChargesInfo, $store, $rowTotal, $item): void
    {
        $baseTax = $orderItemChargesInfo['taxAmount'];

        if ($baseTax > 0) {
            $tax = (float)$this->convert($store, $baseTax);

            $taxPercent = round((float)($tax / ($rowTotal + $item->getDiscountAmount())) * 100, 2, PHP_ROUND_HALF_UP);
            $item->setTaxPercent($taxPercent);

            $itemBasePrice = $orderItemChargesInfo['itemPrice'];
            $itemPrice = (float)$this->convert($store, $itemBasePrice);

            $item->setBaseTaxAmount($baseTax);
            $item->setBasePrice($itemBasePrice);
            $item->setBasePriceInclTax($itemBasePrice + (float)($baseTax / $orderItemChargesInfo['qty']));

            $item->setTaxAmount($tax);
            $item->setPrice($itemPrice);
            $item->setPriceInclTax($itemPrice + (float)($tax / $orderItemChargesInfo['qty']));
        }
    }

    /**
     * @param Quote\Item $item
     * @param float $rowTotal
     * @param float $rowBaseTotal
     * @return void
     */
    public function calculateItemRowCosts($item, $rowTotal, $rowBaseTotal): void
    {
        $item->setRowTotalWithDiscount($rowTotal - $item->getTaxAmount());
        $item->setBaseRowTotal($rowBaseTotal + $item->getBaseDiscountAmount() - $item->getBaseTaxAmount());
        $item->setBaseRowTotalInclTax($rowBaseTotal + $item->getBaseDiscountAmount());
        $item->setRowTotal($rowTotal + $item->getDiscountAmount() - $item->getTaxAmount());
        $item->setRowTotalInclTax($rowTotal + $item->getDiscountAmount());
    }

    /**
     * @param Quote $quote
     * @param \stdClass $order
     * @param StoreInterface $store
     * @param float $baseSubtotal
     * @param float $totalBaseTax
     * @param float $subtotal
     * @param float $totalTax
     * @return void
     */
    public function calculateTotals($quote, $order, $store, $baseSubtotal, $totalBaseTax, $subtotal, $totalTax): void
    {
        $address = $quote->getShippingAddress();

        $baseShippingAmount = $order->shipping->shippingAmount;
        $baseShippingTax = $order->shipping->shippingTax;
        $shippingAmount = (float)$this->convert($store, $baseShippingAmount);
        $shippingTax = (float)$this->convert($store, $baseShippingTax);

        $address->setBaseSubtotalTotalInclTax($baseSubtotal + $totalBaseTax);
        $address->setBaseSubtotal($baseSubtotal);
        $address->setBaseTaxAmount($totalBaseTax + $baseShippingTax);
        $address->setBaseShippingAmount($baseShippingAmount - $baseShippingTax);
        $address->setBaseShippingInclTax($baseShippingAmount);
        $address->setBaseShippingTaxAmount($baseShippingTax);

        $address->setSubtotalInclTax($subtotal + $totalTax);
        $address->setSubtotal($subtotal);
        $address->setTaxAmount($totalTax + $shippingTax);
        $address->setShippingAmount($shippingAmount - $shippingTax);
        $address->setShippingInclTax($shippingAmount);
        $address->setShippingTaxAmount($shippingTax);

        $baseGrandTotal = $baseSubtotal + $totalBaseTax + $baseShippingAmount;
        $grandTotal = (float)$this->convert($store, $baseGrandTotal);

        $address->setBaseGrandTotal($baseGrandTotal);
        $address->setGrandTotal($grandTotal);

        $quote->setBaseSubtotal($baseSubtotal);
        $quote->setSubtotal($subtotal);
        $quote->setGrandTotal($grandTotal);
        $quote->setBaseGrandTotal($baseGrandTotal);
    }
}
