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
class UpdateStock extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    private $productFactory;
    private $resultJsonFactory;
    private $productRepository;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context     
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
    }

    /**
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $data = file_get_contents("C:\Users\luis.olivarria\Desktop\productsjson\dataPrueba.json");
        try{
            $products  = json_decode($data, true);
            foreach($products as $product){
                $items = $this->productFactory->create();
                $sumaExistencia = 0;
                $pro = $product['existencia'];
                foreach($pro as $existencia){
                    $sumaExistencia += $existencia;
                }
                $producto = $this->productRepository->get($product['clave']);
                if($producto){
                    foreach($producto->_data as $key => $valor){
                        if($key == "quantity_and_stock_status"){
                            $valor['qty'] = $sumaExistencia;
                            $producto->setData($key, $valor);
                        }
                    }                           
                    $this->productRepository->save($producto);
                }               
            }
        }catch (Exception $e) {
            $data = [];
        } 
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData(['test' => 'el archivo esta vacio']);
    }
}
