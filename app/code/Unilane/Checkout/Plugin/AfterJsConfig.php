<?php
namespace Unilane\Checkout\Plugin;

use Magento\Checkout\Block\Onepage;
use Magento\Framework\Serialize\Serializer\Json;

class AfterJsConfig
{
    /**
     * @var Json
     */
    protected $serializer;

    /**
     * AfterJsConfig constructor.
     * @param Json $serializer
     */
    public function __construct(
        Json $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param Onepage $subject
     * @param $result
     * @return mixed
     */
    public function afterGetJsLayout(Onepage $subject, $result) {
        try {
            if ($result != "") {
                $jsonLayoutArray = $this->serializer->unserialize($result);


                $jsonLayoutArray['components']['checkout']['children']['steps']['children']['shipping-step'] 
                ['children']['shippingAddress']['children']['shipping-address-fieldset'] ['children']['street']['children'][0]['label'] = "Calle o Avenida";

                $jsonLayoutArray['components']['checkout']['children']['steps']['children']['shipping-step'] 
                ['children']['shippingAddress']['children']['shipping-address-fieldset'] ['children']['street']['children'][1]['label'] = "Número Ext - Int";

                $jsonLayoutArray['components']['checkout']['children']['steps']['children']['shipping-step'] 
                ['children']['shippingAddress']['children']['shipping-address-fieldset'] ['children']['street']['children'][2]['label'] = "Entre calle";

                $jsonLayoutArray['components']['checkout']['children']['steps']['children']['shipping-step'] 
                ['children']['billingAddress']['children']['billing-address-fieldset'] ['children']['street']['children'][0]['label'] = "Calle o Avenida";

                $jsonLayoutArray['components']['checkout']['children']['steps']['children']['shipping-step'] 
                ['children']['billingAddress']['children']['billing-address-fieldset'] ['children']['street']['children'][1]['label'] = "Número Ext - Int";

                $jsonLayoutArray['components']['checkout']['children']['steps']['children']['shipping-step'] 
                ['children']['billingAddress']['children']['billing-address-fieldset'] ['children']['street']['children'][2]['label'] = "Entre calle";

                return $this->serializer->serialize($jsonLayoutArray);
            }
        } catch (\Exception $e) {
        }

        return $result;
    }
}