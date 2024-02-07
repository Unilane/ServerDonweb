<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilane\Sales\Plugin\Magento\Sales\Api;

use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResourceConnection;

class IntegrationIntelisis 
{
    /**
    * @var OrderInterface 
    */
    private $OrderInterface;
    /**
    * @var OrderManagementInterface 
    */
    private $OrderManagementInterface;
    /**
    * @var OrderRepositoryInterface 
    */
    private $OrderRepository;
    
    public function __construct(
        OrderInterface $OrderInterface,
        OrderManagementInterface $OrderManagementInterface,
        OrderRepositoryInterface $OrderRepository
    ) 
    {
        $this->OrderManagementInterface = $OrderManagementInterface;
        $this->OrderInterface           = $OrderInterface;
        $this->OrderRepository          = $OrderRepository;
    }
    public function afterPlace(OrderManagementInterface $subject, OrderInterface $order)
    {
        //return $order;
        // Este codigo colecciona toda la informacion del cliente como su nombre y direccion
        $infoCliente = $order->getAddresses();
        $datosDomicilio = [];        
        foreach($infoCliente as $cliente){       
            $data["calle"]      = $cliente->getStreet();
            $data["delegacion"] = $cliente->getCity();
            $data["estado"]     = $cliente->getRegion();
            $data["pais"]       = "Mexico";
            $data["cp"]         = $cliente->getPostCode();
            $data["nombre"]     = $order->getCustomerFirstname()." ".$order->getCustomerLastname(); 
            $data["bandera"]    = $cliente->getAddressType();
            $data['id']         = $order->getIncrementId();
            $data['telefono']   = $cliente->getTelephone();
            $data['colonia']    = $cliente->getMposcField1();
            $data['entreCalle'] = $data["calle"][2] ." - ". $cliente->getMposcField2();
            $data["query"]      = 1;
            array_push($datosDomicilio, $data);            
        }
        if($datosDomicilio){
            $result = $this->connectionAPI($datosDomicilio);
        }
        if($result["response"] == true){
            //Guarda el IDR que correspone a la orden actual
            $resource = \Magento\Framework\App\ObjectManager::getInstance()->get(ResourceConnection::class);
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('sales_order');
            $sql = "UPDATE $tableName SET idr = :idr WHERE entity_id = :id";
            $params = [
                'id' => $order->getId(),
                'idr' => $result["datos"]["IDR"]
            ];
            $results = $connection->fetchAll($sql, $params);
            // Este codigo colecciona toda la informacion del pedido que requiere intelisis
            $pedidos = $order->getItems();
            $datosPedido = [];
            foreach($pedidos as $pedido){
                $dataP["IDR"]        = $result["datos"]["IDR"];
                $dataP["Cantidad"]   = $pedido->getQtyOrdered();
                $dataP["Almacen"]    = 'ALMGEN';
                $dataP["Articulo"]   = $pedido->getSku();
                $dataP["Precio"]     = $pedido->getPrice() / 1.16;
                $dataP["Moneda"]     = 'MXN';
                $dataP["TipoCambio"] = '1';
                $dataP["query"]      = 2;
                array_push($datosPedido, $dataP);
            }
            $dataPa["IDR"]        = $result["datos"]["IDR"];
            $dataPa["Cantidad"]   = '1';
            $dataPa["Almacen"]    = 'ALMGEN';
            $dataPa["Articulo"]   = "U00002";
            $dataPa["Precio"]     = $order->getShippingAmount() / 1.16;
            $dataPa["Moneda"]     = 'MXN';
            $dataPa["TipoCambio"] = '1';
            $dataPa["query"]      = 2;
            array_push($datosPedido, $dataPa);
            if($datosPedido){
                $resultP = $this->connectionAPI($datosPedido);
            }
            if($resultP["response"] == true){
                return $order;
            }            
            else{
                return false;
            }     
        }        
        else{
            $this->messageManager->addErrorMessage(
                __($result["okRef"])
            );
            return $this->resultRedirectFactory->create()->setPath('onestepcheckout/');
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
