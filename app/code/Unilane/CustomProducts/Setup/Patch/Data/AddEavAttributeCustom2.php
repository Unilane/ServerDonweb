<?php
/**
 * Class AddEavAttribute
 *
 * PHP version 7
 *
 */
namespace Unilane\CustomProducts\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Setup\EavSetupFactory;

class AddEavAttributeCustom2
    implements DataPatchInterface,
    PatchRevertableInterface
{
    /**
     * Eav setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'capacidad_ram',
            [
                'type' => 'varchar',
                'label' => 'Capacidad de RAM',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("0.064 GB","0.5 GB","128 KB","2 GB","32 MB","4 GB","64 MB","8 GB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'diagonal',
            [
                'type' => 'varchar',
                'label' => 'Diagonal',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array('152.4 cm (60")','177.8 cm (70")','2.13 m (84")','2.44 m (96")','2.54 m (100")','3.05 m (120")','4.31 m (169.7")','5.18 m (204")','5.74 m (226")'))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_tarjeta_flash',
            [
                'type' => 'varchar',
                'label' => 'Tipo de tarjeta flash',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("CompactFlash","MicroSD","MicroSDHC","MicroSDXC","SDXC"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'capacidad_hdd',
            [
                'type' => 'varchar',
                'label' => 'Capacidad del HDD',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1 TB","1.02 TB","1.2 TB","1.8 TB","10 TB","12 TB","14 TB","16 TB","18 TB","2 TB","2.4 TB","3 TB","4 TB","5 TB","500 GB","6 TB","8 TB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'capacidad',
            [
                'type' => 'varchar',
                'label' => 'Capacidad',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("128 GB","1440 W","16 GB","256 GB","28 ml","32 GB","512 GB","64 GB","70 ml","8 GB","8 ml","90 W"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'fuente_alimentacion',
            [
                'type' => 'varchar',
                'label' => 'Fuente de alimentación',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("100 W","135 W","150 W","180 W","24 W","240 W","260 W","3.36 W","300 W","350 W","450 W","5.92 W","500 W","550 W","600 W","65 W","800 W","90 W"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'max_resolucion',
            [
                'type' => 'varchar',
                'label' => 'Máxima resolución',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1280 x 1024 Pixeles","1280 x 720 Pixeles","1600 x 900 Pixeles","1920 x 1080 Pixeles","1920 x 1200 Pixeles","1920 x 1440 Pixeles","1920 x 1920 Pixeles","1984 x 1225 Pixeles","2048 x 1280 Pixeles","2048 x 1536 Pixeles","2160 x 1440 Pixeles","2304 x 1296 Pixeles","2560 x 1440 Pixeles","2560 x 1600 Pixeles","2592 x 1520 Pixeles","2592 x 1944 Pixeles","2608 x 1960 Pixeles","2688 x 1520 Pixeles","2880 x 1620 Pixeles","2960 x 1665 Pixeles","3096 x 2196 Pixeles","320 x 240 Pixeles","3840 x 2160 Pixeles","3840 x 2400 Pixeles","4096 x 2160 Pixeles","4096 x 2304 Pixeles","5120 x 2880 Pixeles","7680 x 4320 Pixeles"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'frecuencia_procesador',
            [
                'type' => 'varchar',
                'label' => 'Frecuencia del procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1 GHz","1.1 GHz","1.2 GHz","1.2 MHz","1.3 GHz","1.5 GHz","1.6 GHz","1.7 GHz","1.8 GHz","1.82 GHz","1.9 GHz","1000 MHz","1016 MHz","1200 MHz","1400 MHz","1837 MHz","2 GHz","2.05 GHz","2.1 GHz","2.2 GHz","2.3 GHz","2.4 GHz","2.5 GHz","2.6 GHz","2.7 GHz","2.8 GHz","2.9 GHz","200 MHz","240 MHz","2520 MHz","2640 MHz","3 GHz","3.1 GHz","3.2 GHz","3.3 GHz","3.6 GHz","3.7 GHz","3.8 GHz","3.9 GHz","360 MHz","390 MHz","4 GHz","4.3 GHz","500 MHz","520 GHz","533 MHz","560 MHz","600 MHz","666 MHz","710 MHz","768 MHz","800 MHz","902 MHz","954 MHz","980 MHz"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'familia_procesador',
            [
                'type' => 'varchar',
                'label' => 'Familia de procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Allwinner","AMD EPYC","AMD Ryzen","AMD Ryzen Embedded V1000","AMD Ryzen™ 3","AMD Ryzen™ 5","AMD Ryzen™ 7","AMD Ryzen™ 9","Apple","Apple M","ARM","ARM Cortex","ARM Cortex-A5","ARM Cortex-A9","Atheros","Canon","Cortex","Intel Core i3 N-series","Intel Pentium N","Intel Xeon Bronze","Intel Xeon E","Intel® Celeron®","Intel® Celeron® G","Intel® Celeron® N","Intel® Core™ i3","Intel® Core™ i5","Intel® Core™ i7","Intel® Core™ i9","Intel® Pentium®","Intel® Pentium® Gold","Intel® Pentium® Silver","Intel® Xeon Silver","Intel® Xeon®","Intel® Xeon® Gold","MediaTek","Qualcomm Snapdragon","Rockchip","Spreadtrum","Unisoc"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'colores_impresion',
            [
                'type' => 'varchar',
                'label' => 'Colores de impresión',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Amarillo","Azul","Cian","Cian, Gris, Cian claro, Gris claro, Negro mate, Foto negro, Violeta, Magenta claro vivo, Magenta vivo, Amarillo","Cian, Magenta, Amarillo","Cian, Magenta, Negro mate, Foto negro, Amarillo","Cian, Magenta, Negro mate, Foto negro, Rojo, Amarillo","Foto negro","Gris claro","Magenta","Magenta claro","Negro","Negro mate","Negro, Azul, Cian, Magenta","Negro, Blanco","Negro, Cian, Cian claro, Magenta claro, Magenta, Amarillo","Negro, Cian, Gris, Magenta, Foto negro, Amarillo","Negro, Cian, Magenta","Negro, Cian, Magenta, Amarillo","Negro, Magenta, Cian, Amarillo","Negro, Plata","Negro, Rojo"))
            ]
        );
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'fabricante_procesador',
            [
                'type' => 'varchar',
                'label' => 'Fabricante de procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Allwinner Technology","AMD","Amlogic","Apple","ARM","Intel","MediaTek","Qualcomm","Rockchip","Spreadtrum Communications","Unisoc"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'genero_conector1',
            [
                'type' => 'varchar',
                'label' => 'Género del conector 1',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Hembra","Macho","Macho/Hembra","No"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'wifi',
            [
                'type' => 'varchar',
                'label' => 'Wifi',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("No","Si"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'voltaje',
            [
                'type' => 'varchar',
                'label' => 'Voltaje',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1.5 V","5 / 12 V","5 V","5.5 V","10.8 V","100 - 120 V","100 - 127 V","100 - 200 V","100 - 220 V","100 - 230 V","100 - 240 V","100 – 240","100 V","100-127 V","100-240 V","105 - 132 V","107-133 V","108 - 132 V","108 V","11.1 V","11.2 V","11.25 V","11.4 V","110 - 120 V","110 - 125 V","110 - 127 V","110 - 220 V","110 - 230 V","110 - 240 V","110 - 250 V","110 V","110-120 V","110-127 V","115 - 220 V","115 - 230 V","115 V","12 V","120 - 127 V","120 - 230 V","120 - 240 V","120 - 270 V","120 V","120-220 V","125 V","127 V","13 V","13.2 V","13.35 V","14.8 V","140 V","15 V","15.2 V","170 V","192 V","200 - 240 V","208 V","220 - 240 V","220 V","220-240 V","230 V","24 V","25.2 V","3 V","3.6 V","3.7 V","3.8 V","3.857 V","48 V","5 - 12 V","50 V","55 V","60 V","7.2 V","7.4 V","7.6 V","70 V","72 V","75 V","78 V","80 V","81 V","82 V","84 V","85 V","86 V","88 V","89 V","9 V","90 - 264 V","90 V","92 V","93 V","95 V","96 - 264 V","96 V"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'velocidad_captura_video',
            [
                'type' => 'varchar',
                'label' => 'Velocidad de captura de vídeo',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("120 fps","240 fps","30 fps"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_pantalla',
            [
                'type' => 'varchar',
                'label' => 'Tipo de Pantalla',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("AMOLED","IPS","LCD","LCD/TFT","LED","No","OLED","TFT","TN","VA","WVA"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_memoria_ram',
            [
                'type' => 'varchar',
                'label' => 'Tipo de Memoria RAM',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("DDR2","DDR3","DDR3-SDRAM","DDR3L","DDR3L-SDRAM","DDR4","DDR4-SDRAM","DDR5","DDR5-SDRAM","DRAM","eSATA","GDDR6","LPDDR3-SDRAM","LPDDR4-SDRAM","LPDDR4x-SDRAM","LPDDR5-SDRAM","NAND","SATA","SATA, eSATA","SATA, SATA II, SATA III","Serial ATA II","UHS","UHS-I","UHS-III"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_memoria_int',
            [
                'type' => 'varchar',
                'label' => 'Tipo de memoria interna',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("DDR2","DDR3","DDR3-SDRAM","DDR3L","DDR3L-SDRAM","DDR4","DDR4-SDRAM","DDR5","DDR5-SDRAM","DRAM","GDDR6","LPDDR3-SDRAM","LPDDR4-SDRAM","LPDDR4x-SDRAM","LPDDR5-SDRAM","NAND","UHS","UHS-I","UHS-III"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_memoria',
            [
                'type' => 'varchar',
                'label' => 'Tipo de memoria',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("3D NAND","3D TLC","3D TLC NAND","3D2 QLC","DDR3-SDRAM","DRAM","MLC","PC-12800","QLC","QLC 3D NAND","SDRAM","SLC","TLC","DDR-SDRAM","DDR2","DDR3","DDR3L","DDR3L-SDRAM","DDR4","DDR4-SDRAM","DDR5","DDR5-SDRAM","LPDDR3-SDRAM","LPDDR4-SDRAM","LPDDR4x-SDRAM","LPDDR5-SDRAM","NAND","UHS","UHS-I","UHS-III"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_disco_duro',
            [
                'type' => 'varchar',
                'label' => 'Tipo de Disco Duro',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("eMCP","eMMC","Flash","HDD + SSD","HDD+SSD","SSD","SSD","Unidad de disco duro"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_antena',
            [
                'type' => 'varchar',
                'label' => 'Tipo de antena',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1x1","2x Omni","2x2","Antena direccional","Antena direccional MIMO","Antena sectorial","Externo","Interno","Omni-direccional"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tecnologia_visualizacion',
            [
                'type' => 'varchar',
                'label' => 'Tecnología de visualización',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("AMOLED","IPS","LCD","LCD/TFT","LED","Mini LED","OLED","QLED","TFT","TN","VA","WVA"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tecnologia_impresion',
            [
                'type' => 'varchar',
                'label' => 'Tecnología de impresión',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Impresión láser","Impresión LED","Impresión por inyección de tinta","Inyección de tinta","Inyección de tinta térmica","Laser","LED","Matriz de punto","Pintar por sublimación","Pintar por sublimación/Transferencia térmica","Sublimación de tinta/Transferencia térmica por resina","Térmica directa","Térmica directa / transferencia térmica","Térmico","Transferencia térmica","Universal"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tarjetas_memoria_comp',
            [
                'type' => 'varchar',
                'label' => 'Tarjetas de memoria compatibles',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("MicroSD (TransFlash)","MicroSD (TransFlash), MicroSDHC","MicroSD (TransFlash), MicroSDHC, MicroSDXC","MicroSD (TransFlash), MicroSDHC, MicroSDXC, SDHC, SDXC","MicroSD (TransFlash), MicroSDHC, SD, SDHC","MicroSD (TransFlash), MicroSDXC","MicroSD (TransFlash), SD","MiniSD","MMC, MiniSD, RS-MMC, SD, SDHC, SDXC","MMC, SD","MMC, SD, SDHC, SDXC","MS Duo, MicroSD (TransFlash), SD","No compatible","SD","SD, SDHC","SD, SDHC, SDXC","SD, SDIO, SDXC","SDXC"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tam_disco_duro_soportado',
            [
                'type' => 'varchar',
                'label' => 'Tamaños de disco duro soportados',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array('2.5,3.5,5.25"','2.5,3.5"','2.5,5.25"','2.5"','3.5"'))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tam_max_memoria',
            [
                'type' => 'varchar',
                'label' => 'Tamaño máximo de tarjeta de memoria',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1000 GB","128 GB","256 GB","32 GB","512 GB","64 GB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tam_max_pantalla',
            [
                'type' => 'varchar',
                'label' => 'Tamaño máximo de pantalla',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array('106.7 cm (42")','139.7 cm (55")','152.4 cm (60")','165.1 cm (65")','17.8 cm (7")','177.8 cm (70")','2.03 m (80")','2.16 m (85")','2.29 m (90")','2.54 m (100")','25.9 cm (10.2")','26.7 cm (10.5")','31.8 cm (12.5")','33 cm (13")','33.8 cm (13.3")','35.6 cm (14")','36.8 cm (14.5")','38.1 cm (15")','39.1 cm (15.4")','39.6 cm (15.6")','40.6 cm (16")','43.2 cm (17")','49.5 cm (19.5")','53.3 cm (21")','54.6 cm (21.5")','61 cm (24")','68.6 cm (27")','81.3 cm (32")','86.4 cm (34")'))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'smarttv',
            [
                'type' => 'varchar',
                'label' => 'Smart TV',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("No","Si"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'so_instalado',
            [
                'type' => 'varchar',
                'label' => 'Sistema operativo instalado',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Android","Android 10","Android 11","Android 11 Go edition","Android 12","Android 5.1","Android 8.1 Oreo","Android 9.0","DTEN D7 OS","Google TV","HarmonyOS","iPadOS 14","iPadOS 15","Linux","Linux Embedded","LiteOS","macOS Big Sur","macOS Monterey","My Cloud OS","No","Roku OS","Sistema operativo de la web","Tizen","Tizen 4.0","Tizen 6.5","VIDAA","VIDAA U","watchOS 8","webOS","Windows 10","Windows 10 Home","Windows 10 Home S","Windows 10 Pro","Windows 10 Pro for Workstations","Windows 11 Home","Windows 11 Home in S mode","Windows 11 Pro","AirOS","Android 12 Go edition","Android 8.0","ChromeOS","iPadOS 16","LINUX incrustado","macOS Ventura","OS6","Ubuntu Linux","Windows Server IoT 2019","Zepp OS"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'resolucion_movimiento',
            [
                'type' => 'varchar',
                'label' => 'Resolución de movimiento',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1000 DPI","10000 DPI","1200 DPI","12000 DPI","12400 DPI","1480 DPI","1500 DPI","1600 DPI","16000 DPI","1750 DPI","18000 DPI","2000 DPI","2400 DPI","24000 DPI","25400 DPI","25600 DPI","26000 DPI","300 DPI","3200 DPI","32000 DPI","3500 DPI","3600 DPI","380 DPI","400 DPI","4000 DPI","4200 DPI","4800 DPI","5000 DPI","6000 DPI","6200 DPI","6400 DPI","7200 DPI","800 DPI","8000 DPI","19000 DPI","8200 DPI"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'resolucion_pantalla',
            [
                'type' => 'varchar',
                'label' => 'Resolución de la pantalla',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1024 x 600 Pixeles","1024 x 768 Pixeles","1080 x 2400 Pixeles","1204 x 600 Pixeles","128 x 160 Pixeles","128 x 32 Pixeles","128 x 56 Pixeles","1280 x 1024 Pixeles","1280 x 600 Pixeles","1280 x 720 Pixeles","1280 x 800 Pixeles","132 x 48 Pixeles","132 x 64 Pixeles","1340 x 800 Pixeles","1366 x 768 Pixeles","1376 x 768 Pixeles","1440 x 900 Pixeles","1560 x 720 Pixeles","160 x 80 Pixeles","1600 x 900 Pixeles","1800 x 2400 Pixeles","1920 x 1080 Pixeles","1920 x 1200 Pixeles","1920 x 1440 Pixeles","2000 x 1200 Pixeles","2048 x 1280 Pixeles","2160 x 1620 Pixeles","2256 x 1504 Pixeles","2266 x 1488 Pixeles","2304 x 1296 Pixeles","2360 x 1640 Pixeles","2388 x 1668 Pixeles","240 x 240 Pixeles","240 x 280 Pixeles","240 x 320 Pixeles","2560 x 1080 Pixeles","2560 x 1440 Pixeles","2560 x 1600 Pixeles","2560 x 1944 Pixeles","2592 x 1520 Pixeles","2592 x 1944 Pixeles","2608 x 1960 Pixeles","2688 x 1520 Pixeles","2732 x 2048 Pixeles","2880 x 1620 Pixeles","3024 x 1964 Pixeles","3096 x 2196 Pixeles","320 x 240 Pixeles","336 x 480 Pixeles","3440 x 1440 Pixeles","3456 x 2234 Pixeles","352 x 430 Pixeles","368 x 194 Pixeles","3840 x 2160 Pixeles","3840 x 2400 Pixeles","3840 x 2560 Pixeles","396 x 484 Pixeles","4096 x 2160 Pixeles","4096 x 2304 Pixeles","4480 x 2520 Pixeles","456 x 280 Pixeles","466 x 466 Pixeles","480 x 272 Pixeles","480 x 960 Pixeles","5120 x 1440 Pixeles","5120 x 2160 Pixeles","5120 x 2880 Pixeles","720 x 1600 Pixeles","7680 x 4320 Pixeles","800 x 480 Pixeles"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'ranuras_memoria',
            [
                'type' => 'varchar',
                'label' => 'Ranuras de memoria',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1","16x DIMM","1x SO-DIMM","2","24x DIMM","2x DIMM","2x SO-DIMM","4x DIMM","4x SO-DIMM","6x DIMM","24","32x DIMM","3x SO-DIMM"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pantalla_tactil',
            [
                'type' => 'varchar',
                'label' => 'Pantalla táctil',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("No","Si"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'num_puertos_35',
            [
                'type' => 'varchar',
                'label' => 'Número de puertos 3.5"',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1","2","3","4"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'num_bahias_25',
            [
                'type' => 'varchar',
                'label' => 'Número de bahías 2.5"',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1","2","3","4","6"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'montaje_vesa',
            [
                'type' => 'varchar',
                'label' => 'montaje VESA',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("No","Si"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'modelo_procesador',
            [
                'type' => 'varchar',
                'label' => 'Modelo del procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("3200G","3204","3250U","3450U","4208","4210R","4300U","4309Y","4310","4314","4316","4500","4600H","4650G","4650U","5300U","5315Y","5500U","5600","5600G","5600H","5600U","5600X","5625U","5700G","5700U","5800H","5800X","5900HX","5900X","5950X","6305","6600H","6800H","7232P","7313P","7600X","7700X","7900X","7950X","A13","A15","A7","A72","A8","AMD Ryzen Zen 2","ARM Cortex-A9","Armada 385","Armada 388","Atheros MIPS 74Kc","Cortex-A53","E-2134","E-2224","E-2224G","E-2314","E-2324G","E-2336","E-2356G","E-2378","G6400","G6900","Helio A22","Helio G80 (MT6769V)","Helio G90T","Helio G95","Helio P22T","i3-1005G1","i3-10100","i3-10100F","i3-10105","i3-10110U","i3-1115G4","i3-12100","i3-1215U","i3-8145U","i5-10210U","i5-1035G1","i5-10400","i5-10400F","i5-10500","i5-10500T","i5-10505","i5-10600K","i5-11260H","i5-11300H","i5-11320H","i5-1135G7","i5-11400","i5-11400F","i5-11400H","i5-1155G7","i5-11600K","i5-1235U","i5-12400","i5-12400F","i5-12500","i5-12500T","i5-12600","i5-12600K","i5-13600K","i7-10510U","i7-1065G7","i7-10700","i7-10700F","i7-10700K","i7-10700T","i7-11370H","i7-11390H","i7-11600H","i7-1165G7","i7-11700","i7-11700F","i7-11700K","i7-11700KF","i7-11800H","i7-1185G7","i7-1255U","i7-1265U","i7-12700","i7-12700F","i7-12700H","i7-12700K","i7-12700KF","i7-12800HX","i7-13700K","i9-10900","i9-10900F","i9-10900K","i9-10900KF","i9-11900","i9-11900K","i9-11950H","i9-12900K","i9-12900KF","i9-12900KS","J1900","J3710","J4005","J5005","M1","M1 Max","M1 Pro","M2","MIPS 74Kc","MT6769V/CA","MT8768","N3060","N3350","N4020","N4120","N4200","N4500","Qualcomm Atheros","Realtek","RK3126C","S7","SC7731E","SC9863A","T610","T618","V1605B","3206R","3250C","4100","4600G","5318Y","7305","7352","7520U","7600","7640HS","7840HS","A14","A9","Cortex-A72","E-2286G","G5905","G7400","Helio G99","i3-10100T","i3-12100T","i3-13100","i3-N305","i5-12400T","i5-1240P","i5-12450H","i5-1245U","i5-12500H","i5-1335U","i5-13400","i5-13500","i5-13500T","i5-14600KF","i7-1260P","i7-12700T","i7-12800H","i7-1355U","i7-13620H","i7-13650HX","i7-1365U","i7-13700","i7-13700H","i7-13700HX","i7-13700KF","i7-1370P","i7-13850HX","i7-14700K","i7-14700KF","i9-11900KF","i9-13900H","i9-14900KF","J5040","MT8166","N4505","N5100","N6000","NVIDIA Custom Tegra","NXP 555","Qualcomm Atheros AR9344-DC3A","RK3326","S905W"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'memoria_flash',
            [
                'type' => 'varchar',
                'label' => 'Memoria flash',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("128 GB","32 GB","4 GB","64 GB","128 MB","132 MB","16 MB","16384 MB","2 MB","256 MB","32 MB","32768 MB","4 MB","4000 MB","4096 MB","512 MB","6 MB","64 MB","7.8 MB","8 MB","8000 MB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'marca_procesador',
            [
                'type' => 'varchar',
                'label' => 'Marcar del procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Allwinner Technology","AMD","Apple","ARM","Arrow","Intel","MediaTek","Qualcomm","Rockchip","Spreadtrum Communications","Unisoc"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'latencia_cas',
            [
                'type' => 'varchar',
                'label' => 'Latencia CAS',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("11","15","16","17","18","19","20","21","22","38","40","43"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'impresion_doble_cara',
            [
                'type' => 'varchar',
                'label' => 'Impresión a doble cara',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("No","Si"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'genero_conector',
            [
                'type' => 'varchar',
                'label' => 'Género del conector',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Macho","Macho/Hembra","Macho/Macho","No"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tarjeta_madre_soportada',
            [
                'type' => 'varchar',
                'label' => 'Formas de factor de tarjeta madre soportadas',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("ATX","ATX, CEB, EATX, EEB, Micro ATX, Mini-ITX","ATX, EATX, ITX, Micro ATX","ATX, EATX, Micro ATX, Mini-ITX","ATX, ITX","ATX, Micro ATX","ATX, Micro ATX, Mini-ATX","ATX, Micro ATX, Mini-ITX","ATX, Micro ATX, Mini-ITX, EATX","ITX, Micro ATX","Micro ATX","Micro ATX, Mini-ATX","Micro ATX, Mini-ATX, Mini-ITX","Micro ATX, Mini-ITX","Mini-ATX","ATX, EATX, Micro ATX, Mini-ATX","ATX, ITX, Micro ATX","ATX, ITX, Micro ATX, Mini-ITX","ITX, Mini-ATX","Micro ATX, Micro-ITX"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'formato_ssd',
            [
                'type' => 'varchar',
                'label' => 'Factor de formato SSD',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array('3.5"','2.5"','M.2'))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'ethernet',
            [
                'type' => 'varchar',
                'label' => 'Ethernet',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("No","Si"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'estilo_teclado',
            [
                'type' => 'varchar',
                'label' => 'Estilo de teclado',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Curvo","Derecho"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'diagonal_pantalla',
            [
                'type' => 'varchar',
                'label' => 'Diagonal de pantalla',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("107.9 cm","127 cm","139 cm","190.5 cm","31.75 cm","33 cm","33.782 cm","35.6 cm","39 cm","39.6 cm","48.26 cm","49.5 cm","54.48 cm","54.6 cm","54.61 cm","55 cm","6.8 cm","60.4 cm","60.45 cm","60.47 cm","60.5 cm","61 cm","68.4 cm","68.6 cm","71.6 cm","73 cm","80 cm","86.7 cm"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'con_usar_plug',
            [
                'type' => 'varchar',
                'label' => 'Conectar y usar (Plug and Play)',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Si"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cap_total_salida',
            [
                'type' => 'varchar',
                'label' => 'Capacidad total de salida',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("100 hojas","120 hojas","125 hojas","150 hojas","25 hojas","250 hojas","280 hojas","30 hojas","300 hojas","400 hojas","50 hojas","500 hojas","65 hojas"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cap_potencia_salida',
            [
                'type' => 'varchar',
                'label' => 'Capacidad de potencia de salida (VA)',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("0.12 kVA","0.35 kVA","0.425 kVA","0.45 kVA","0.5 kVA","0.52 kVA","0.55 kVA","0.6 kVA","0.625 kVA","0.65 kVA","0.685 kVA","0.7 kVA","0.75 kVA","0.8 kVA","0.825 kVA","0.85 kVA","0.9 kVA","1 kVA","1.1 kVA","1.2 kVA","1.25 kVA","1.35 kVA","1.44 kVA","1.5 kVA","1.8 kVA","10 kVA","1000 kVA","2 kVA","2.2 kVA","2.4 kVA","2.7 kVA","2.88 kVA","3 kVA","5 kVA","6 kVA"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cap_bateria',
            [
                'type' => 'varchar',
                'label' => 'Capacidad de batería',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("10 mAh","100 mAh","100 Wh","1000 mAh","10000 mAh","1040 mAh","1050 mAh","110 mAh","1200 mAh","14 mAh","150 mAh","1500 mAh","160 mAh","1600 mAh","16000 mAh","180 mAh","1800 mAh","19.3 Wh","1900 mAh","200 mAh","2000 mAh","210 mAh","220 mAh","2200 mAh","2400 mAh","250 mAh","2500 mAh","28.6 Wh","28.65 Wh","280 mAh","2800 mAh","292 mAh","30 mAh","300 mAh","3000 mAh","310 mAh","3100 mAh","32.4 Wh","3200 mAh","33 Wh","35 Wh","350 mAh","36 Wh","36.7 Wh","3600 mAh","37 Wh","38 Wh","38.5 Wh","380 mAh","3800 mAh","3920 mAh","40 mAh","40.88 Wh","400 mAh","4000 mAh","41 Wh","42 Wh","4300 mAh","44.5 Wh","4400 mAh","45 Wh","4500 mAh","455 mAh","46 Wh","48 Wh","49 Wh","49.9 Wh","50 Wh","500 mAh","5000 mAh","51 Wh","5100 mAh","52.8 Wh","5200 mAh","53 Wh","54 Wh","56 Wh","57 Wh","57.5 Wh","58 Wh","58.2 Wh","60 Wh","600 mAh","6000 mAh","610 mAh","63 Wh","65 mAh","650 mAh","66 Wh","67 Wh","68 Wh","70 mAh","70 Wh","7000 mAh","7040 mAh","71 Wh","74 Wh","7600 mAh","7700 mAh","80 mAh","800 mAh","8000 mAh","816 Wh","83 Wh","85 mAh","86 Wh","87 Wh","90 mAh","90 Wh","900 mAh","9000 mAh","94 Wh"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cant_antenas',
            [
                'type' => 'varchar',
                'label' => 'Cantidad de antenas',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1","12","2","3","4","5","6","7"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'brillo_proyector',
            [
                'type' => 'varchar',
                'label' => 'Brillo de proyector',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1500 lúmenes ANSI","2000 lúmenes ANSI","2200 lúmenes ANSI","2500 lúmenes ANSI","2700 lúmenes ANSI","3000 lúmenes ANSI","3500 lúmenes ANSI","3600 lúmenes ANSI","3800 lúmenes ANSI","4000 lúmenes ANSI","4200 lúmenes ANSI","4500 lúmenes ANSI","5000 lúmenes ANSI","550 lúmenes ANSI","300 lúmenes ANSI","3400 lúmenes ANSI","450 lúmenes ANSI","4800 lúmenes ANSI","6000 lúmenes ANSI"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'inclinacion',
            [
                'type' => 'varchar',
                'label' => 'Ángulo de inclinación',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("-10 - 10°","-10 - 15°","-10 - 20°","-10 - 5°","-12 - 5°","-15 - 15°","-15 - 5°","-15 - 68°","-2 - 15°","-2 - 20°","-2 - 22°","-2 - 25°","-2 - 4°","-20 - 10°","-20 - 20°","-20 - 60°","-3 - 13°","-3.5 - 21.5°","-4 - 14°","-4 - 18°","-4 - 21.5°","-45 - 45°","-5 - 15°","-5 - 17°","-5 - 18°","-5 - 20°","-5 - 21°","-5 - 22°","-5 - 23°","-5 - 25°","-5 - 30°","-5 - 35°","-5 - 80°","-50 - 20°","-8 - 0°","-90 - 90°","0 - 10°","0 - 114°","0 - 120°","0 - 14°","0 - 15°","0 - 165°","0 - 180°","0 - 20°","0 - 25°","0 - 45°","0 - 5°","0 - 75°","0 - 85°","0 - 90°","0 - 95°","10 - 15°","10 - 20°","15 - 15°","23 - -5°","3.5 - 21.5°","35 - -5°","45 - 45°","5 - 15°","6.5 - 45°","90 - -45°","90 - 145°","-12 - 12°","-15 - 90°","-25 - 25°","-3 - 22°","-35 - 90°","-5 - 23.5°","-5 - 33°","-5 - 60°","-70 - 70°","-8 - 8°","-80 - 90°","0 - 135°","0 - 360°","0 - 65°","0 - 78°","0 - 87°","20 - -5°","45 - 90°","90 - 135°","90 - 165°","90 - 180°"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'airplay',
            [
                'type' => 'varchar',
                'label' => 'AirPlay',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("No","Si"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'velocidad_procesador',
            [
                'type' => 'varchar',
                'label' => 'Velocidad del procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("0.717 GHz","1 GHz","1.1 GHz","1.2 GHz","1.2 MHz","1.3 GHz","1.4 GHz","1.5 GHz","1.6 GHz","1.7 GHz","1.8 GHz","1.82 GHz","1.9 GHz","1000 MHz","1016 MHz","1050 MHz","1151 MHz","1200 MHz","1219 MHz","1400 MHz","1500 MHz","1600 MHz","1700 MHz","1830 MHz","2 GHz","2.05 GHz","2.2 GHz","2.3 GHz","2.4 GHz","2.64 GHz","2.7 GHz","2.8 GHz","200 MHz","2200 MHz","240 MHz","3.2 GHz","3.3 GHz","3.4 GHz","3.5 GHz","3.6 GHz","3.7 GHz","3.8 GHz","3.9 GHz","360 MHz","4 GHz","4.1 GHz","4.2 GHz","4.3 GHz","4.4 GHz","4.5 GHz","4.6 GHz","4.7 GHz","4.8 GHz","4.9 GHz","5 GHz","5.1 GHz","5.2 GHz","5.3 GHz","5.4 GHz","5.5 GHz","5.6 GHz","5.7 GHz","500 MHz","533 MHz","560 MHz","600 MHz","666 MHz","716 MHz","720 MHz","750 MHz","800 MHz","880 MHz","900 MHz","902 MHz"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tipo_procesador',
            [
                'type' => 'varchar',
                'label' => 'Tipo Procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Unisoc SC9863A","Unisoc SC7731E","Tigre T618","Spreadtrum SC9863A","Spreadtrum SC7731E","Spreadtrum","Rockchip","MediaTek MT6769V/CA","Mediatek Helio P22T","MediaTek Helio G95","Mediatek Helio G90T","MediaTek Helio G80 (MT6769V)","Mediatek Helio A22","Marvell Armada 388","Marvell Armada 385","Intel® Xeon® 4310","Intel® Xeon Silver 5315Y","Intel® Xeon Silver 4316","Intel® Xeon Silver 4314 Intel® Xeon® Escalable de 3ª generación","Intel® Xeon Silver 4314","Intel® Xeon Silver 4310 Intel® Xeon® Escalable de 3ª generación","Intel® Xeon Silver 4309Y","Intel® Xeon Silver 4210R Intel® Xeon® Escalable de 2ª generación","Intel® Xeon Silver 4208 Intel® Xeon® Escalable de 2ª generación","Intel® Pentium® J5005","Intel® Pentium® J3710","Intel® Pentium® J J3710","Intel® Pentium® Gold G6400","Intel® Core™ i9 i9-12900KS Intel® Core™ i9 de 12ma Generación","Intel® Core™ i9 i9-12900KF Intel® Core™ i9 de 12ma Generación","Intel® Core™ i9 i9-12900K Intel® Core™ i9 de 12ma Generación","Intel® Core™ i9 i9-11950H","Intel® Core™ i9 i9-11900K Intel® Core™ i9 de 11ma Generación","Intel® Core™ i9 i9-11900 Intel® Core™ i9 de 11ma Generación","Intel® Core™ i9 i9-10900KF Intel® Core™ i9 de 10ma Generación","Intel® Core™ i9 i9-10900K Intel® Core™ i9 de 10ma Generación","Intel® Core™ i9 i9-10900F Intel® Core™ i9 de 10ma Generación","Intel® Core™ i9 i9-10900 Intel® Core™ i9 de 10ma Generación","Intel® Core™ i7 i7-13700K Intel® Core™ i7 de 13ma Generación","Intel® Core™ i7 i7-12800HX Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-12700KF Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-12700KF","Intel® Core™ i7 i7-12700K Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-12700H Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-12700F Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-12700 Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-1265U Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-1255U Intel® Core™ i7 de 12ma Generación","Intel® Core™ i7 i7-1185G7 Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-11800H Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-11700KF Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-11700K Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-11700F Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-11700 Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-1165G7 Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-1165G7 Intel® Core™ i7 de 10ma Generación","Intel® Core™ i7 i7-1165G7 Intel® Core™ i5 de 11ma Generación","Intel® Core™ i7 i7-1165G7","Intel® Core™ i7 i7-11600H Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-11390H Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-11370H Intel® Core™ i7 de 11ma Generación","Intel® Core™ i7 i7-10700T Intel® Core™ i7 de 10ma Generación","Intel® Core™ i7 i7-10700K Intel® Core™ i7 de 10ma Generación","Intel® Core™ i7 i7-10700F Intel® Core™ i7 de 10ma Generación","Intel® Core™ i7 i7-10700 Intel® Core™ i7 de 10ma Generación","Intel® Core™ i7 i7-10700 Intel® Core™ i5 de 10ma Generación","Intel® Core™ i7 i7-1065G7 Intel® Core™ i7 de 10ma Generación","Intel® Core™ i7 i7-10510U Intel® Core™ i7 de 10ma Generación","Intel® Core™ i5 i5-13600K Intel® Core™ i5 de 13ma Generación","Intel® Core™ i5 i5-12600K Intel® Core™ i5 de 12ma Generación","Intel® Core™ i5 i5-12600K","Intel® Core™ i5 i5-12600 Intel® Core™ i5 de 12ma Generación","Intel® Core™ i5 i5-12500T Intel® Core™ i5 de 12ma Generación","Intel® Core™ i5 i5-12500T","Intel® Core™ i5 i5-12500 Intel® Core™ i5 de 12ma Generación","Intel® Core™ i5 i5-12500","Intel® Core™ i5 i5-12400F Intel® Core™ i5 de 12ma Generación","Intel® Core™ i5 i5-12400 Intel® Core™ i5 de 12ma Generación","Intel® Core™ i5 i5-1235U Intel® Core™ i5 de 12ma Generación","Intel® Core™ i5 i5-1235U","Intel® Core™ i5 i5-11600K Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-1155G7 Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-11400H Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-11400F Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-11400 Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-1135G7 Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-1135G7","Intel® Core™ i5 i5-11320H Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-11300H Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-11260H Intel® Core™ i5 de 11ma Generación","Intel® Core™ i5 i5-10600K Intel® Core™ i5 de 10ma Generación","Intel® Core™ i5 i5-10505","Intel® Core™ i5 i5-10500T Intel® Core™ i5 de 10ma Generación","Intel® Core™ i5 i5-10500 Intel® Core™ i5 de 10ma Generación","Intel® Core™ i5 i5-10500","Intel® Core™ i5 i5-10400F Intel® Core™ i5 de 10ma Generación","Intel® Core™ i5 i5-10400 Intel® Core™ i5 de 10ma Generación","Intel® Core™ i5 i5-10400","Intel® Core™ i5 i5-1035G1 Intel® Core™ i5 de 10ma Generación","Intel® Core™ i5 i5-10210U Intel® Core™ i5 de 10ma Generación","Intel® Core™ i3 i3-8145U 8th gen Intel® Core™ i3","Intel® Core™ i3 i3-1215U Intel® Core™ i3 de 12ma Generación","Intel® Core™ i3 i3-12100 Intel® Core™ i3 de 12ma Generación","Intel® Core™ i3 i3-1115G4 Intel® Core™ i3 de 11ma Generación","Intel® Core™ i3 i3-1115G4","Intel® Core™ i3 i3-10110U Intel® Core™ i3 de 10ma Generación","Intel® Core™ i3 i3-10105 Intel® Core™ i3 de 10ma Generación","Intel® Core™ i3 i3-10105 Intel® Core™ i3","Intel® Core™ i3 i3-10105","Intel® Core™ i3 i3-10100F Intel® Core™ i3 de 10ma Generación","Intel® Core™ i3 i3-10100F","Intel® Core™ i3 i3-10100 Intel® Core™ i3 de 10ma Generación","Intel® Core™ i3 i3-10100","Intel® Core™ i3 i3-1005G1 Intel® Core™ i3 de 10ma Generación","Intel® Celeron® N4020","Intel® Celeron® N3350","Intel® Celeron® N N4500","Intel® Celeron® N N4120","Intel® Celeron® N N4020","Intel® Celeron® N N3350","Intel® Celeron® N N3060","Intel® Celeron® J4005","Intel® Celeron® J1900","Intel® Celeron® G G6900","Intel® Celeron® 6305","Intel Xeon E E-2378","Intel Xeon E E-2356G","Intel Xeon E E-2336","Intel Xeon E E-2324G","Intel Xeon E E-2314","Intel Xeon E E-2224G","Intel Xeon E E-2224","Intel Xeon E E-2134","Intel Xeon Bronze 3204 Intel® Xeon® Escalable de 2ª generación","Intel Pentium N N4200","Cortex Cortex-A53","Cortex A7","Canon","Atheros MIPS 74Kc","ARM Cortex-A9","ARM Cortex A72","ARM Cortex","ARM","APQ8053","Apple M M2","Apple M M1 Pro 1st Generation Apple M Pro","Apple M M1 Max 1st Generation Apple M Max","Apple M M1 1st Generation Apple M","Apple A15","Apple A13","AMD Ryzen™ 9 7950X","AMD Ryzen™ 9 7900X","AMD Ryzen™ 9 5950X","AMD Ryzen™ 9 5900X","AMD Ryzen™ 9 5900HX","AMD Ryzen™ 7 7700X","AMD Ryzen™ 7 6800H","AMD Ryzen™ 7 5800X","AMD Ryzen™ 7 5800H","AMD Ryzen™ 7 5700U","AMD Ryzen™ 7 5700G","AMD Ryzen™ 5 PRO 4650U","AMD Ryzen™ 5 PRO 4650G","AMD Ryzen™ 5 7600X","AMD Ryzen™ 5 6600H","AMD Ryzen™ 5 5700G","AMD Ryzen™ 5 5625U","AMD Ryzen™ 5 5600X","AMD Ryzen™ 5 5600U","AMD Ryzen™ 5 5600H","AMD Ryzen™ 5 5600G","AMD Ryzen™ 5 5600","AMD Ryzen™ 5 5500U","AMD Ryzen™ 5 4600H","AMD Ryzen™ 5 4500","AMD Ryzen™ 5 3450U","AMD Ryzen™ 3 5300U","AMD Ryzen™ 3 4300U","AMD Ryzen™ 3 3250U","AMD Ryzen™ 3 3200G","AMD Ryzen Embedded V1000 V1605B","AMD EPYC 7313P","AMD EPYC 7232P","Allwinner","T610","SC7731E","S7","RK3126C","Realtek","Qualcomm Atheros","N4020","MT8768","Atheros MIPS 74Kc","ARM Cortex-A9","AMD Ryzen Zen 2","A8"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tecnologia_conect',
            [
                'type' => 'varchar',
                'label' => 'Tecnología de conectividad',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Alámbrico","Inalámbrico","Inalámbrico y alámbrico","True Wireless Stereo (TWS)"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tam_hdd',
            [
                'type' => 'varchar',
                'label' => 'Tamaño del HDD',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array('2.5"','3.5"','2.5/3.5"'))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'tam_pantalla',
            [
                'type' => 'varchar',
                'label' => 'Tamaño de pantalla',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array('0','10.9 cm (4.3")','106.7 cm (42")','108 cm (42.5")','109.2 cm (43")','12.7 cm (5")','124.5 cm (49")','127 cm (50")','138.7 cm (54.6")','139.7 cm (55")','146.1 cm (57.5")','147.3 cm (58")','15.2 cm (5.99")','15.5 cm (6.1")','152 x 152 cm','152.4 cm (60")','16.6 cm (6.52")','16.9 cm (6.67")','163.9 cm (64.5")','164.1 cm (64.6")','165.1 cm (65")','17.8 cm (7")','177.8 cm (70")','178 x 178 cm','190.5 cm (75")','2.44 cm (0.96")','20.3 cm (8")','21.1 cm (8.3")','213 x 213 cm','22.1 cm (8.7")','24.6 cm (9.7")','244 x 244 cm','25.6 cm (10.1")','25.9 cm (10.2")','26.7 cm (10.5")','27.7 cm (10.9")','27.9 cm (11")','29.5 cm (11.6")','3.25 cm (1.28")','3.3 cm (1.3")','3.35 cm (1.32")','3.56 cm (1.4")','3.63 cm (1.43")','3.66 cm (1.44")','3.71 cm (1.46")','3.73 cm (1.47")','3.81 cm (1.5")','305 x 305 cm','32.8 cm (12.9")','33.8 cm (13.3")','34.3 cm (13.5")','35.6 cm (14")','35.8 cm (14.1")','36.1 cm (14.2")','38.1 cm (15")','39.6 cm (15.6")','4.17 cm (1.64")','4.27 cm (1.68")','4.29 cm (1.69")','4.42 cm (1.74")','4.57 cm (1.8")','40.6 cm (16")','40.9 cm (16.1")','41.1 cm (16.2")','43.2 cm (17")','43.9 cm (17.3")','47 cm (18.5")','48.3 cm (19")','49.5 cm (19.5")','5.59 cm (2.2")','5.61 cm (2.21")','54.5 cm (21.4")','54.6 cm (21.5")','55.9 cm (22")','59.7 cm (23.5")','59.9 cm (23.6")','6.1 cm (2.4")','6.3 cm (2.48")','6.86 cm (2.7")','60.5 cm (23.8")','61 cm (24")','61.2 cm (24.1")','62.2 cm (24.5")','65.3 cm (25.7")','68.6 cm (27")','7.11 cm (2.8")','7.62 cm (3")','71.1 cm (28")','71.6 cm (28.2")','73.7 cm (29")','74.9 cm (29.5")','8.89 cm (3.5")','80 cm (31.5")','81.3 cm (32")','86.4 cm (34")','9.3 cm (3.66")','9.4 cm (3.7")'))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'memoria_ram',
            [
                'type' => 'varchar',
                'label' => 'Tamaño de memoria RAM',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1 GB","1 MB","1000 MB","10000 MB","1024 MB","12 GB","128 MB","136 MB","1500 MB","16 GB","16 MB","2 GB","2 MB","2.5 GB","2000 MB","2048 MB","2500 MB","256 MB","3 GB","30 MB","3072 MB","32 GB","32 MB","4 GB","4 MB","4096 MB","512 MB","6 MB","64 GB","64 MB","8 GB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'nucleos_procesador',
            [
                'type' => 'varchar',
                'label' => 'Núcleos del procesador',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1","10","12","14","16","2","3","4","6","8"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'memoria_interna_max',
            [
                'type' => 'varchar',
                'label' => 'Memoria interna máxima',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1000 GB","1024 GB","1024 MB","12 GB","128 GB","128 MB","1536 GB","1536 MB","1540 GB","16 GB","192 GB","2000 GB","2048 MB","24 GB","256 MB","3 GB","3000 MB","32 GB","4 GB","4 MB","40 GB","4000 GB","4096 MB","48 GB","512 MB","6000 GB","64 GB","64 MB","8 GB","8000 GB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'memoria_interna',
            [
                'type' => 'varchar',
                'label' => 'Memoria interna',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1 GB","1 MB","1000 MB","10000 MB","1024 MB","12 GB","128 MB","136 MB","1500 MB","16 GB","16 MB","2 GB","2 MB","2.5 GB","2000 MB","2048 MB","2500 MB","256 MB","3 GB","30 MB","3072 MB","32 GB","32 MB","4 GB","4 MB","4096 MB","512 MB","6 MB","64 GB","64 MB","8 GB","6 GB","8192 MB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'forma_pantalla',
            [
                'type' => 'varchar',
                'label' => 'Forma de la pantalla',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Curva","Plana"))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'factor_forma',
            [
                'type' => 'varchar',
                'label' => 'Factor de forma',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("1U","Ambidiestro","Bala","Barra","Cable","Compacto","Concha","Convertible (Carpeta)","Cubo","Deslizar","Diestro","Domo","Escritorio","Esférico","Full Tower","Half-Height/Half-Length (HH/HL)","Micro Tower","Midi Tower","Mini Tower","Mochila","Montaje en rack o Montaje en bastidor","Montaje en rack/Torre o Montaje en bastidor/Torre","OCP 3.0","Otro","Perfil bajo (Slimline)","Pizarra","Plaza","Rectángulo","Sin tapa","Tapa","Torreta","Tower","VESA 75x75mm; VESA 100x100mm","Zurdo"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'color_product',
            [
                'type' => 'varchar',
                'label' => 'Color del producto',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("Gris","Aluminio","Amarillo","Azul","Beige","Blanco","Borgoña","Bronce","Café","Camuflaje","Carbono","Chocolate","Cian","Aguamarina","Menta","Multicolor","Coral","Grafito","Lavanda","Lila","Magenta","Marfil","Marina","Metálico","Negro","Naranja","Oro","Plata","Platino","Púrpura","Rojo","Rosa","Terracota","Translúcido","Transparente","Turquesa","Verde","Violeta"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cap_almacenaje',
            [
                'type' => 'varchar',
                'label' => 'Capacidad total de almacenaje',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("0.7 GB","1000 GB","1024 GB","120 GB","1240 GB","1256 GB","128 GB","1512 GB","2000 GB","240 GB","256 GB","4000 GB","480 GB","512 GB","64 GB","8 GB"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pantalla',
            [
                'type' => 'varchar',
                'label' => 'Pantalla',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("20","23.8","28"))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'disco_duro',
            [
                'type' => 'varchar',
                'label' => 'Capacidad de Disco Duro',
                'input' => 'multiselect',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'sort_order' => '30',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'default' => null,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'unique' => false,
                'apply_to' => '',
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'option' => array('values' => array("0.7 GB","10 GB","1000 GB","10000 GB","1024 GB","1024 MB","120 GB","1200 GB","12000 GB","128 GB","128 MB","14000 GB","16 GB","16 MB","16000 GB","16000 MB","18000 GB","1920 GB","2 GB","2 MB","2000 GB","240 GB","2400 GB","250 GB","256 GB","256 MB","3000 GB","32 GB","32 MB","320 GB","3840 GB","4 GB","4 MB","4000 GB","4000 MB","4096 GB","480 GB","500 GB","5000 GB","512 GB","512 MB","6000 GB","6144 GB","64 GB","7.8 MB","8 GB","8 MB","8 TB","8000 GB","8000 MB","8192 GB","960 GB"))
            ]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [   
        ];
    }

    /**
     * @inheritdoc
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        /**
         * This internal Magento method, that means that some patches with time can change their names,
         * but changing name should not affect installation process, that's why if we will change name of the patch
         * we will add alias here
         */
        return [];
    }
}