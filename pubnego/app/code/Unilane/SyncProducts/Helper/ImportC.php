<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilane\SyncProducts\Helper;

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Adminhtml Catalog helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ImportC 
{
    private $productFactory;
    private $productRepository;
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context     
     */
    public function __construct(
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;

    }
    /**
     * Set Custom Attribute Tab Block Name for Category Edit
     *
     * @param string $attributeTabBlock
     * @return $this
     */
    public function importProduct()
    {
        
        $data         = file_get_contents("C:\Users\luis.olivarria\Desktop\productsjson\dataPrueba.json");
        $dataCatagory = file_get_contents("C:\Users\luis.olivarria\Desktop\productsjson\arregloCategorias.json");
        if($data){
            try {
                $products  = json_decode($data, true);
                $categorys = json_decode($dataCatagory, true);
                foreach($products as $product){
                    $items = $this->productFactory->create();
                    $sumaExistencia = 0;
                    $pro = $product['existencia'];
                    foreach($pro as $existencia){
                        $sumaExistencia += $existencia;
                    }
                    $items->setAttributeSetId(4);
                    $items->setName($product['nombre']);
                    $items->setSku($product['clave']);
                    $items->setPrice($product['precio']);
                    $items->setVisibility(4);
                    $items->setStatus(1);
                    $items->setTypeId('simple');
                    $items->setTaxClassId(1);
                    $items->setWebsiteIds([1]);
                    $items->setProductPageType('custom');
                    //ICECAT
                    $items->setGtinEan($product['upc']);
                    $items->setBrandName($product['marca']);
                    $items->setProductCode($product['numParte']);                    
                    $items->setCategoryIds([
                        2,24,31,243
                    ]);
                    $items->setStockData(
                        array( 
                        'use_config_manage_stock' => 1,                       
                        'manage_stock' => 1,
                        'is_in_stock' => 1,   
                        'qty' => $sumaExistencia
                        )
                    );
                    $items->save();
                } 
            } catch (Exception $e) {
                // No Ip found in database
                $data = [];
            }           
        }
    }
    
    public function updateStock() {
        $data = file_get_contents("C:\Users\luis.olivarria\Desktop\productsjson\dataPrueba.json");
        try{
            $products  = json_decode($data, true);
            foreach($products as $product){
                //$items = $this->productFactory->create();
                $sumaExistencia = 0;
                $pro = $product['existencia'];
                foreach($pro as $existencia){
                    $sumaExistencia += $existencia;
                }
                $producto = $this->productRepository->get($product['clave']);
                if($producto){
                    $producto->setStockData(
                        array( 
                        'use_config_manage_stock' => 1,                       
                        'manage_stock' => 1,
                        'is_in_stock' => 1,   
                        'qty' => $sumaExistencia
                        )
                    );     
                    $this->productRepository->save($producto);
                }               
            }
        }catch (Exception $e) {
            $data = [];
        } 
    }
}
