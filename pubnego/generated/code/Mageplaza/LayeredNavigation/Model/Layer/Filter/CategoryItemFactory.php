<?php
namespace Mageplaza\LayeredNavigation\Model\Layer\Filter;

/**
 * Factory class for @see \Mageplaza\LayeredNavigation\Model\Layer\Filter\CategoryItem
 */
class CategoryItemFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Mageplaza\\LayeredNavigation\\Model\\Layer\\Filter\\CategoryItem')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Mageplaza\LayeredNavigation\Model\Layer\Filter\CategoryItem
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
