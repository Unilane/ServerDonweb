<?php
/**
 * Madkting Software (http://www.madkting.com)
 *
 *                                      ..-+::moossso`:.
 *                                    -``         ``ynnh+.
 *                                 .d                 -mmn.
 *     .od/hs..sd/hm.   .:mdhn:.   yo                 `hmn. on     mo omosnomsso oo  .:ndhm:.   .:odhs:.
 *    :hs.h.shhy.d.mh: :do.hd.oh:  /h                `+nm+  dm   ys`  ````mo```` hn :ds.hd.yo: :oh.hd.dh:
 *    ys`   `od`   `h+ sh`    `do  .d`              `snm/`  +s hd`        hd     yy yo`    `sd oh`    ```
 *    hh     sh     +m hs      yy   y-            `+mno`    dkdm          +d     o+ no      ss ys    dosd
 *    y+     ss     oh hdsomsmnmy   ++          .smh/`      om ss.        dh     mn yo      oh sm      hy
 *    sh     ho     ys hs``````yy   .s       .+hh+`         ys   hs.      os     yh os      d+ od+.  ./m/
 *    od     od     od od      od   +y    .+so:`            od     od     od     od od      od  `syssys`
 *                                 .ys .::-`
 *                                o.+`
 *
 * @category Module
 * @package Madkting\Connect
 * @author Carlos Guillermo Jiménez Salcedo <guillermo@madkting.com>
 * @author Israel Calderón Aguilar <israel@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Model\Sales;

use Madkting\Connect\Helper\Data;
use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\OrderActionsFactory;
use Madkting\Connect\Model\OrderStatusFactory;
use Madkting\Connect\Setup\UpgradeData;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Order
 * @package Madkting\Connect\Model\Sales
 */
