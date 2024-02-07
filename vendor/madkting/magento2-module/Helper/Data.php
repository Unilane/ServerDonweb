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
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Helper;

use Madkting\Connect\Model\AttributeFactory;
use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\OrderActionsFactory;
use Madkting\Connect\Model\OrderStatusFactory;
use Madkting\MadktingClient;
use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Bundle\RegionBundle;
use Magento\Framework\Locale\ListsInterface;
use Magento\Widget\Model\Template\Filter;

/**
 * Class Data
 * @package Madkting\Connect\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var array
     */
    protected $paymentMethods = [
        'Amex_Gateway' => 'American Express',
        'Banorte_PagoReferenciado' => 'Referenced Payment',
        'Banorte_Payworks' => 'Credit Card',
        'Banorte_Payworks_Debit' => 'Debit Card',
        'CashOnDelivery_Payment' => 'Cash on Delivey Payment',
        'Oxxo_Direct' => 'Oxxo Direct',
        'Paypal_Express_Checkout' => 'PayPal',
        'CreditCardOnDelivery_Payment' => 'Credit Card on Delivery Payment',
        'Club_Premier_Kmp_Payment' => 'Club Premier Kmp',
        'Club_Premier_Mixed_Payment' => 'Club Premier Mixed',
        'LOYALTY' => 'LOYALTY',
        'Banorte_Payworks_Lpay' => 'Banorte Payworks Lpay',
        'Zero_Payment' => 'Zero Payment',
        'ticket' => 'Convenience Store',
        'atm' => 'Payment by ATM',
        'credit_card' => 'Credit Card',
        'debit_card' => 'Debit Card',
        'prepaid_card' => 'Prepaid Card',
        'account_money' => 'Mercadopago'
    ];

    /**
     * Always shown fields, even if requirement is optional or recommended
     *
     * @var array
     */
    protected $alwaysShown = [
        'template_html'
    ];

    /**
     * Fields that do not need to be validated in configurable products
     *
     * @var array
     */
    protected $noValidation = [
        'stock',
        'price'
    ];

    /**
     * Fields allowed to have empty value
     *
     * @var array
     */
    protected $emptyValueAllowed = [
        'template_html'
    ];

    /**
     * Fields that have to be cleared
     *
     * @var array
     */
    protected $clearAttributes = [
        'name',
        'description',
        'description_html',
        'short_description',
        'characteristics'
    ];

    /**
     * Fields allowed to have HTML
     *
     * @var array
     */
    protected $htmlAllowed = [
        'description_html'
    ];

    /**
     * Madkting Shops
     *
     * @var array
     */
    protected $madktingShops = [];

    /**
     * Madkting Marketplaces
     *
     * @var array
     */
    protected $madktingMarketplaces = [];

    /**
     * Order status mapping statuses
     *
     * @var array
     */
    protected $statuses;

    /**
     * Order status mapping documents
     *
     * @var array
     */
    protected $documents;

    /**
     * @var OrderActionsFactory
     */
    protected $orderActionsFactory;

    /**
     * @var OrderStatusFactory
     */
    protected $orderStatusFactory;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var CountryCollection
     */
    protected $countryCollection;

    /**
     * @var ListsInterface
     */
    protected $localeLists;

    /**
     * @var Filter
     */
    protected $templateProcessor;

    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param OrderActionsFactory $orderActionsFactory
     * @param OrderStatusFactory $orderStatusFactory
     * @param AttributeFactory $attributeFactory
     * @param CountryCollection $countryCollection
     * @param ListsInterface $localeLists
     * @param Filter $templateProcessor
     * @param Config $madktingConfig
     */
    public function __construct(
        Context $context,
        OrderActionsFactory $orderActionsFactory,
        OrderStatusFactory $orderStatusFactory,
        AttributeFactory $attributeFactory,
        CountryCollection $countryCollection,
        ListsInterface $localeLists,
        Filter $templateProcessor,
        Config $madktingConfig
    ) {
        parent::__construct($context);
        $this->orderActionsFactory = $orderActionsFactory;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->attributeFactory = $attributeFactory;
        $this->countryCollection = $countryCollection;
        $this->localeLists = $localeLists;
        $this->templateProcessor = $templateProcessor;
        $this->madktingConfig = $madktingConfig;
    }

    /**
     * @return array
     */
    public function getMadktingShops()
    {
        if (empty($this->madktingShops)) {
            $token = $this->madktingConfig->getMadktingToken();
            if ($token) {
                $client = new MadktingClient(['token' => $token]);
                $shopService = $client->serviceShop();
                $this->madktingShops = $shopService->search();
            }
        }

        return $this->madktingShops;
    }

    /**
     * @return array
     */
    public function getMarketplaces()
    {
        if (empty($this->madktingMarketplaces)) {
            foreach ($this->getMadktingShops() as $shop) {
                foreach ($shop->marketplaces as $channel) {
                    $this->madktingMarketplaces[$channel->pk] = $channel->name;
                }
            }
        }

        return $this->madktingMarketplaces;
    }

    /**
     * @param string $pk
     * @return string
     */
    public function getMarketplaceByPk($pk)
    {
        $marketplaces = $this->getMarketplaces();

        return array_key_exists($pk, $marketplaces) ? $marketplaces[$pk] : $pk;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getStatusByCode($code)
    {
        if (empty($this->statuses)) {
            $this->statuses = $this->orderStatusFactory->create()->getStatusLabels();
        }

        return array_key_exists($code, $this->statuses) ? __($this->statuses[$code]) : $code;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        if (empty($this->statuses)) {
            $this->statuses = $this->orderStatusFactory->create()->getStatusLabels();
        }

        $statuses = [];

        foreach ($this->statuses as $code => $status) {
            $statuses[$code] = __($status);
        }

        return $statuses;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getDocumentByCode($code)
    {
        if (empty($this->documents)) {
            $this->documents = $this->orderStatusFactory->create()->getDocumentLabels();
        }

        return array_key_exists($code, $this->documents) ? __($this->documents[$code]) : $code;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getPaymentMethodByCode($code)
    {
        return array_key_exists($code, $this->paymentMethods) ? __($this->paymentMethods[$code]) : $code;
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @return array
     */
    public function getProcessedName($firstname, $lastname)
    {
        $name = [
            'firstname' => preg_replace('/\s+/', ' ', trim($firstname)),
            'lastname' => preg_replace('/\s+/', ' ', trim($lastname))
        ];

        if (empty($name['lastname'])) {

            /* If there is a name try to get lastname */
            if (!empty($name['firstname'])) {
                $nameEx = explode(' ', $name['firstname'], 2);

                if (!empty($nameEx[0])) $name['firstname'] = $nameEx[0];
                if (!empty($nameEx[1])) $name['lastname'] = $nameEx[1];
            }
        }

        return $name;
    }

    /**
     * @param string $locale
     * @return array
     */
    public function getCountryList($locale = 'en_US')
    {
        $countries = [];

        $countriesId = $this->countryCollection->getItems();

        foreach ($countriesId as $key => $value) {
            $countries[$key] = $this->getCountryTranslation($key, $locale);
        }

        return $countries;
    }

    /**
     * @param string $value
     * @param string $locale
     * @return string
     */
    public function getCountryTranslation($value, $locale = 'en_US')
    {
        return (new RegionBundle())->get($locale)['Countries'][$value];
    }

    /**
     * @param object $address
     * @return string
     */
    public function getStreetMerged($address)
    {
        $street = '';

        if (!empty($address)) {
            empty($address->address) ?: $street.= $address->address;
            empty($address->neighborhood) ?: $street.= ' ' . $address->neighborhood;
            empty($address->reference) ?: $street.= ' ' . $address->reference;
        }

        return $street;
    }

    /**
     * @param string $orderPk
     * @param string $action
     * @return bool
     */
    public function setOrderActionDone($orderPk, $action)
    {
        /** @var \Madkting\Connect\Model\OrderActions $actionModel */
        $actionModel = $this->orderActionsFactory->create();
        $orderAction = $actionModel->loadByOrderPk($orderPk, $action);

        if (!empty($orderAction = $orderAction[0])) {
            $actionModel->load($orderAction['action_id'])->setDone(1);
            $actionModel->save();
        }

        return false;
    }

    /**
     * Fields in variations
     *
     * @return array
     */
    public function getVariationsFields()
    {
        return $this->attributeFactory->create()->getCollection()
            ->addFieldToFilter('in_variation', true)
            ->getColumnValues('attribute_code');
    }

    /**
     * Get fields always shown, requirement no needed
     *
     * @return array
     */
    public function getAlwaysShown()
    {
        return $this->alwaysShown;
    }

    /**
     * Get fields that do not need to be validated
     *
     * @return array
     */
    public function getNoValidation()
    {
        return $this->noValidation;
    }

    /**
     * Get fields with empty value allowed
     *
     * @return array
     */
    public function getEmptyValueAllowed()
    {
        return $this->emptyValueAllowed;
    }

    /**
     * Get fields that have to be cleared
     *
     * @return array
     */
    public function getClearAttributes()
    {
        return $this->clearAttributes;
    }

    /**
     * Get fields with HTML value allowed
     *
     * @return array
     */
    public function getHtmlAllowed()
    {
        return $this->htmlAllowed;
    }

    /**
     * @param string $data
     * @param bool $allowHtml
     * @return string
     */
    public function cleanMadktingData($data, $allowHtml = false)
    {
        /* Return data when it is empty */
        if (empty($data)) {
            return $data;
        }

        /* Process template info */
        $dataCleared = $this->templateProcessor->filter($data);

        /* Decode HTML entities */
        $dataCleared = !$allowHtml ? html_entity_decode(html_entity_decode($dataCleared)) : $dataCleared;

        /* Remove HTML */
        if (!$allowHtml) {
            $dataCleared = preg_replace('/<p>/i', "", $dataCleared);
            $dataCleared = preg_replace('/<\/p>/i', "\n", $dataCleared);
            $dataCleared = preg_replace('/<br\s?\/?>/i', "\n", $dataCleared);
            $dataCleared = strip_tags($dataCleared);
        }

        /* Remove phone numbers */
        $dataCleared = preg_replace('/\s?\d?[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{4}/', '', $dataCleared);

        /* Remove emails */
        $dataCleared = preg_replace('/\s?[\w.+\-]+@[\w.+\-]+\.[A-Za-z]{2,4}/', '', $dataCleared);

        /* Remove <...> data */
        $dataCleared = !$allowHtml ? preg_replace('/\s?<.*>/', '', $dataCleared) : $dataCleared;

        /* Remove Magento replace data */
        $dataCleared = preg_replace('/\s?{{.*}}/', '', $dataCleared);

        /* Remove Unequal expression */
        $dataCleared = preg_replace('/\s?(=!|!=)/', '', $dataCleared);

        /* Remove duplicate symbols data */
        $dataCleared = !$allowHtml ? preg_replace('/\s?[=&#~+_=<>%${}\[\]\/\\\-]{2,}/', '', $dataCleared) : $dataCleared;

        return $dataCleared;
    }
}
