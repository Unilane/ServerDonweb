<?php
namespace Smartwave\Porto\ViewModel;

use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Model\Customer;

class usersloggin implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
    * @var Magento\Customer\Model\SessionFactory
    */
    protected $customerSessionFactory;
    /**
    * @var Magento\Customer\Model\SessionFactory
    */
    protected $Customer;
    /**
    * ...
    * @param \Magento\Customer\Model\SessionFactory $customerSession
    */
    public function __construct(
        
        SessionFactory $customerSessionFactory,
        Customer $Customer
    ) {        
        $this->customerSessionFactory = $customerSessionFactory;
        $this->Customer = $Customer;
    }
    /**
     * @return int
     */
    public function getCustomerId()
    {
        $customerSession = $this->customerSessionFactory->create();
        return $customerSession->getCustomer()->getId();
    }
    /**
    * @return bool
    */
    public function isCustomerLoggedIn()
    {
        $customerSession = $this->customerSessionFactory->create();
        return $customerSession->isLoggedIn();
    }
    /**
    * @return bool
    */
    public function getCustomerNameById($Id)
    {
        $this->Customer->load($Id);
        $customerName = $this->Customer->getName();
        return $customerName;
    }

    // /**
    // * @var Session;
    // */
    // private $customerSession;
    // /**
    // * @param Session $customerSession;
    // */
    // public function __construct(
    //     Session $customerSession
    // )
    // {
    //     $this->customerSession = $customerSession;        
    // }
    // /**
    // * @return int
    // */
    // public function getCustomerId()
    // {
    //     return $this->customerSession->getCustomer()->getId();
    // }
    // /**
    // * @return bool
    // */
    // public function isCustomerLoggedIn()
    // {
    //     return $this->customerSession->isLoggedIn();
    // }
    // /**
    // * @return Session
    // */
    // public function getCustumberSession()
    // {
    //     return $this->customerSession;
    // }
}

?>