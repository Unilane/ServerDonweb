<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Unilane\SyncProducts\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Bootstrap;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
/**
 * @api
 */
class Import extends \Magento\Config\Block\System\Config\Form\Field
{
    private $productFactory;
    private $productRepository;
    private File $file;
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        ProductRepositoryInterface            $productRepository,
        File                                  $file     
    )
    {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->file = $file;
    }
    /**
     * @inheritdoc
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {       
        //$connect = $this->connectCT();
        //if($connect){
            $this->importProducts();
        //}                
    }
    public function importProducts(){    
	try{
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/productodata.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info("Actualizacion de categorias");
            $logger->info("");                
            //CT
            $dataCt  = file_get_contents("/home/imagended/productos.json");        
            $productsData  = json_decode($dataCt, true);
            //$products  = json_decode($data, true);
            $i = 1;
            foreach($productsData as $product){
                $producto = $this->productRepository->get($product['clave']);
                if($producto){
                    $nombreCategoria = $product['subcategoria'];               
                    if($nombreCategoria == "Cámaras de Video Vigilancia"){
                        $producto->setCategoryIds([
                            2,63,209
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Cámara bala análogica"){
                        $producto->setCategoryIds([
                            2,63,209
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cámaras domo analógicas"){
                        $producto->setCategoryIds([
                            2,63,209
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cámaras PTZ analógicas"){
                        $producto->setCategoryIds([
                            2,63,209
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }               
    
                    if($nombreCategoria == "Cables USB"){
                        $producto->setCategoryIds([
                            2,36,252,245
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;    
                    }
                    if($nombreCategoria == "Adaptadores de Energía"){
                        $producto->setCategoryIds([
                            2,36,254,243
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;    
                    }
                    if($nombreCategoria == "Inversores de Energia"){
                        $producto->setCategoryIds([
                            2,36,254,243
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;    
                    }
                    if($nombreCategoria == "Reemplazos"){
                        $producto->setCategoryIds([
                            2,67,234,242
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Supresores"){
                        $producto->setCategoryIds([
                            2,67,261,239
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;     
                    }
                    if($nombreCategoria == "Regletas y Multicontactos"){
                        $producto->setCategoryIds([
                            2,67,261,238
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Estaciones de Carga"){
                        $producto->setCategoryIds([
                            2,67,261,278
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }  
                    if($nombreCategoria == "Reguladores"){
                        $producto->setCategoryIds([
                            2,67,236
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "No Breaks y UPS"){
                        $producto->setCategoryIds([
                            2,67,235
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    } 
                    if($nombreCategoria == "Baterías"){
                        $producto->setCategoryIds([
                            2,67,234
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Barra de Contactos"){                    
                        $producto->setCategoryIds([
                            2,67,233
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }    
                    if($nombreCategoria == "Tarjetas de Acceso"){
                        $producto->setCategoryIds([
                            2,57,223
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para seguridad"){
                        $producto->setCategoryIds([
                            2,57,217
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Soportes para Video Vigilancia"){
                        $producto->setCategoryIds([
                            2,63,262,215
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Monitores para Video Vigilancia"){
                        $producto->setCategoryIds([
                            2,63,262,213
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Kits de Video Vigilancia"){
                        $producto->setCategoryIds([
                            2,63,212
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Grabadoras Digitales"){
                        $producto->setCategoryIds([
                            2,63,211
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Grabadores analógicos"){
                        $producto->setCategoryIds([
                            2,63,211
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Fuentes de Poder para Video Vigilancia"){
                        $producto->setCategoryIds([
                            2,63,262,210
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cámaras"){
                        $producto->setCategoryIds([
                            2,40,125
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables y conectores"){
                        $producto->setCategoryIds([
                            2,63,262,208
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Inyectores PoE"){
                        $producto->setCategoryIds([
                            2,62,206
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Antenas"){
                        $producto->setCategoryIds([
                            2,62,205
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para Racks"){
                        $producto->setCategoryIds([
                            2,62,204
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Racks Modulo"){
                        $producto->setCategoryIds([
                            2,62,204
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Switches"){
                        $producto->setCategoryIds([
                            2,62,203
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Amplificadores Wifi"){
                        $producto->setCategoryIds([
                            2,62,202
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Extensores de Red"){
                        $producto->setCategoryIds([
                            2,62,202
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Hub y Concentadores Wifi"){
                        $producto->setCategoryIds([
                            2,62,202
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Routers"){
                        $producto->setCategoryIds([
                            2,62,201
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Access Points"){
                        $producto->setCategoryIds([
                            2,62,200
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para Cables"){
                        $producto->setCategoryIds([
                            2,36,252,283
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;      
                    }
                    if($nombreCategoria == "Herramientas para red"){
                        $producto->setCategoryIds([
                            2,62,197
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios de Redes"){
                        $producto->setCategoryIds([
                            2,62,197
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Bobinas"){
                        $producto->setCategoryIds([
                            2,36,252,280
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Herramientas"){
                        $producto->setCategoryIds([
                            2,281,282
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Jacks"){ //TODO
                        $producto->setCategoryIds([
                            2,36,252,284
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Convertidor de medios"){
                        $producto->setCategoryIds([
                            2,36,103
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Transceptores"){
                        $producto->setCategoryIds([
                            2,36,254
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Consumibles POS"){
                        $producto->setCategoryIds([
                            2,61,196
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Etiquetas"){
                        $producto->setCategoryIds([
                            2,61,196
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Cables POS"){
                        $producto->setCategoryIds([
                            2,36,252,285
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Digitalizadores de Firmas"){
                        $producto->setCategoryIds([
                            2,61,260,194
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Terminales POS"){
                        $producto->setCategoryIds([
                            2,61,260,193
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Monitores POS"){
                        $producto->setCategoryIds([
                            2,61,260,192
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Lectores de Códigos de Barras"){
                        $producto->setCategoryIds([
                            2,61,191
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Impresoras POS"){
                        $producto->setCategoryIds([
                            2,61,190
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cajones de Dinero"){
                        $producto->setCategoryIds([
                            2,61,189
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;      
                    }
                    if($nombreCategoria == "Kit Punto de Venta"){
                        $producto->setCategoryIds([
                            2,61,260,188
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Pcs de Escritorio Gaming"){
                        $producto->setCategoryIds([
                            2,60,187
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Monitores Gaming"){
                        $producto->setCategoryIds([
                            2,60,186
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Laptops Gaming"){
                        $producto->setCategoryIds([
                            2,60,185
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;      
                    }
                    if($nombreCategoria == "Tarjetas de Video Gaming"){
                        $producto->setCategoryIds([
                            2,60,185
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Tarjetas de Video"){
                        $producto->setCategoryIds([
                            2,60,185
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Controles"){
                        $producto->setCategoryIds([
                            2,41,286
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Consolas y Video Juegos"){
                        $producto->setCategoryIds([
                            2,59,287
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Controles Gaming"){
                        $producto->setCategoryIds([
                            2,59,183
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Pilas"){
                        $producto->setCategoryIds([
                            2,38,288
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Escritorio Gaming"){
                        $producto->setCategoryIds([
                            2,59,182
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Sillas Gaming"){
                        $producto->setCategoryIds([
                            2,59,181
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Motherboards Gaming"){
                        $producto->setCategoryIds([
                            2,60,180
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Gabinetes Gaming"){
                        $producto->setCategoryIds([
                            2,60,179
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Fuentes de Poder Gaming"){
                        $producto->setCategoryIds([
                            2,60,178
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Kits de Teclado y Mouse Gaming"){
                        $producto->setCategoryIds([
                            2,59,177
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Teclados Gaming"){
                        $producto->setCategoryIds([
                            2,59,176
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Mouse Gaming"){
                        $producto->setCategoryIds([
                            2,59,175
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Mouse Pads Gaming"){
                        $producto->setCategoryIds([
                            2,59,175
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Diademas Gaming"){
                        $producto->setCategoryIds([
                            2,59,174
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Sensores"){
                        $producto->setCategoryIds([
                            2,57,169
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Sensores para Vídeo Vigilancia"){
                        $producto->setCategoryIds([
                            2,57,169,289
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Sensores Wifi"){
                        $producto->setCategoryIds([
                            2,57,169,163
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Paneles para Alarma"){
                        $producto->setCategoryIds([
                            2,57,168
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Adaptadores USB"){
                        $producto->setCategoryIds([
                            2,36,254,106
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para PCs"){
                        $producto->setCategoryIds([
                            2,35,290
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Kits para Teclado y Mouse"){ //todo
                        $producto->setCategoryIds([
                            2,32,291
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Acceso"){
                        $producto->setCategoryIds([
                            2,57,167
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cámara Inteligentes"){
                        $producto->setCategoryIds([
                            2,56,165
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cerraduras"){
                        $producto->setCategoryIds([
                            2,56,164
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Timbres"){
                        $producto->setCategoryIds([
                            2,56,164
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Contactos Inteligentes Wifi"){
                        $producto->setCategoryIds([
                            2,56,160
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Control Inteligente"){
                        $producto->setCategoryIds([
                            2,41,286
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Iluminación"){
                        $producto->setCategoryIds([
                            2,56,161
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Interruptores Wifi"){
                        $producto->setCategoryIds([
                            2,41,292
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Control de Acceso"){
                        $producto->setCategoryIds([
                            2,55,159
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Checadores"){
                        $producto->setCategoryIds([
                            2,57,167,293
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Lector de Huella"){
                        $producto->setCategoryIds([
                            2,57,167,294
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Equipo"){
                        $producto->setCategoryIds([
                            2,54,156
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Termómetros"){
                        $producto->setCategoryIds([
                            2,54,296
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Desinfectantes"){
                        $producto->setCategoryIds([
                            2,54,155
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
    
                    }
                    if($nombreCategoria == "Caretas"){
                        $producto->setCategoryIds([
                            2,54,154
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cubrebocas"){
                        $producto->setCategoryIds([
                            2,54,154
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Aspiradoras"){
                        $producto->setCategoryIds([
                            2,53,257,153
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Microondas"){
                        $producto->setCategoryIds([
                            2,53,257,152
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Aires Acondicionados"){
                        $producto->setCategoryIds([
                            2,53,257,151
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Pantallas Profesionales"){
                        $producto->setCategoryIds([
                            2,53,150
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Video Conferencia"){
                        $producto->setCategoryIds([
                            2,53,149
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Análogos"){
                        $producto->setCategoryIds([
                            2,53,297,298
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Central Telefónica"){
                        $producto->setCategoryIds([
                            2,53,297,299
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Teléfonos Analógicos"){
                        $producto->setCategoryIds([
                            2,53,148,300
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Teléfonos Digitales"){
                        $producto->setCategoryIds([
                            2,53,148,301
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Teléfonos IP"){
                        $producto->setCategoryIds([
                            2,53,148,302
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Teléfonos para Hogar"){
                        $producto->setCategoryIds([
                            2,53,148,303
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Teléfonos SIP"){
                        $producto->setCategoryIds([
                            2,53,148,304
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Escritorio de Oficina"){
                        $producto->setCategoryIds([
                            2,53,258,147
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Ergonomia"){
                        $producto->setCategoryIds([
                            2,281,305
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Sillas de Oficina"){
                        $producto->setCategoryIds([
                            2,53,257,146
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Almacenamiento Óptico"){
                        $producto->setCategoryIds([
                            2,37,306
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Quemadores DVD y BluRay"){
                        $producto->setCategoryIds([
                            2,52,143,145
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Accesorios de Papeleria"){
                        $producto->setCategoryIds([
                            2,52,143
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Articulos de Escritura"){
                        $producto->setCategoryIds([
                            2,52,143
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Basico de Papeleria"){
                        $producto->setCategoryIds([
                            2,52,143
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cuadernos"){
                        $producto->setCategoryIds([
                            2,52,143
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Papelería"){
                        $producto->setCategoryIds([
                            2,52,143
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Plumas Interactivas"){
                        $producto->setCategoryIds([
                            2,52,143
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Mantenimiento"){
                        $producto->setCategoryIds([
                            2,44,142
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Refacciones"){
                        $producto->setCategoryIds([
                            2,44,141
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cabezales"){
                        $producto->setCategoryIds([
                            2,44,140
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para impresoras"){
                        $producto->setCategoryIds([
                            2,44,139
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cintas"){
                        $producto->setCategoryIds([
                            2,43,138
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Papel"){
                        $producto->setCategoryIds([
                            2,43,137
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Tóners"){
                        $producto->setCategoryIds([
                            2,43,136
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cartuchos"){
                        $producto->setCategoryIds([
                            2,43,135
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Plotters"){
                        $producto->setCategoryIds([
                            2,42,134
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Rotuladores"){
                        $producto->setCategoryIds([
                            2,42,133
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
    
                    }
                    if($nombreCategoria == "Escaner"){
                        $producto->setCategoryIds([
                            2,42,132
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Multifuncionales"){
                        $producto->setCategoryIds([
                            2,42,131
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Impresoras"){
                        $producto->setCategoryIds([
                            2,42,130
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Soporte para TV"){
                        $producto->setCategoryIds([
                            2,41,129
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Soporte Videowall"){
                        $producto->setCategoryIds([
                            2,41,129
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Soportes"){
                        $producto->setCategoryIds([
                            2,41,129
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Soporte para Proyector"){
                        $producto->setCategoryIds([
                            2,41,128
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Limpieza"){
                        $producto->setCategoryIds([
                            2,41,127
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para Camaras"){
                        $producto->setCategoryIds([
                            2,40,125
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Lentes"){
                        $producto->setCategoryIds([
                            2,40,124
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Micrófonos"){
                        $producto->setCategoryIds([
                            2,39,122
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Home Theaters"){
                        $producto->setCategoryIds([
                            2,39,120
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Bocina Portatil"){
                        $producto->setCategoryIds([
                            2,39,120
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Bocinas"){
                        $producto->setCategoryIds([
                            2,39,120
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Bocinas Gaming"){
                        $producto->setCategoryIds([
                            2,39,120
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Bocinas y Bocinas Portátiles"){
                        $producto->setCategoryIds([
                            2,39,120
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Base Diademas"){
                        $producto->setCategoryIds([
                            2,39,255,119
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Diademas"){
                        $producto->setCategoryIds([
                            2,39,255,119
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria =="Audífonos"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Audífonos para Apple"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Auriculares"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Earbuds"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "In Ears"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "On Ear"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "on-ear"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Reproductores MP3"){
                        $producto->setCategoryIds([
                            2,39,255,118
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Patinetas"){
                        $producto->setCategoryIds([
                            2,38,117
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Streaming"){
                        $producto->setCategoryIds([
                            2,38,307
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Televisiones"){
                        $producto->setCategoryIds([
                            2,38,116
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Pantallas de Proyección"){
                        $producto->setCategoryIds([
                            2,38,115
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Proyectores"){
                        $producto->setCategoryIds([
                            2,38,115
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Power banks"){
                        $producto->setCategoryIds([
                            2,38,114
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Smartwatch"){
                        $producto->setCategoryIds([
                            2,38,113
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cables Lightning"){
                        $producto->setCategoryIds([
                            2,36,252,308
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;      
                    }
                    if($nombreCategoria == "Cargadores"){
                        $producto->setCategoryIds([
                            2,38,256,112
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para Celulares"){
                        $producto->setCategoryIds([
                            2,38,256,309
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Bases"){
                        $producto->setCategoryIds([
                            2,35,91
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Celulares"){
                        $producto->setCategoryIds([
                            2,38,256,111
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Equipo para Celulares"){
                        $producto->setCategoryIds([
                            2,38,256,310
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Transmisores"){
                        $producto->setCategoryIds([
                            2,38,311
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Gabinetes para Discos Duros"){
                        $producto->setCategoryIds([
                            2,37,110
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Memorias Flash"){
                        $producto->setCategoryIds([
                            2,37,109
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Memorias USB"){
                        $producto->setCategoryIds([
                            2,37,109
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Adaptadores para Disco Duro"){
                        $producto->setCategoryIds([
                            2,36,254,107
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Almacenamiento Externo"){
                        $producto->setCategoryIds([
                            2,37,108
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Discos Duros"){
                        $producto->setCategoryIds([
                            2,37,108
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Discos Duros Externos"){
                        $producto->setCategoryIds([
                            2,37,108
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "SSD"){
                        $producto->setCategoryIds([
                            2,37,108
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cables de Alimentación"){
                        $producto->setCategoryIds([
                            2,36,252,99
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables de Audio"){
                        $producto->setCategoryIds([
                            2,36,252,100
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables de Video"){
                        $producto->setCategoryIds([
                            2,36,252,101
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables Serial"){
                        $producto->setCategoryIds([
                            2,36,252,102
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cables de Red"){
                        $producto->setCategoryIds([
                            2,36,252,199
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Cables Coaxial"){
                        $producto->setCategoryIds([
                            2,36,252,271
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables Displayport"){
                        $producto->setCategoryIds([
                            2,36,252,272
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables DVI"){
                        $producto->setCategoryIds([
                            2,36,252,273
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables HDMI"){
                        $producto->setCategoryIds([
                            2,36,252,274
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables KVM"){
                        $producto->setCategoryIds([
                            2,36,252,275
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Cables VGA"){
                        $producto->setCategoryIds([
                            2,36,252,276
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    
                    if($nombreCategoria == "Cables de Energía"){
                        $producto->setCategoryIds([
                            2,36,252,263
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Adaptadores para Video"){
                        $producto->setCategoryIds([
                            2,36,254,104
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores USB"){
                        $producto->setCategoryIds([
                            2,36,254,106
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores HDMI"){
                        $producto->setCategoryIds([
                            2,36,254,264
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores de Ethernet"){
                        $producto->setCategoryIds([
                            2,36,254,265
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores Displayport"){
                        $producto->setCategoryIds([
                            2,36,254,266
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores para Apple"){
                        $producto->setCategoryIds([
                            2,36,254,267
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores para Red"){
                        $producto->setCategoryIds([
                            2,36,254,198
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores para Audio"){
                        $producto->setCategoryIds([
                            2,36,254,268
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores USB para Video"){
                        $producto->setCategoryIds([
                            2,36,254,269
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores USB Red"){
                        $producto->setCategoryIds([
                            2,36,254,270
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "Fundas y Maletines"){
                        $producto->setCategoryIds([
                            2,35,249
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Mochila Gaming"){
                        $producto->setCategoryIds([
                            2,40,123
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if( $nombreCategoria == "Mochilas y Maletines"){
                        $producto->setCategoryIds([
                            2,40,123
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Fundas Laptops"){
                        $producto->setCategoryIds([
                            2,35,249
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Fundas para Tablets"){
                        $producto->setCategoryIds([
                            2,35,249
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Protectores para Tablets"){
                        $producto->setCategoryIds([
                            2,35,249
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Filtro de Privacidad"){
                        $producto->setCategoryIds([
                            2,35,94
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Concentradores Hub"){
                        $producto->setCategoryIds([
                            2,36,254,243
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Docking Station"){
                        $producto->setCategoryIds([
                            2,35,93
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Candados Laptops"){
                        $producto->setCategoryIds([
                            2,35,92
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Bases Enfriadoras"){
                        $producto->setCategoryIds([
                            2,35,91
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para Laptops"){
                        $producto->setCategoryIds([
                            2,35,90
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores para Laptops"){
                        $producto->setCategoryIds([
                            2,36,254,312
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Baterias Laptops"){
                        $producto->setCategoryIds([
                            2,35,90
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Pantallas Laptops"){
                        $producto->setCategoryIds([
                            2,35,90
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Teclados Laptops"){
                        $producto->setCategoryIds([
                            2,34,89
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Tarjetas de Sonido"){
                        $producto->setCategoryIds([
                            2,34,89
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Tarjetas Paralelas"){
                        $producto->setCategoryIds([
                            2,34,89
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Tarjetas Seriales"){
                        $producto->setCategoryIds([
                            2,34,89
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Motherboards"){
                        $producto->setCategoryIds([
                            2,34,88
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Microprocesadores"){
                        $producto->setCategoryIds([
                            2,34,87
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Memorias RAM"){
                        $producto->setCategoryIds([
                            2,34,86
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Lectores de Memorias"){
                        $producto->setCategoryIds([
                            2,34,85
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Gabinetes para Computadoras"){
                        $producto->setCategoryIds([
                            2,34,84
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Fuentes de Poder"){
                        $producto->setCategoryIds([
                            2,34,83
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Enfriamiento y Ventilación"){
                        $producto->setCategoryIds([
                            2,34,82
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Webcams"){
                        $producto->setCategoryIds([
                            2,33,81
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Soporte de Monitor"){
                        $producto->setCategoryIds([
                            2,33,80
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Soporte Laptops"){
                        $producto->setCategoryIds([
                            2,35,97
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Soportes para PCs"){
                        $producto->setCategoryIds([
                            2,35,97
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Monitores"){
                        $producto->setCategoryIds([
                            2,33,79
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Monitores Curvos"){
                        $producto->setCategoryIds([
                            2,33,79
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++; 
                    }
                    if($nombreCategoria == "Teclados"){
                        $producto->setCategoryIds([
                            2,32,77
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Mouse"){
                        $producto->setCategoryIds([
                            2,32,76
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Mouse Pads"){
                        $producto->setCategoryIds([
                            2,32,76
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "iPad"){
                        $producto->setCategoryIds([
                            2,31,75
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Soporte para Tablets"){
                        $producto->setCategoryIds([
                            2,31,75
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Tabletas"){
                        $producto->setCategoryIds([
                            2,31,75
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                    }
                    if($nombreCategoria == "Workstations de Escritorio"){
                        $producto->setCategoryIds([
                            2,31,74
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Workstations Gaming"){
                        $producto->setCategoryIds([
                            2,31,74
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Workstations Móviles"){
                        $producto->setCategoryIds([
                            2,31,74
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Mini PC"){
                        $producto->setCategoryIds([
                            2,31,73
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
    
                    if($nombreCategoria == "PCs de Escritorio"){
                        $producto->setCategoryIds([
                            2,31,70
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Laptops"){
                        $producto->setCategoryIds([
                            2,31,69
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "MacBook"){
                        $producto->setCategoryIds([
                            2,31,69
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "All In One"){
                        $producto->setCategoryIds([
                            2,31,68
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "iMac"){
                        $producto->setCategoryIds([
                            2,31,68
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para Apple"){
                        $producto->setCategoryIds([
                            2,38,256,309
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores Inalámbricos"){
                        $producto->setCategoryIds([
                            2,36,254,315
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Aires Acondicionados para Centros"){
                        $producto->setCategoryIds([
                            2,53,257,151
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }     
                    if($nombreCategoria == "Bancos de Batería"){
                        $producto->setCategoryIds([
                            2,67,234,241
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }       
                    if($nombreCategoria == "Micro y Mini Componentes"){
                        $producto->setCategoryIds([
                            2,34,89
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para Video Vigilancia"){
                        $producto->setCategoryIds([
                            2,63,262,210
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }    
                    if($nombreCategoria == "Accesorios para Telefonia"){
                        $producto->setCategoryIds([
                            2,53,148
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }      
                    if($nombreCategoria == "Adaptadores DVI"){
                        $producto->setCategoryIds([
                            2,36,254,313
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores VGA"){
                        $producto->setCategoryIds([
                            2,36,254,314
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Tarjetas para Red"){
                        $producto->setCategoryIds([
                            2,34,89
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Accesorios para pantallas"){
                        $producto->setCategoryIds([
                            2,35,94
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Adaptadores Tipo C"){
                        $producto->setCategoryIds([
                            2,36,254,317
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Over Ear"){
                        $producto->setCategoryIds([
                            2,39,255,119
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Tinta"){
                        $producto->setCategoryIds([
                            2,43,135
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;  
                    }
                    if($nombreCategoria == "Tambor"){
                        $producto->setCategoryIds([
                            2,43,135
                        ]);
                        $this->productRepository->save($producto);
                        $logger->info($i.".-"." "."sku actualizado"." ".$producto->getSku());
                        $i++;
                          
                    }
                }
            }
        }             
        catch (Exception $e) {
            $data = [];
        }
    }
    public function connectCT(){
        //FTP
        // Configuración de la conexión FTP
        $ftp_server = '216.70.82.104'; // Reemplaza con la dirección del servidor FTP
        $ftp_user = 'HMO0410'; // Reemplaza con tu nombre de usuario FTP
        $ftp_pass = 'Z6v3Bh7*k@lLcXTGR0!P'; // Reemplaza con tu contraseña FTP

        // Ruta al archivo JSON en el servidor FTP
        $remote_file = 'catalogo_xml/productos.json'; // Reemplaza con la ruta de tu archivo JSON

        // Ruta local donde guardarás el archivo JSON descargado
        $local_file = '/home/imagended/productos.json'; // Reemplaza con la ruta de tu elección en tu servidor local

        // Establece la conexión FTP
        $ftp_conn = ftp_connect($ftp_server);
        if (!$ftp_conn) {
            die('No se pudo conectar al servidor FTP.');
        }

        // Inicia sesión en el servidor FTP
        if (ftp_login($ftp_conn, $ftp_user, $ftp_pass)) {
            // Descarga el archivo JSON
            if (ftp_get($ftp_conn, $local_file, $remote_file, FTP_ASCII, 0)) {
                return true;
            } else {
                return false;
            }

            // Cierra la conexión FTP
            ftp_close($ftp_conn);
        } else {
            echo "Error al iniciar sesión en el servidor FTP.";
        }
        //FIN FTP
    }
    
    public function redondeo($precio_desgloce){
        if(!isset($precio_desgloce[1])){
            return $precio_desgloce[0].".00";
        }
        #region switch para redondear centavos
        switch($precio_desgloce[1]){
            //00 
            case "00":
                return $precio_desgloce[0].".00";
            break;
            case "01":
                return $precio_desgloce[0].".00";
            break;
            case "02":
                return $precio_desgloce[0].".00";
            break;
            case "03":
                return $precio_desgloce[0].".00";
            break;
            case "04":
                return $precio_desgloce[0].".00";
            break;
            case "05":
                return $precio_desgloce[0].".10";
            break;
            case "06":
                return $precio_desgloce[0].".10";
            break;
            case "07":
                return $precio_desgloce[0].".10";
            break;
            case "08":
                return $precio_desgloce[0].".10";
            break;
            case "09":
                return $precio_desgloce[0].".10";
            break;
            case "10":
                return $precio_desgloce[0].".10";
            break;
            //10 >
            case "10":
                return $precio_desgloce[0].".10";
            break;
            case "11":
                return $precio_desgloce[0].".10";
            break;
            case "12":
                return $precio_desgloce[0].".10";
            break;
            case "13":
                return $precio_desgloce[0].".10";
            break;
            case "14":
                return $precio_desgloce[0].".10";
            break;
            case "15":
                return $precio_desgloce[0].".20";
            break;
            case "16":
                return $precio_desgloce[0].".20";
            break;
            case "17":
                return $precio_desgloce[0].".20";
            break;
            case "18":
                return $precio_desgloce[0].".20";
            break;
            case "19":
                return $precio_desgloce[0].".20";
            break;
            case "20":
                return $precio_desgloce[0].".20";
            break;
            //20
            case "20":
                return $precio_desgloce[0].".20";
            break;
            case "21":
                return $precio_desgloce[0].".20";
            break;
            case "22":
                return $precio_desgloce[0].".20";
            break;
            case "23":
                return $precio_desgloce[0].".20";
            break;
            case "24":
                return $precio_desgloce[0].".20";
            break;
            case "25":
                return $precio_desgloce[0].".30";
            break;
            case "26":
                return $precio_desgloce[0].".30";
            break;
            case "27":
                return $precio_desgloce[0].".30";
            break;
            case "28":
                return $precio_desgloce[0].".30";
            break;
            case "29":
                return $precio_desgloce[0].".30";
            break;
            case "30":
                return $precio_desgloce[0].".30";
            break;
            //30
            case "30":
                return $precio_desgloce[0].".30";
            break;
            case "31":
                return $precio_desgloce[0].".30";
            break;
            case "32":
                return $precio_desgloce[0].".30";
            break;
            case "33":
                return $precio_desgloce[0].".30";
            break;
            case "34":
                return $precio_desgloce[0].".30";
            break;
            case "35":
                return $precio_desgloce[0].".40";
            break;
            case "36":
                return $precio_desgloce[0].".40";
            break;
            case "37":
                return $precio_desgloce[0].".40";
            break;
            case "38":
                return $precio_desgloce[0].".40";
            break;
            case "39":
                return $precio_desgloce[0].".40";
            break;
            case "40":
                return $precio_desgloce[0].".40";
            break;
            //40
            case "40":
                return $precio_desgloce[0].".40";
            break;
            case "41":
                return $precio_desgloce[0].".40";
            break;
            case "42":
                return $precio_desgloce[0].".40";
            break;
            case "43":
                return $precio_desgloce[0].".40";
            break;
            case "44":
                return $precio_desgloce[0].".40";
            break;
            case "45":
                return $precio_desgloce[0].".50";
            break;
            case "46":
                return $precio_desgloce[0].".50";
            break;
            case "47":
                return $precio_desgloce[0].".50";
            break;
            case "48":
                return $precio_desgloce[0].".50";
            break;
            case "49":
                return $precio_desgloce[0].".50";
            break;
            case "50":
                return $precio_desgloce[0].".50";
            break;
            //50
            case "50":
                return $precio_desgloce[0].".50";
            break;
            case "51":
                return $precio_desgloce[0].".50";
            break;
            case "52":
                return $precio_desgloce[0].".50";
            break;
            case "53":
                return $precio_desgloce[0].".50";
            break;
            case "54":
                return $precio_desgloce[0].".50";
            break;
            case "55":
                return $precio_desgloce[0].".60";
            break;
            case "56":
                return $precio_desgloce[0].".60";
            break;
            case "57":
                return $precio_desgloce[0].".60";
            break;
            case "58":
                return $precio_desgloce[0].".60";
            break;
            case "59":
                return $precio_desgloce[0].".60";
            break;
            case "60":
                return $precio_desgloce[0].".60";
            break;
            //60
            case "60":
                return $precio_desgloce[0].".60";
            break;
            case "61":
                return $precio_desgloce[0].".60";
            break;
            case "62":
                return $precio_desgloce[0].".60";
            break;
            case "63":
                return $precio_desgloce[0].".60";
            break;
            case "64":
                return $precio_desgloce[0].".60";
            break;
            case "65":
                return $precio_desgloce[0].".70";
            break;
            case "66":
                return $precio_desgloce[0].".70";
            break;
            case "67":
                return $precio_desgloce[0].".70";
            break;
            case "68":
                return $precio_desgloce[0].".70";
            break;
            case "69":
                return $precio_desgloce[0].".70";
            break;
            case "70":
                return $precio_desgloce[0].".70";
            break;
            //70
            case "70":
                return $precio_desgloce[0].".70";
            break;
            case "71":
                return $precio_desgloce[0].".70";
            break;
            case "72":
                return $precio_desgloce[0].".70";
            break;
            case "73":
                return $precio_desgloce[0].".70";
            break;
            case "74":
                return $precio_desgloce[0].".70";
            break;
            case "75":
                return $precio_desgloce[0].".80";
            break;
            case "76":
                return $precio_desgloce[0].".80";
            break;
            case "77":
                return $precio_desgloce[0].".80";
            break;
            case "78":
                return $precio_desgloce[0].".80";
            break;
            case "79":
                return $precio_desgloce[0].".80";
            break;
            case "80":
                return $precio_desgloce[0].".80";
            break;
            //80
            case "80":
                return $precio_desgloce[0].".80";
            break;
            case "81":
                return $precio_desgloce[0].".80";
            break;
            case "82":
                return $precio_desgloce[0].".80";
            break;
            case "83":
                return $precio_desgloce[0].".80";
            break;
            case "84":
                return $precio_desgloce[0].".80";
            break;
            case "85":
                return $precio_desgloce[0].".90";
            break;
            case "86":
                return $precio_desgloce[0].".90";
            break;
            case "87":
                return $precio_desgloce[0].".90";
            break;
            case "88":
                return $precio_desgloce[0].".90";
            break;
            case "89":
                return $precio_desgloce[0].".90";
            break;
            case "90":
                return $precio_desgloce[0].".90";
            break;
            //90
            case "90":
                return $precio_desgloce[0].".90";
            break;
            case "91":
                return $precio_desgloce[0].".90";
            break;
            case "92":
                return $precio_desgloce[0].".90";
            break;
            case "93":
                return $precio_desgloce[0].".90";
            break;
            case "94":
                return $precio_desgloce[0].".90";
            break;
            case "95":
                $precio = $precio_desgloce[0];
                $precioReal = $precio + 1;
                return $precioReal.".00";
            break;
            case "96":
                $precio = $precio_desgloce[0];
                $precioReal = $precio + 1;
                return $precioReal.".00";
            break;
            case "97":
                $precio = $precio_desgloce[0];
                $precioReal = $precio + 1;
                return $precioReal.".00";
            break;
            case "98":
                $precio = $precio_desgloce[0];
                $precioReal = $precio + 1;
                return $precioReal.".00";
            break;
            case "99":
                $precio = $precio_desgloce[0];
                $precioReal = $precio + 1;
                return $precioReal.".00";
            break;            
            default:
            break;
        }
        #endregion

    }

    public function connectionAPI($dataI){
        // URL de la API a la que te deseas conectar
        $url = 'http://187.141.179.27/APIserve/index.php';
        // Datos que deseas enviar (por ejemplo, en formato JSON)
        $data = array(
            'datos' => $dataI
        );
        $data_string = json_encode($data);
        // Inicializar cURL
        $ch = curl_init($url);
        // Configurar la petición
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Puedes cambiar "POST" a otros métodos como "GET" o "PUT".
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));
        // Ejecutar la petición
        $result = curl_exec($ch);
        // Verificar si hubo errores
        if (curl_errno($ch)) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/errorURL.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(curl_error($ch));
        }
        // Cerrar la conexión cURL
        curl_close($ch);
        // Procesar la respuesta (puede ser JSON, XML, HTML, etc.)
        if ($result) {
            $response = json_decode($result, true);
            if($response){
                return $response;
            }
            else{
                return $response;
            }
        } else {
            echo 'No se recibió una respuesta válida.';
        }
    }

    public function ctApiToken(){
        // URL de la API a la que te deseas conectar
        $url = 'http://connect.ctonline.mx:3001/cliente/token';
        //$url = 'http://localhost/API/index.php';

        // Datos que deseas enviar (por ejemplo, en formato JSON)
        $data = array(
            "email" => "mario.flores@grupoqar.com",
            "cliente" => "HMO0410",
            "rfc"=> "NCO021021K82"
        );
        $data_string = json_encode($data);
        // Inicializar cURL
        $ch = curl_init($url);
        // Configurar la petición
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Puedes cambiar "POST" a otros métodos como "GET" o "PUT".
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));
        // Ejecutar la petición
        $result = curl_exec($ch);
        // Verificar si hubo errores
        if (curl_errno($ch)) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/errorURL.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(curl_error($ch));
        }
        // Cerrar la conexión cURL
        curl_close($ch);
        // Procesar la respuesta (puede ser JSON, XML, HTML, etc.)
        if ($result) {
            $response = json_decode($result, true);
            if($response){
                return $response;
            }
            else{
                return $response;
            }
        } else {
            echo 'No se recibió una respuesta válida.';
        }
    }

    public function ctApiVolumetria($token,$sku){
        // URL de la API a la que te deseas conectar
        $url = 'http://connect.ctonline.mx:3001/paqueteria/volumetria/'.$sku;
        //$url = 'http://localhost/API/index.php';

        // Configuración de la solicitud
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'x-auth:' . $token,
            // Otros encabezados si es necesario
        ]);
        // Ejecutar la petición
        $result = curl_exec($curl);
        // Verificar si hubo errores
        if (curl_errno($curl)) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/errorURL.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(curl_error($curl));
        }
        // Cerrar la conexión cURL
        curl_close($curl);
        // Procesar la respuesta (puede ser JSON, XML, HTML, etc.)
        if ($result) {
            $response = json_decode($result, true);
            if($response){
                return $response;
            }
            else{
                return $response;
            }
        } else {
            echo 'No se recibió una respuesta válida.';
        }
    }
}

