<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Unilane\SyncProducts\Controller\Adminhtml\Products;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class ValidateApi
 */
class ImportProducts extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    private $productFactory;
    private $resultJsonFactory;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context     
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('entro');
        $response = $this->resultJsonFactory->create();
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
                $response->setData(['test' => 'La importacion fue correcta']); 
            } catch (Exception $e) {
                $response->setData(['test' => 'hubo un error']);
            }           
        }
        else{
            $response->setData(['test' => 'el archivo esta vacio']);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(['test' => 'el archivo esta vacio']);
    }
}
