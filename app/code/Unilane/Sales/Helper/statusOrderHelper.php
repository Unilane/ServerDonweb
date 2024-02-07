<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilane\Sales\Helper;

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\OrderFactory;
use PHPMailer\PHPMailer\Exception;

/**
 * Adminhtml Catalog helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class statusOrderHelper 
{
    private $productFactory;
    private $productRepository;
    protected $orderFactory;
	protected $_objectManager;
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context     
     */
    public function __construct(
        ProductInterfaceFactory $productFactory,
	\Magento\Framework\ObjectManagerInterface $_objectManager,
        ProductRepositoryInterface $productRepository,
        OrderFactory $orderFactory

    ) {
        $this->orderFactory = $orderFactory;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
 $this->_objectManager = $_objectManager;

    }
    /**
     * Set Custom Attribute Tab Block Name for Category Edit
     *
     * @param string $attributeTabBlock
     * @return $this
     */

    public function statusOrder()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/guias.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("Inicio");

        $resource = \Magento\Framework\App\ObjectManager::getInstance()->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('sales_order');
	$tableNameTrack = $resource->getTableName('sales_shipment_track');
        $sql = "SELECT t1.entity_id, t1.idr from $tableName as t1 
                LEFT JOIN 
                $tableNameTrack as t2 
                ON t1.entity_id = t2.order_id 
                WHERE t2.order_id is null
        ";        
        $params = [];
        $results = $connection->fetchAll($sql, $params);
        if($results){
            $logger->info("si tiene registros");
            foreach($results as $result){
                array_push($result, ["enviar" => "enviar"]);
                $connect = $this->connectionAPI($result);
                if(@$connect["enviar"] == true){
		    $logger->info("ejecuto bien la funcion");

                    $orderId = $result["entity_id"];
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $this->_objectManager->get('Magento\Framework\Registry')->register('isSecureArea', true);        
                    $order = $this->orderFactory->create()->load($orderId);
                    // Initialize the order shipment object
                    $convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
                    $shipment = $convertOrder->toShipment($order);
                    // Loop through order items
                    foreach ($order->getAllItems() AS $orderItem) {
                        // Check if order item has qty to ship or is virtual
                        if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                            continue;
                        }
                        $qtyShipped = $orderItem->getQtyToShip();
                        // Create shipment item with qty
                        $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                        // Add shipment item to shipment
                        $shipment->addItem($shipmentItem);
                    }
                    $track = $this->_objectManager->create('\Magento\Sales\Model\Order\Shipment\TrackFactory')->create();
                    $track->setNumber($connect["datos"]["Guias"]);
                    $track->setCarrierCode('custom');
                    $shipment->addTrack($track);
                    $shipment->register();
                    $shipment->getOrder()->setIsInProcess(true);
                    // Save created shipment and order
                    $shipment->save();
                    $shipment->getOrder()->save();
                    //Manda notificacion al cliente con su numero de guia
                    // $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                    //         ->notify($shipment);
                    $shipment->save();
                }
		$logger->info("no encontro informacion o hubo un error en la consulta");
            }
        }
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
}

