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

class TeamPost extends Action
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
            $correoDestino = $_POST['correo'];
            $telefono      = $_POST['telefono'];
            $area          = $_POST['area'];
            $mensaje       = $_POST['mensaje'];
            $nombreArchivo = $_FILES["file-1"]["name"];
            $rutaTemporal  = $_FILES["file-1"]["tmp_name"];        
            //Recipients
            $mail->setFrom('luis.pruebasqar@outlook.com', 'Unilane');
            $destinatarios = [
                'luis.pruebasqar@outlook.com' => 'Unilane',
                $correoDestino => $nombre
            ];
            foreach ($destinatarios as $email => $nombre) {
                $mail->addAddress($email, $nombre);
            }         
            //Attachments
            if($nombreArchivo !=""){
                $mail->addAttachment($rutaTemporal,$nombreArchivo);//Add attachments
            }
            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'únete al equipo unilane';
            $mail->Body    = '          
            <!DOCTYPE html>       
            <html>    
                <body>        
                    <img src="https://drive.google.com/file/d/1m39nzd8XJKp7CNPu4FIGlv8AKJdDvzFb/view?usp=drive_link" alt="Encabezado" width="600" height="200">
                    <br>
                    <br>
                    <h3> DATOS DEL SOLICITANTE </h3>
                    <p> <strong>Nombre del solicitante:</strong> '.$nombre.'</p>
                    <p> <strong>Telefono:</strong> '.$telefono.'</p>
                    <p> <strong>Area prometida:</strong> '.$area.'</p>
                    <p> <strong>Mensaje:</strong> '.$mensaje.'</p>

                </body>
            </html>';
                        
            if ($mail->Send())
            {
                $this->messageManager->addSuccessMessage(
                    __('El formulario se mando correctamente.')
                );
                return $this->resultRedirectFactory->create()->setPath('forms/index/teamindex');
            }
            else
            {
                $this->messageManager->addErrorMessage(
                    __('Error al enviar el formulario.')
                );
                return $this->resultRedirectFactory->create()->setPath('forms/index/teamindex');
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        //return $this->resultRedirectFactory->create()->setPath('contact/index');
    }
}