class Order
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderAddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var OrderStatusFactory
     */
    protected $orderStatusFactory;

    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @var Data
     */
    protected $madktingHelper;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var CreditmemoService
     */
    protected $creditmemoService;

    /**
     * @var CreditmemoFactory
     */
    protected $creditmemoFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var Rate
     */
    protected $rate;

    /**
     * @var OrderActionsFactory
     */
    protected $orderActionsFactory;

    /**
     * @var ConvertOrder
     */
    protected $convertOrder;

    /**
     * @var TrackFactory
     */
    protected $track;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * Order constructor.
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderAddressRepositoryInterface $addressRepository
     * @param CartRepositoryInterface $cartRepository
     * @param CartManagementInterface $cartManagement
     * @param OrderStatusFactory $orderStatusFactory
     * @param Config $madktingConfig
     * @param Data $madktingHelper
     * @param InvoiceService $invoiceService
     * @param TransactionFactory $transactionFactory
     * @param Rate $rate
     * @param OrderActionsFactory $orderActionsFactory
     * @param ConvertOrder $convertOrder
     * @param TrackFactory $track
     * @param RegionFactory $regionFactory
     * @param MadktingLogger $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        OrderAddressRepositoryInterface $addressRepository,
        CartRepositoryInterface $cartRepository,
        CartManagementInterface $cartManagement,
        OrderStatusFactory $orderStatusFactory,
        Config $madktingConfig,
        Data $madktingHelper,
        InvoiceService $invoiceService,
        CreditmemoService $creditmemoService,
        CreditmemoFactory $creditmemoFactory,
        TransactionFactory $transactionFactory,
        Rate $rate,
        OrderActionsFactory $orderActionsFactory,
        ConvertOrder $convertOrder,
        TrackFactory $track,
        RegionFactory $regionFactory,
        MadktingLogger $logger
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->addressRepository = $addressRepository;
        $this->cartRepository = $cartRepository;
        $this->cartManagement = $cartManagement;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->madktingConfig = $madktingConfig;
        $this->madktingHelper = $madktingHelper;
        $this->invoiceService = $invoiceService;
        $this->creditmemoService = $creditmemoService;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->transactionFactory = $transactionFactory;
        $this->rate = $rate;
        $this->orderActionsFactory = $orderActionsFactory;
        $this->convertOrder = $convertOrder;
        $this->track = $track;
        $this->regionFactory = $regionFactory;
        $this->logger = $logger;
    }

    /**
     * @param object $data
     * @param string $status
     * @param bool $isPaid
     * @throws LocalizedException
     */
    public function execute($data, $status, $isPaid)
    {
        try {
            $search = $this->searchCriteriaBuilder->addFilter('madkting_pk', $data->pk)->create();
            $order = $this->orderRepository->getList($search)->getItems();
        } catch (\Exception $e) {
            throw new LocalizedException(__('Error searching order, %1', $e->getMessage()));
        }
   
        if (empty($order)) {
            $orderId = $this->createOrder($data, $status);

            if (!empty($orderId)) {
                $this->checkMadktingOrderStatus($orderId, $data, $status, $isPaid);
            }
        } else {
            $orderId = $this->updateOrder(array_values($order)[0], $data, $status);

            if (!empty($orderId)) {
                $this->checkMadktingOrderStatus($orderId, $data, $status, $isPaid);
            }
        }
    }

    function removeAccents($string)
    {
        $unwanted_array = array(
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u'
        );
        return strtr($string, $unwanted_array);
    }

    /**
     * @param object $data
     * @param string $status
     * @return int
     * @throws LocalizedException
     */
    protected function createOrder($data, $status)
    {
        /* Creation date */
        $createdAt = !empty($data->created_at) ? $data->created_at : null;
        $startCreationFrom = $this->madktingConfig->getStartCreationOrderDate();

        if (strtotime($createdAt) < strtotime($startCreationFrom)) {
            $message = __('Start date creation configuration is %1, order was placed at %2', $startCreationFrom, $createdAt);
            throw new LocalizedException($message);
        }

        try {
            $storeId = $this->madktingConfig->getSelectedStore();
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

            /* Create cart */
            $cartId = $this->cartManagement->createEmptyCart();

            /** @var \Magento\Quote\Model\Quote $cart */
            $cart = $this->cartRepository->get($cartId);
            $cart->setStoreId($storeId);

            /* Set customer information */
            $customerFirstName = !empty($data->customer->first_name) ? $data->customer->first_name : '-';
            $customerLastName = !empty($data->customer->last_name) ? $data->customer->last_name : '-';
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($data->customer->email);
            if (!empty($customer->getEntityId())) {
                $this->cartManagement->assignCustomer($cartId, $customer->getEntityId(), $storeId);
            } else {
                /* Get customer group ID */
                $groupSearch = $this->searchCriteriaBuilder->addFilter('customer_group_code', UpgradeData::MADKTING_NAME)->create();
                $customerGroup = $this->groupRepository->getList($groupSearch)->getItems()[0];
                $customerGroupId = !empty($customerGroup->getId()) ? $customerGroup->getId() : GroupInterface::NOT_LOGGED_IN_ID;

                $cart->setCustomerIsGuest(true);
                $cart->setCustomerGroupId($customerGroupId);
                $cart->setCustomerEmail($data->customer->email);
                $cart->setCustomerFirstname($customerFirstName);
                $cart->setCustomerLastname($customerLastName);
            }
            $cart->setCurrency();

            # Check fulfillment
            $fulfillmentEnabled = $this->madktingConfig->isFulfillmentOrdersEnabled();
            if (!$fulfillmentEnabled && $data->ff_type == 'fbc') {
                throw new LocalizedException(__('Creation of orders with fulfillment by channel is disabled.'));
            }

            /* Add items to cart */
            foreach ($data->items as $item) {
                # Check fulfillment
                if (!$fulfillmentEnabled && $item->ff_type == 'fbc') {
                    continue;
                }

                if (empty($item->price)) {
                    throw new LocalizedException(__('Price is empty'));
                } elseif (empty($item->quantity)) {
                    throw new LocalizedException(__('Quantity is empty'));
                }

                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->productRepository->get($item->sku, false, $storeId);
                try {
                    $quoteItem = $cart->addProduct($product, intval($item->quantity));
                    if ($quoteItem instanceof \Magento\Quote\Model\Quote\Item) {
                        $quoteItem->setOriginalCustomPrice($item->price)
                            ->setCustomPrice($item->price)
                            ->getProduct()->setIsSuperMode(true);
                    } else {
                        $errorQuote = $quoteItem;
                    }
                } catch (\Exception $e) {
                    $errorQuote = $e->getMessage();
                }

                if (!empty($errorQuote)) {
                    throw new LocalizedException(__('Error adding product %1 to quote, %2', $item->sku, $errorQuote));
                }
            }

            /* Get countries */
            $countries = $this->madktingHelper->getCountryList();

            /* Set shipping address to cart */
            if (!$this->madktingConfig->isNoShippingAddressOrdersEnabled() && empty($data->shipping_address->address) && empty($data->shipping_address->street_name)) {
                throw new LocalizedException(__('Creation of orders with no shipping address is disabled.'));
            }


            $country = !empty($data->shipping_address->country) ? array_search($this->removeAccents($data->shipping_address->country), $countries) : '';
            $country = !empty($country) ? $country : $this->madktingConfig->getStoreCountryId();
            $regionName = !empty($data->shipping_address->region) ? $data->shipping_address->region : '';
            $regionId = $this->regionFactory->create()->loadByName($regionName, $country)->getId();
            $requiredRegion = $this->madktingConfig->getRequiredRegionCountries();

            if (is_null($regionId) && in_array($country, $requiredRegion)) {
                $regionId = !empty($regionId) ? $regionId : $this->getRegionId($country,  $data->shipping_address->country);
            }
            $street = $this->madktingHelper->getStreetMerged($data->shipping_address);
            $firstname = !empty($data->shipping_address->first_name) ? $data->shipping_address->first_name : '';
            $lastname = !empty($data->shipping_address->last_name) ? $data->shipping_address->last_name : '';
            if (empty($firstname)) {
                $firstname = $customerFirstName;
                $lastname = $customerLastName;
            }
            $name = $this->madktingHelper->getProcessedName($firstname, $lastname);

            $shippingAddress = [
                'firstname' => !empty($name['firstname']) ? $name['firstname'] : '-',
                'lastname' => !empty($name['lastname']) ? $name['lastname'] : '-',
                'street' => !empty($street) ? $street : __('To define'),
                'city' => !empty($data->shipping_address->city) ? $data->shipping_address->city : '-',
                'country_id' => $country,
                'region_id' => $regionId,
                'region' => !empty($regionName) ? $regionName : '-',
                'postcode' => !empty($data->shipping_address->postal_code) ? $data->shipping_address->postal_code : '-',
                'telephone' => !empty($data->shipping_address->phone) ? $data->shipping_address->phone : '-',
                'email' => !empty($data->shipping_address->email) ? $data->shipping_address->email : ''
            ];
            $cart->getShippingAddress()->addData($shippingAddress);

            /* Set billing address to cart */
            $country = !empty($data->billing_address->country) ? array_search($this->removeAccents($data->billing_address->country), $countries) : '';
            $country = !empty($country) ? $country : $cart->getShippingAddress()->getCountryId();
            $regionName = !empty($data->billing_address->region) ? $data->billing_address->region : '';
            $regionId = $this->regionFactory->create()->loadByName($regionName, $country)->getId();
            $regionId = !empty($regionId) ? $regionId : $cart->getShippingAddress()->getRegionId();
            $street = $this->madktingHelper->getStreetMerged($data->billing_address);
            $firstname = !empty($data->billing_address->first_name) ? $data->billing_address->first_name : '';
            $lastname = !empty($data->billing_address->last_name) ? $data->billing_address->last_name : '';
            if (empty($firstname)) {
                $firstname = $customerFirstName;
                $lastname = $customerLastName;
            }
            $name = $this->madktingHelper->getProcessedName($firstname, $lastname);

            $billingAddress = [
                'firstname' => !empty($name['firstname']) ? $name['firstname'] : '-',
                'lastname' => !empty($name['lastname']) ? $name['lastname'] : '-',
                'street' => !empty($street) ? $street : __('To define'),
                'city' => !empty($data->billing_address->city) ? $data->billing_address->city : '-',
                'country_id' => $country,
                'region_id' => $regionId,
                'region' => !empty($regionName) ? $regionName : '-',
                'postcode' => !empty($data->billing_address->postal_code) ? $data->billing_address->postal_code : '-',
                'telephone' => !empty($data->billing_address->phone) ? $data->billing_address->phone : '-',
                'email' => !empty($data->billing_address->email) ? $data->billing_address->email : ''
            ];
            $cart->getBillingAddress()->addData($billingAddress);

            /* Set shipping */
            $shippingPrice = !empty($data->shipping_cost) ? $data->shipping_cost : '0';
            $this->rate
                ->setCode('madkting_madkting')
                ->setCarrier('madkting')
                ->setCarrierTitle('Madkting')
                ->setMethod('shipping')
                ->setMethodTitle('Shipping')
                ->setPrice($shippingPrice);
            $cart->getShippingAddress()
                ->setCollectShippingRates(false)
                ->setShippingMethod('madkting_madkting')
                ->addShippingRate($this->rate);

            /* Set payment method */
            $madktingPayment = !empty($data->payment_method) ? $data->payment_method : '';
            $cart->getPayment()->importData(['method' => 'madkting']);
            $cart->getPayment()->setAdditionalInformation([
                'madkting_payment' => $madktingPayment,
                'madkting_marketplace_pk' => $data->marketplace_pk
            ]);

            /* Set Madkting information*/
            $cart->setMadktingPk($data->pk);
            $cart->setMadktingMarketplaceReference($data->reference);
            $cart->setMadktingMarketplacePk($data->marketplace_pk);
            $cart->setMadktingShopPk($data->shop_pk);
            $cart->setMadktingStatus($status);
            $cart->setMadktingFulfillment($data->ff_type);

            if (in_array('mshops', $data->tags)) {
                $cart->setMadktingMercashop('Mercado Shops');
            }

            /* Save cart data */
            $cart->setIsSuperMode(true);
            $cart->setCreatedAt($createdAt);
            $this->cartRepository->save($cart);

            /* Create order */
            $orderId = $this->cartManagement->placeOrder($cartId);
            $order = $this->orderRepository->get($orderId)->setCreatedAt($createdAt);
            $this->orderRepository->save($order);

            return $orderId;
        } catch (\Exception $e) {
            throw new LocalizedException(__('Order creation error, %1', $e->getMessage()));
        }
    }

    /**
     * @param object $order
     * @param object $data
     * @param string $status
     * @return int
     * @throws LocalizedException
     */
    protected function updateOrder($order, $data, $status)
    {
        try {
            /* Get customer info */
            $customerFirstName = !empty($data->customer->first_name) ? $data->customer->first_name : '-';
            $customerLastName = !empty($data->customer->last_name) ? $data->customer->last_name : '-';
            $customerEmail = $data->customer->email;

            /* Get countries */
            $countries = $this->madktingHelper->getCountryList();

            /* Set shipping address to cart */
            $country = !empty($data->shipping_address->country) ? array_search($this->removeAccents($data->shipping_address->country), $countries) : '';
            $country = !empty($country) ? $country : $this->madktingConfig->getStoreCountryId();
            $regionName = !empty($data->shipping_address->region) ? $data->shipping_address->region : '';
            $regionId = $this->regionFactory->create()->loadByName($regionName, $country)->getId();
            $requiredRegion = $this->madktingConfig->getRequiredRegionCountries();

            if (is_null($regionId) && in_array($country, $requiredRegion)) {
                $regionId = !empty($regionId) ? $regionId : $this->getRegionId($country, $data->shipping_address->country);
            }
            $street = $this->madktingHelper->getStreetMerged($data->shipping_address);
            $firstname = !empty($data->shipping_address->first_name) ? $data->shipping_address->first_name : '';
            $lastname = !empty($data->shipping_address->last_name) ? $data->shipping_address->last_name : '';
            if (empty($firstname)) {
                $firstname = $customerFirstName;
                $lastname = $customerLastName;
            }
            $name = $this->madktingHelper->getProcessedName($firstname, $lastname);

            $shippingAddressData = [
                'firstname' => !empty($name['firstname']) ? $name['firstname'] : '-',
                'lastname' => !empty($name['lastname']) ? $name['lastname'] : '-',
                'street' => !empty($street) ? $street : __('To define'),
                'city' => !empty($data->shipping_address->city) ? $data->shipping_address->city : '-',
                'country_id' => $country,
                'region_id' => $regionId,
                'region' => !empty($regionName) ? $regionName : '-',
                'postcode' => !empty($data->shipping_address->postal_code) ? $data->shipping_address->postal_code : '-',
                'telephone' => !empty($data->shipping_address->phone) ? $data->shipping_address->phone : '-',
                'email' => !empty($data->shipping_address->email) ? $data->shipping_address->email : $customerEmail
            ];
            $shippingAddressId = $order->getShippingAddressId();
            $shippingAddress = $this->addressRepository->get($shippingAddressId);
            $shippingAddress->addData($shippingAddressData);
            $this->addressRepository->save($shippingAddress);

            /* Set billing address to cart */
            $country = !empty($data->billing_address->country) ? array_search($this->removeAccents($data->billing_address->country), $countries) : '';
            $country = !empty($country) ? $country : $shippingAddress->getCountryId();
            $regionName = !empty($data->billing_address->region) ? $data->billing_address->region : '';
            $regionId = $this->regionFactory->create()->loadByName($regionName, $country)->getId();
            $regionId = !empty($regionId) ? $regionId : $shippingAddress->getRegionId();
            $street = $this->madktingHelper->getStreetMerged($data->billing_address);
            $firstname = !empty($data->billing_address->first_name) ? $data->billing_address->first_name : '';
            $lastname = !empty($data->billing_address->last_name) ? $data->billing_address->last_name : '';
            if (empty($firstname)) {
                $firstname = $customerFirstName;
                $lastname = $customerLastName;
            }
            $name = $this->madktingHelper->getProcessedName($firstname, $lastname);

            $billingAddressData = [
                'firstname' => !empty($name['firstname']) ? $name['firstname'] : '-',
                'lastname' => !empty($name['lastname']) ? $name['lastname'] : '-',
                'street' => !empty($street) ? $street : __('To define'),
                'city' => !empty($data->billing_address->city) ? $data->billing_address->city : '-',
                'country_id' => $country,
                'region_id' => $regionId,
                'region' => !empty($regionName) ? $regionName : '-',
                'postcode' => !empty($data->billing_address->postal_code) ? $data->billing_address->postal_code : '-',
                'telephone' => !empty($data->billing_address->phone) ? $data->billing_address->phone : '-',
                'email' => !empty($data->billing_address->email) ? $data->billing_address->email : $customerEmail
            ];
            $billingAddressId = $order->getBillingAddressId();
            $billingAddress = $this->addressRepository->get($billingAddressId);
            $billingAddress->addData($billingAddressData);
            $this->addressRepository->save($billingAddress);

            return $order->getEntityId();
        } catch (\Exception $e) {
            throw new LocalizedException(__('Order updating error, %1', $e->getMessage()));
        }
    }

    /**
     * @param int $orderId
     * @param object $data
     * @param string $status
     * @param bool $isPaid
     */
    protected function checkMadktingOrderStatus($orderId, $data, $status, $isPaid)
    {
        /** @var \Madkting\Connect\Model\OrderStatus $orderStatus */
        $orderStatus = $this->orderStatusFactory->create();
        $orderStatusData = $orderStatus->loadByMadktingStatus($status)[0];
        $paidDocument = $orderStatus->loadByMadktingStatus('paid')[0]['create_document'];

        /* Get order */
        $order = $this->orderRepository->get($orderId);

        /* Create invoice if is paid */
        if (($status == 'paid' || $isPaid) && !empty($paidDocument)) {
            $this->createInvoice($order);
        }

        /* Create shipment if is shipped */
        if ($status == 'shipped' && !empty($orderStatusData['create_document'])) {
            $this->createShipment($order, $data->items);
        }

        /* Create credit memo if is refunded or canceled & create document is active */
        if (($status == 'refunded' || $status == 'canceled') && !empty($orderStatusData['create_document'])) {
            $this->createCreditMemo($order);
        }

        /* Update order actions */
        if (!empty($data->actions)) {
            foreach ((array) $data->actions as $action) {
                if ($action == 'print_delivery_label' || $action == 'set_ready_to_ship') {

                    /** @var \Madkting\Connect\Model\OrderActions $orderActions */
                    $orderActions = $this->orderActionsFactory->create();
                    $actionsArray = $orderActions->loadByOrderPk($data->pk, $action);
                    if (empty($actionsArray)) {
                        $orderActions->setMadktingPk($data->pk);
                        $orderActions->setAction($action);
                        $orderActions->setDone(0);
                        $orderActions->save();
                    }
                }
            }
        }

        /* Update Madkting and Magento status */
        $order->setMadktingStatus($status);
        if (!empty($orderStatusData['status_magento'])) {
            $order->setStatus($orderStatusData['status_magento']);
        }
        $this->orderRepository->save($order);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    protected function createInvoice($order)
    {
        /* Create invoice */
        if ($order->canInvoice()) {
            try{
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
                $invoice->register();
                $transaction = $this->transactionFactory->create();
                $transaction->addObject($invoice)->addObject($invoice->getOrder());
                $transaction->save();
                $order->addStatusHistoryComment(__('Invoice #%1 created.', $invoice->getIncrementId()));
                $order->setIsCustomerNotified(false);
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $order->addStatusHistoryComment(__('Could not create invoice'));

                $pk = $order->getMadktingPk();
                $reference = $order->getMadktingMarketplaceReference();
                $title = __('Invoice Creation Error %1(%2)', $reference, $pk);
                $this->logger->debug($e->getMessage(), [
                    'title' => $title
                ]);
            }
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param object $items
     */
    protected function createShipment($order, $items)
    {
        /* Create invoice */
        if ($order->canShip()) {
            try{
                $shipment = $this->convertOrder->toShipment($order);

                /* Set data for each order item */
                foreach ($order->getItems() as $item) {
                    /* Check if order item has qty to ship or is virtual */
                    if(!$item->getQtyToShip() || $item->getIsVirtual()) {
                        continue;
                    }

                    /* Crete shipment item */
                    $shipmentItem = $this->convertOrder->itemToShipmentItem($item)->setQty($item->getQtyToShip());
                    $shipment->addItem($shipmentItem);

                    /* Add tracking number */
                    $trackingNumbers = [];
                    foreach ($items as $madktingItem) {
                        if (!empty($trackingNumber = $madktingItem->tracking_code)) {
                            if (!in_array($trackingNumber, $trackingNumbers)) {
                                $trackingNumbers[] = $trackingNumber;

                                /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
                                $track = $this->track->create()
                                    ->setCarrierCode('custom')
                                    ->setTitle($madktingItem->carrier)
                                    ->setTrackNumber($trackingNumber);
                                $shipment->addTrack($track);
                            }
                        }
                    }
                }

                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);
                $shipment->save();
                $order->addStatusHistoryComment(__('Shipment #%1 created.', $shipment->getIncrementId()));
                $order->setIsCustomerNotified(false);
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $order->addStatusHistoryComment(__('Could not create shipment'));

                $pk = $order->getMadktingPk();
                $reference = $order->getMadktingMarketplaceReference();
                $title = __('Shipment Creation Error %1(%2)', $reference, $pk);
                $this->logger->debug($e->getMessage(), [
                    'title' => $title
                ]);
            }
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    protected function createCreditMemo($order)
    {
        $creditMemo = $this->creditmemoFactory->createByOrder($order);

        if ($creditMemo->canRefund()) {
            try {
                $this->creditmemoService->refund($creditMemo);
                $order->addStatusHistoryComment(__('Credit memo #%1 created.', $creditMemo->getIncrementId()));
                $order->setIsCustomerNotified(false);
                $this->orderRepository->save($order);
            } catch (\Exception $e) {
                $order->addStatusHistoryComment(__('Could not create the credit memo'));

                $pk = $order->getMadktingPk();
                $reference = $order->getMadktingMarketplaceReference();
                $title = __('Credit Memo Creation Error %1(%2)', $reference, $pk);
                $this->logger->debug($e->getMessage(), [
                    'title' => $title
                ]);
            }
        }
    }

    /**
     * Get region ID from different country
     *
     * @param string $country
     * @return string
     */
    protected function getRegionId($countryId, $country )
    {
        /** @var \Magento\Directory\Model\Region $region */
        $region = $this->regionFactory->create()
            ->getCollection()
            ->addFieldToFilter('country_id', ['eq' => $countryId])
            ->addFieldToFilter('default_name', ['like' => '%' . $country . '%'])
            ->setPageSize(1)
            ->getFirstItem();

        if ($region->getId()) {
            return $region->getId();
        } else {
            return '1';
        }
    }
}
