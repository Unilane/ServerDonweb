<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilane\Formularios\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\App\Action\Action;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class WarrantyPost extends Action
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var MailInterface
     */
    private $mail;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @param Context $context
     * @param ConfigInterface $contactsConfig
     * @param MailInterface $mail
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ConfigInterface $contactsConfig,
        MailInterface $mail,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context);
        $this->mail = $mail;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }
    /**
     * Post user question
     *
     * @return Redirect
     */
    public function execute()
    {        
        try {
            /**
             * @var mail
             */
            $mail = new PHPMailer(true);
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'luis.pruebasqar@outlook.com';                     //SMTP username
            $mail->Password   = 'D11DB6B02A.123';                               //SMTP password
            $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $mail->Port       = '587';
            $mail->CharSet    = 'UTF-8';

            $nombre        = $_POST['nombre'];
            $folio         = $_POST['folio'];
            $fecha         = $_POST['fecha'];
            $correo        = $_POST['correo'];
            $telefono      = $_POST['telefono'];
            $motivo        = $_POST['motivo'];       
            //Recipients
            $mail->setFrom('luis.pruebasqar@outlook.com', 'Unilane');
            $destinatarios = [
                'luis.pruebasqar@outlook.com' => 'Unilane',
                $correo => $nombre
            ];
            foreach ($destinatarios as $email => $nombre) {
                $mail->addAddress($email, $nombre);
            }
            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'Garantía';
            $mail->Body    = '                                 
                        <img src="C:\xampp\htdocs\magento\pub\media\wysiwyg\smartwave\porto\homepage\34\unilane.png" alt="Imagen" style="display: block; max-width: 30%;">
                        <br>
                        <br>
                        <h3> INFORMACION DE LA GARANTIA </h3>
                        <p> <strong>Nombre Completo o Razon Social:</strong> '.$nombre.'</p>
                        <p> <strong>Folio del pedido:</strong> '.$folio.'</p>
                        <p> <strong>Fecha del pedido:</strong> '.$fecha.'</p>
                        <p> <strong>Correo electrónico:</strong> '.$correo.'</p>
                        <p> <strong>Telefono:</strong> '.$telefono.'</p>
                        <p> <strong>Motivo de solicitud de garantía:</strong> '.$motivo.'</p>';                                            
                        
            if ($mail->Send())
            {
                $this->messageManager->addSuccessMessage(
                    __('El formulario se mando correctamente.')
                );
                return $this->resultRedirectFactory->create()->setPath('forms/index/warrantyindex');
            }
            else
            {
                $this->messageManager->addErrorMessage(
                    __('Error al enviar el formulario.')
                );
                return $this->resultRedirectFactory->create()->setPath('forms/index/warrantyindex');
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        //return $this->resultRedirectFactory->create()->setPath('contact/index');
    }
}
