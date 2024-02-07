<?php
namespace Magento\SalesChannels\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddPublishToChannelManagerAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // Create the attribute.
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'publish_to_channel_manager', [
            'type' => 'int',
            'label' => 'Publish To Channel Manager',
            'input' => 'boolean',
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'required' => false,
            'user_defined' => true, // this technically allows the attr to be deleted, but is required to export the attribute.
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class
        ]);

        // Add the attribute to each attribute set
        $select = $this->moduleDataSetup->getConnection()->select()->from(
            $this->moduleDataSetup->getTable('eav_attribute_set')
        )->where(
            'entity_type_id = :entity_type_id'
        );
        $sets = $this->moduleDataSetup->getConnection()->fetchAll(
            $select,
            ['entity_type_id' => $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY)]
        );
        foreach ($sets as $set) {
            $eavSetup->addAttributeToSet(
                \Magento\Catalog\Model\Product::ENTITY,
                $set['attribute_set_id'],
                'Product Details',
                'publish_to_channel_manager',
                10
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
