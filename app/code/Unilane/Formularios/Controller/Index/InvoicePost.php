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

class InvoicePost extends Action
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
        try{          

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
            //Datos de facturación 
            $foliopedido   = $_POST['foliopedido'];
            $razonsocial   = $_POST['razonsocial'];
            $fisicamoral   = $_POST['fisicamoral'];
            $rfc           = $_POST['rfc'];
            $regimenfiscal = $_POST['regimenfiscal'];
            $cfdi          = $_POST['cfdi'];
            $metodopago    = $_POST['metodopago'];           
            $importeFinal  = $_POST['importeiva'];
            //Domicilio Fiscal 
            $calle        = $_POST['calle'];
            $noexterno    = $_POST['noexterno'];
            $nointerno    = $_POST['nointerno'];
            $colonia      = $_POST['colonia'];
            $ciudad       = $_POST['ciudad'];
            $municipio    = $_POST['municipio'];
            $estado       = $_POST['estado'];
            $codigopostal = $_POST['codigopostal'];
            //Datos de contacto
            $telefono    = $_POST['telefono'];
            $correoelec  = $_POST['correoelec'];
            $comentarios = $_POST['comentarios'];
            //Archivo
            $nombreArchivo = $_FILES["file-1"]["name"];
            $rutaTemporal  = $_FILES["file-1"]["tmp_name"];
            //Attachments
            $mail->setFrom('luis.pruebasqar@outlook.com', 'Unilane');
            $destinatarios = [
                'luis.pruebasqar@outlook.com' => 'Unilane',
                $correoelec => "A quien corresponda"
            ];
            foreach ($destinatarios as $email => $nombre) {
                $mail->addAddress($email, $nombre);
            }
            if($nombreArchivo !=""){
                $mail->addAttachment($rutaTemporal,$nombreArchivo);//Add attachments
            }
            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'FACTURACIÓN';
            $mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
            <!--[if (gte mso 9)|(IE)]>
              <xml>
                <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
              </o:OfficeDocumentSettings>
            </xml>
            <![endif]-->
            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- So that mobile will display zoomed in -->
            <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- enable media queries for windows phone 8 -->
            <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->
            <meta name="format-detection" content="date=no"> <!-- disable auto date linking in iOS -->
            <meta name="format-detection" content="address=no"> <!-- disable auto address linking in iOS -->
            <meta name="format-detection" content="email=no"> <!-- disable auto email linking in iOS -->
            <meta name="color-scheme" content="only">
            <title></title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
            <style type="text/css">
            /*Basics*/
            body {margin:0px!important;padding:0px!important;display:block!important;min-width:100%!important;width:100%!important;-webkit-text-size-adjust:none;}
            table {border-spacing:0;mso-table-lspace:0pt;mso-table-rspace:0pt;}
            table td {border-collapse:collapse;mso-line-height-rule:exactly;}
            td img {-ms-interpolation-mode:bicubic;width:auto;max-width:auto;height:auto;margin:auto;display:block!important;border:0px;}
            td p {margin:0;padding:0;}
            td div {margin:0;padding:0;}
            td a {text-decoration:none;color:inherit;}
            /*Outlook*/
            .ExternalClass {width:100%;}
            .ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div {line-height:inherit;}
            .ReadMsgBody {width:100%;background-color:#ffffff;}
            /* iOS BLUE LINKS */
            a[x-apple-data-detectors] {color:inherit!important;text-decoration:none!important;font-size:inherit!important;font-family:inherit!important;font-weight:inherit!important;line-height:inherit!important;} 
            /*Gmail blue links*/
            u + #body a {color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit;}
            /*Buttons fix*/
            .undoreset a, .undoreset a:hover {text-decoration:none!important;}
            .yshortcuts a {border-bottom:none!important;}
            .ios-footer a {color:#aaaaaa!important;text-decoration:none;}
            /*Responsive-Tablet*/
            @media only screen and (max-width:799px) and (min-width:601px) {
              .outer-table.row {width:640px!important;max-width:640px!important;}
              .inner-table.row {width:580px!important;max-width:580px!important;}
            }
            /*Responsive-Mobile*/
            @media only screen and (max-width:600px) and (min-width:320px) {
              table.row {width:100%!important;max-width:100%!important;}
              td.row {width:100%!important;max-width:100%!important;}
              .img-responsive img {width:100%!important;max-width:100%!important;height:auto!important;margin:auto;}
              .center-float {float:none!important;margin:auto!important;}
              .center-text{text-align:center!important;}
              .container-padding {width:100%!important;padding-left:15px!important;padding-right:15px!important;}
              .container-padding10 {width:100%!important;padding-left:10px!important;padding-right:10px!important;}
              .hide-mobile {display:none!important;}
              .menu-container {text-align:center!important;}
              .autoheight {height:auto!important;}
              .m-padding-10 {margin:10px 0!important;}
              .m-padding-15 {margin:15px 0!important;}
              .m-padding-20 {margin:20px 0!important;}
              .m-padding-30 {margin:30px 0!important;}
              .m-padding-40 {margin:40px 0!important;}
              .m-padding-50 {margin:50px 0!important;}
              .m-padding-60 {margin:60px 0!important;}
              .m-padding-top10 {margin:30px 0 0 0!important;}
              .m-padding-top15 {margin:15px 0 0 0!important;}
              .m-padding-top20 {margin:20px 0 0 0!important;}
              .m-padding-top30 {margin:30px 0 0 0!important;}
              .m-padding-top40 {margin:40px 0 0 0!important;}
              .m-padding-top50 {margin:50px 0 0 0!important;}
              .m-padding-top60 {margin:60px 0 0 0!important;}
              .m-height10 {font-size:10px!important;line-height:10px!important;height:10px!important;}
              .m-height15 {font-size:15px!important;line-height:15px!important;height:15px!important;}
              .m-height20 {font-size:20px!important;line-height:20px!important;height:20px!important;}
              .m-height25 {font-size:25px!important;line-height:25px!important;height:25px!important;}
              .m-height30 {font-size:30px!important;line-height:30px!important;height:30px!important;}
              .radius6 {border-radius:6px!important;}
              .fade-white {background-color:rgba(255, 255, 255, 0.8)!important;}
              .rwd-on-mobile {display:inline-block!important;padding:5px!important;}
              .center-on-mobile {text-align:center!important;}
              .rwd-col {width:100%!important;max-width:100%!important;display:inline-block!important;}
            }
            </style>
            
            </head>
            <body style="margin-top:0;margin-bottom:0;padding-top:0;padding-bottom:0;width:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;" bgcolor="#F4F4F4">
            <span class="preheader-text" style="color:transparent;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;visibility:hidden;width:0;display:none;mso-hide:all;"></span>
            <!-- Preheader white space hack -->
            <div style="display:none;max-height:0px;overflow:hidden;">
            ‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;‌&nbsp;
            </div>
            
            <table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" style="width:100%;max-width:100%;">
              <tbody><tr><!-- Outer Table -->
                <td align="center" bgcolor="#F4F4F4">
                                    <table data-outer-table="" border="0" align="center" cellpadding="0" cellspacing="0" class="outer-table row" role="presentation" width="680" style="width:680px;max-width:680px;">
              <!-- colibri-preheader-14 -->
              <tbody><tr>
                <td align="center" bgcolor="#f4f4f4">
            <!-- Content -->
            <table data-inner-table="" border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="inner-table row" width="580" style="width:580px;max-width:580px;">
              <tbody><tr>
                <td height="0" style="font-size:20px;line-height:0px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">
            <!-- rwd-col -->
            <table border="0" cellpadding="0" cellspacing="0" align="center" class="container-padding" width="100%" style="width:100%;max-width:100%;">
              <tbody><tr>
                <td class="rwd-col" align="center" width="46.66%" style="width:46.66%;max-width:46.66%;">
            <!-- column -->
            <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;">
              <tbody>
            </tbody></table>
            <!-- column -->
            </td>
            <td class="rwd-col" align="center" width="6.66%" height="20" style="width:6.66%;max-width:6.66%;height:20px;">&nbsp;</td>
            <td class="rwd-col" align="center" width="46.66%" style="width:46.66%;max-width:46.66%;">
            <!-- column -->
            <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;">
              <tbody>
            </tbody></table>
            <!-- column -->
                </td>
              </tr>
            </tbody></table>
            <!-- rwd-col -->
                </td>
              </tr>
              <tr>
                <td height="0" style="font-size:30px;line-height:0px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="center-text">
                  <a href="www.unilane.mx" target="_blank"><img style="width:200px;border:0px;display:inline!important;" src="https://previews.dropbox.com/p/thumb/AB_oynHQHr184in79tlKU7zCDQKZpG1C3Fn83Cgtl8P_m0S5VRp-o6epwjzpvw41veeubskRyXCYRWrnrfka1a6rY0qibzfttGmiylnAhmbvLmF9Ckwgsbioxg4rhjGp5HI3qLnJkFeZB-wXG7v7ykZ2XSUjHqQRQv9zR5YUtxHrG5FI-00VVQyQbWDkhOHJhwclDLTzGTbYJ1ZGrzJIXpH3wohC_VwM5mJLuwhKPPK5tb1WHV5gZD-KWUOf0iUtzMH7kwamjvMrX98KTaQD5nP8iewuCYojMi_kUX_anFNgcb6Qz8LufWIOSRWWZgukcvzd65UoEOXTjTY6FZv2_65-/p.png" width="200" border="0" alt="unilane"></a>
                </td>
              </tr>
              <tr>
                <td height="40" style="font-size:20px;line-height:40px;">&nbsp;</td>
              </tr>
            </tbody></table>
            <!-- Content -->
                </td>
              </tr>
              <!-- colibri-preheader-14 -->
            </tbody></table><table data-outer-table="" border="0" align="center" cellpadding="0" cellspacing="0" class="outer-table row" role="presentation" width="640" style="width:680px;max-width:680px;">
              <!-- colibri-basic-message-13 -->
              <tbody><tr>
                <td align="center" bgcolor="#ffffff">
            <!-- Content -->
            <table data-inner-table="" border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="inner-table row container-padding" width="580" style="width:580px;max-width:580px;">
              <tbody><tr>
                <td height="45" style="font-size:15px;line-height:45px;">&nbsp;</td>
              </tr>
              <tr>
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:25px;line-height:33px;font-weight:500;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;">Confirmación datos de factura</p></div>
                    
                </td>
              </tr>
              <tr>
                <td height="30" style="font-size:15px;line-height:30px;">&nbsp;</td>
              </tr>
              <tr>
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:14px;line-height:22px;font-weight:400;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;">Hemos recibido una solicitud de facturación de tu compra, para brindarte un mejor servicio y asegurarnos de que recibas tu factura de manera oportuna, te pedimos  nos ayudes a verificar la información que nos proporcionaste. </p><p style="margin: 0px; padding: 0px;"><br></p><p style="margin: 0px; padding: 0px;">A continuación, te mostramos los datos que hemos registrado.</p><p style="margin: 0px; padding: 0px;">
                      </p></div>
                    
                </td>
              </tr>
              <tr>
                <td height="2" style="font-size:25px;line-height:2px;">&nbsp;</td>
              </tr>
              
              
              <tr>
                <td align="center">
                  <!-- Content -->
                  <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row" width="89.66%" style="width:89.66%;max-width:89.66%;">
                    <tbody><tr>
                      <td height="2" style="font-size:20px;line-height:2px;">&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="center">
                        <!-- rwd-col -->
                        <table border="0" cellpadding="0" cellspacing="0" align="center" class="container-padding" width="100%" style="width:100%;max-width:100%;">
                          <tbody><tr>
                            <td class="rwd-col" align="center" valign="top" width="48.96%" style="width:48.96%;max-width:48.96%;">
                        <!-- column -->
                        <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;">
                          <tbody>
                          
                          
                        </tbody></table>
                        <!-- column -->
                        </td>
                        <td class="rwd-col" align="center" width="2.08%" height="30" style="width:2.08%;max-width:2.08%;height:30px;">&nbsp;</td>
                        <td class="rwd-col" align="center" valign="top" width="48.96%" style="width:48.96%;max-width:48.96%;">
                        <!-- column -->
                        <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;">
                          <tbody>
                          
                          
                        </tbody></table>
                        <!-- column -->
                            </td>
                          </tr>
                        </tbody></table>
                        <!-- rwd-col -->
                      </td>
                    </tr>
                    <tr>
                      <td height="0" style="font-size:10px;line-height:0px;">&nbsp;</td>
                    </tr>
                  </tbody></table>
                  <!-- Content -->
                </td>
              </tr>
            </tbody></table>
            <!-- Content -->
                </td>
              </tr>
              <!-- colibri-basic-message-13 -->
            </tbody></table><table data-outer-table="" border="0" align="center" cellpadding="0" cellspacing="0" class="outer-table row" role="presentation" width="640" style="width:680px;max-width:680px;">
              <!-- colibri-basic-message-11 -->
              <tbody><tr>
                <td align="center" bgcolor="#ffffff">
            <!-- Content -->
            <table data-inner-table="" border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="inner-table row container-padding" width="580" style="width:580px;max-width:580px;">
              <tbody><tr>
                <td height="0" style="font-size:15px;line-height:0px;">&nbsp;</td>
              </tr>
              
              <tr style="">
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:16px;line-height:38px;font-weight:700;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;">Datos de facturación</p></div>
                    
                </td>
              </tr>
              <tr style="">
                <td height="5" style="font-size:15px;line-height:5px;">&nbsp;</td>
              </tr>
              <tr>
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13.3px;line-height:22px;font-weight:400;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;text-align:left;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;"><strong>Folio Pedido o No. Orden: </strong>'.$foliopedido.'</p><p style="margin: 0px; padding: 0px;"><strong>Nombre o Razón Social: </strong>'.$razonsocial.'</p><p style="margin: 0px; padding: 0px;"><strong>Persona Física o Moral: </strong>'.$fisicamoral.'</p><p style="margin: 0px; padding: 0px;"><strong>Número de Identificación Fiscal (RFC): </strong>'.$rfc.'</p><p style="margin: 0px; padding: 0px;"><strong>Régimen Fiscal: </strong>'.$regimenfiscal.'</p><p style="margin: 0px; padding: 0px;"><strong>Uso de CFDI: </strong>'.$cfdi.'</p><p style="margin: 0px; padding: 0px;"><strong>Método de pago: </strong>'.$metodopago.'</p><p style="margin: 0px; padding: 0px;"><strong>Importe total: </strong>$'.$importeFinal.'</p></div>
                    
                </td>
              </tr>
              <tr>
                <td height="16" style="font-size:15px;line-height:16px;">&nbsp;</td>
              </tr>
            </tbody></table>
            <!-- Content -->
                </td>
              </tr>
            <!-- colibri-basic-message-11 -->
            </tbody></table><table data-outer-table="" border="0" align="center" cellpadding="0" cellspacing="0" class="outer-table row" role="presentation" width="640" style="width:680px;max-width:680px;">
              <!-- colibri-basic-message-11 -->
              <tbody><tr>
                <td align="center" bgcolor="#ffffff">
            <!-- Content -->
            <table data-inner-table="" border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="inner-table row container-padding" width="580" style="width:580px;max-width:580px;">
              <tbody><tr>
                <td height="0" style="font-size:15px;line-height:0px;">&nbsp;</td>
              </tr>
              
              <tr style="">
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:16px;line-height:38px;font-weight:700;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;">Domicilio Fiscal</p></div>
                    
                </td>
              </tr>
              <tr style="">
                <td height="5" style="font-size:15px;line-height:5px;">&nbsp;</td>
              </tr>
              <tr>
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13.3px;line-height:22px;font-weight:400;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;text-align:left;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;"><strong>Calle o Av. del domicilio: </strong>'.$calle.'</p><p style="margin: 0px; padding: 0px;"><strong>Número Ext: </strong>'.$noexterno.'</p><p style="margin: 0px; padding: 0px;"><strong>Número Int: </strong>'.$nointerno.'</p><p style="margin: 0px; padding: 0px;"><strong>Colonia: </strong>'.$colonia.'</p><p style="margin: 0px; padding: 0px;"><strong>Municipio: </strong>'.$municipio.'</p><p style="margin: 0px; padding: 0px;"><strong>Ciudad: </strong>'.$ciudad.'</p><p style="margin: 0px; padding: 0px;"><strong>Estado: </strong>'.$estado.'</p><p style="margin: 0px; padding: 0px;"><strong>Código Postal: </strong>'.$codigopostal.'</p></div>
                    
                </td>
              </tr>
              <tr>
                <td height="16" style="font-size:15px;line-height:16px;">&nbsp;</td>
              </tr>
            </tbody></table>
            <!-- Content -->
                </td>
              </tr>
            <!-- colibri-basic-message-11 -->
            </tbody></table><table data-outer-table="" border="0" align="center" cellpadding="0" cellspacing="0" class="outer-table row" role="presentation" width="640" style="width:680px;max-width:680px;">
              <!-- colibri-basic-message-11 -->
              <tbody><tr>
                <td align="center" bgcolor="#ffffff">
            <!-- Content -->
            <table data-inner-table="" border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="inner-table row container-padding" width="580" style="width:580px;max-width:580px;">
              <tbody><tr>
                <td height="0" style="font-size:15px;line-height:0px;">&nbsp;</td>
              </tr>
              
              <tr style="">
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:16px;line-height:38px;font-weight:700;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;">Datos de contacto y comentarios</p></div>
                    
                </td>
              </tr>
              <tr style="">
                <td height="5" style="font-size:15px;line-height:5px;">&nbsp;</td>
              </tr>
              <tr>
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13.3px;line-height:22px;font-weight:400;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;text-align:left;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;"><strong>Teléfono: </strong>'.$telefono.'</p><p style="margin: 0px; padding: 0px;"><strong>Correo electrónico: </strong>'.$correoelec.'</p><p style="margin: 0px; padding: 0px;"><strong>Comentarios: </strong>'.$comentarios.'</p></div>
                    
                </td>
              </tr>
              <tr>
                <td height="16" style="font-size:15px;line-height:16px;">&nbsp;</td>
              </tr>
            </tbody></table>
            <!-- Content -->
                </td>
              </tr>
            <!-- colibri-basic-message-11 -->
            </tbody></table><table data-outer-table="" border="0" align="center" cellpadding="0" cellspacing="0" class="outer-table row" role="presentation" width="680" style="width:680px;max-width:680px;">
              <!-- colibri-divider-4 -->
              <tbody><tr>
                <td align="center" bgcolor="#ffffff" height="50" style="font-size:50px;line-height:50px;">
                  <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="container-padding" width="100%" style="width:100%;max-width:100%;">
                    <tbody><tr>
                      <td align="center" height="3" style="font-size:3px;line-height:3px;border-top:3px dotted #AAAAAA;">&nbsp;</td>
                    </tr>
                  </tbody></table>
                </td>
              </tr>
              <!-- colibri-divider-4 -->
            </tbody></table><table data-outer-table="" border="0" align="center" cellpadding="0" cellspacing="0" class="outer-table row" role="presentation" width="640" style="width:680px;max-width:680px;">
              <!-- colibri-basic-message-2 -->
              <tbody><tr>
                <td align="center" bgcolor="#ffffff">
            <!-- Content -->
            <table data-inner-table="" border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="inner-table row" width="580" style="width:580px;max-width:580px;">
              <tbody><tr>
                <td height="15" style="font-size:15px;line-height:15px;">&nbsp;</td>
              </tr>
              
              
              <tr>
                <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:14px;line-height:21px;font-weight:400;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                    
                      <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;">Revisa cuidadosamente esta información, si encuentras algún error o necesitas hacer modificaciones, te pedimos nos lo informes lo antes posible respondiendo a este correo. Si la información es correcta, no es necesario responder este correo.</p><p style="margin: 0px; padding: 0px;"><br></p><p style="margin: 0px; padding: 0px;">Procederemos a generar la factura correspondiente a tu compra y enviaremos la factura por correo electrónico en formato PDF y XML dentro de las próximas 48 horas.</p><p style="margin: 0px; padding: 0px;"><br></p><p style="margin: 0px; padding: 0px;">Si tienes alguna pregunta o necesitas asistencia adicional, no dudes en ponerte en contacto con nuestro equipo de atención al cliente. Estamos aquí para ayudarte en todo momento.</p><p style="margin: 0px; padding: 0px;"><br></p><p style="margin: 0px; padding: 0px;"><br></p><p style="margin: 0px; padding: 0px;"><strong>– Horarios de atención –</strong></p><p style="margin: 0px; padding: 0px;"><br></p><p style="margin: 0px; padding: 0px;">Lunes a Viernes de 08:30 a 18:30</p><p style="margin: 0px; padding: 0px;">Sábados de 9:00 a 13:00</p><p style="margin: 0px; padding: 0px;">Tel. (662) 109 33 27</p><p style="margin: 0px; padding: 0px;">contacto@unilane.mx</p></div>
                    
                </td>
              </tr>
              <tr>
                <td height="25" style="font-size:25px;line-height:25px;">&nbsp;</td>
              </tr>
              
              <tr>
                <td height="30" style="font-size:30px;line-height:30px;">&nbsp;</td>
              </tr>
            </tbody></table>
            <!-- Content -->
                </td>
              </tr>
              <!-- colibri-basic-message-2 -->
            </tbody></table><table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;">
              <!-- colibri-footer -->
              <tbody><tr>
                <td align="center">
            <!-- Content -->
            <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row container-padding" width="520" style="width:520px;max-width:520px;">
              <tbody><tr>
                <td height="17" style="font-size:60px;line-height:17px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">
                  <!-- Social Icons -->
                  <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="width:100%;max-width:100%;">
                    <tbody><tr>
                      <td align="center">
                        <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation">
                          <tbody><tr>
                            <td class="rwd-on-mobile" align="center" valign="middle" height="36" style="height:36px;">
                              <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation">
                                <tbody><tr>
                                  <td width="10"></td>
                                  <td align="center">
                                    <a href="https://facebook.com/unilane.mx" target="_blank"><img style="width:25px;border:0px;display:inline!important;" src="https://modulescomposer.s3.us-east-2.amazonaws.com/blue/Facebook.png" width="25" border="0" alt="Facebook"></a>
                                  </td>
                                  <td width="10"></td>
                                </tr>
                              </tbody></table>
                            </td>
                            <td class="rwd-on-mobile" align="center" valign="middle" height="36" style="height:36px;">
                              <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation">
                                <tbody><tr>
                                  <td width="10"></td>
                                  <td align="center">
                                    <a href="https://instagram.com/unilane.mx" target="_blank"><img style="width:25px;border:0px;display:inline!important;" src="https://modulescomposer.s3.us-east-2.amazonaws.com/blue/Instagram.png" width="25" border="0" alt="Instagram"></a>
                                  </td>
                                  <td width="10"></td>
                                </tr>
                              </tbody></table>
                            </td>
                            <td class="rwd-on-mobile" align="center" valign="middle" height="36" style="height:36px;">
                              <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation">
                                <tbody><tr>
                                  <td width="10"></td>
                                  <td align="center">
                                    <a href="linkedin.com/company/Nego-Comput-SAPI-de-CV" target="_blank"><img style="width:26px;border:0px;display:inline!important;" src="https://previews.dropbox.com/p/thumb/AB-fxG3XVZxwjLAXuiF-qzUedUEXKez3_KVZz2VDThUt1JlaD2NIQJ-b3KUinI-y9RrjZWylqnp2DPZn52EYPAMDhDgN-yTAK-n3_dM2icWqcHDG1FsDj7LFy2X8M0zgV8-Vt4FPnZsCJ_CGQr4TqxaLJSwFxdJUcQLCXhEWZ_H5ZrqfkMLgE8mGl6CzuLU9P5WptKtiaWuD76wegkWYioGu6IchRvqxqKgVvu2vUqjZlbitxaas7xoaJGNoLmESdflTvV7pMzC0d09FHDl1s16bAd52AFF75uloLwKNQ4ZfopENg2kjgDPRCfIQdWU2lC4k2P0S5Fij2jzTNOIQ-gbm/p.png" width="26" border="0" alt="Linkedin"></a>
                                  </td>
                                  <td width="10"></td>
                                </tr>
                              </tbody></table>
                            </td>
                            
                          </tr>
                        </tbody></table>
                      </td>
                    </tr>
                  </tbody></table>
                  <!-- Social Icons -->
                </td>
              </tr>
              <tr>
                <td height="7" style="font-size:60px;line-height:7px;">&nbsp;</td>
              </tr>
              
              
              <tr>
                <td align="center">
                  <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation">
                    <tbody><tr class="center-on-mobile">
                      <td class="rwd-on-mobile center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13px;line-height:20.5px;font-weight:300;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                        <a href="#" style="Arial,Helvetica,sans-serif;font-size:14px;font-weight:300;line-height:24px;color:#333333;text-decoration:none;">Aviso de Privacidad</a>
                      </td>
                      <td class="hide-mobile" align="center" valign="middle" style="">
                        <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation">
                          <tbody><tr>
                            <td width="5"></td>
                            <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13px;line-height:20.5px;font-weight:300;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">|</td>
                            <td width="5"></td>
                          </tr>
                        </tbody></table>
                      </td>
                      <td class="rwd-on-mobile center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13px;line-height:20.5px;font-weight:300;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                        <a href="#" style="fArial,Helvetica,sans-serif;font-size:14px;font-weight:300;line-height:24px;color:#333333;text-decoration:none;">Términos y Condiciones</a>
                      </td>
                      <td class="hide-mobile" align="center" valign="middle">
                        <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation">
                          <tbody><tr>
                            <td width="5"></td>
                            <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13px;line-height:20.5px;font-weight:300;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">|</td>
                            <td width="5"></td>
                          </tr>
                        </tbody></table>
                      </td>
                      <td class="rwd-on-mobile center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13px;line-height:20.5px;font-weight:300;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                        <a href="#" style="Arial,Helvetica,sans-serif;font-size:14px;font-weight:300;line-height:24px;color:#333333;text-decoration:none;">Unsubscribe</a>
                      </td>
                    </tr>
                  </tbody></table>
                </td>
              </tr>
              <tr style="">
                <td height="5" style="border-bottom:4px dotted rgb(228, 228, 228);font-size:26px;line-height:5px;">&nbsp;</td>
              </tr>
              <tr style="">
                <td height="8" style="font-size:30px;line-height:8px;">&nbsp;</td>
              </tr>
              <tr style="">
                <td align="center">
                  <table border="0" align="center" cellpadding="0" cellspacing="0" role="presentation" class="row" width="480" style="width:480px;max-width:480px;">
                    <tbody><tr>
                      <td class="center-text" align="center" style="font-family:Poppins, Arial, Helvetica, sans-serif;font-size:13px;line-height:20.5px;font-weight:300;font-style:normal;color:rgb(51, 51, 51);text-decoration:none;letter-spacing:0px;">
                        
                          <div style="margin: 0px; padding: 0px;"><p style="margin: 0px; padding: 0px;">© 2023 Unilane | Nego Comput SAPI de CV</p><p style="margin: 0px; padding: 0px;">Calzada de los Angeles 22, Montebello, Hermosillo, Sonora 83249</p><p style="margin: 0px; padding: 0px;">Tel. (662) 109 33 27 | contacto@unilane.mx</p></div>
                        
                      </td>
                    </tr>
                  </tbody></table>
                </td>
              </tr>
              <tr>
                <td height="10" style="font-size:30px;line-height:10px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="center-text">
                  <img style="width:130px;border:0px;display:inline!important;" src="https://previews.dropbox.com/p/thumb/AB-U-e16QVhvRIbL3GlW78PMHx19BO826gtSV675ngaE37QruZpf632ybTqTM4YOWMHa187yG13PAYGCl4YcKsRROZQlv1D4a8BYPzX-zlfdVzBa2Z6HRO7HXrJ4KATVHnptv4fkOPl9Qoqb--TlpJWhy-VgYGUuMr44Pf38IXR2YYHNrlAocPpuigFvxcl5rg0A-3_yqr1roE09ujolgU0Ggk4TV9adl3ZgZdHpULUd0oNREFmzBeQqW337sir5o4im2vPERi2ZcFMJh17boHf_Cekcf3kh6o3Qf668OD80L4kDLHb_8Vcl2Qxfe_2k7JaJClpd_ct0j5m6wV4exCPY/p.png" width="130" border="0" alt="unilane">
                </td>
              </tr>
              <tr>
                <td height="25" style="font-size:60px;line-height:25px;">&nbsp;</td>
              </tr>
            </tbody></table>
            <!-- Content -->
                </td>
              </tr>
              <!-- colibri-footer -->
            </tbody></table></td>
              </tr><!-- Outer-Table -->
            </tbody></table>
            </body>
            </html>';
            if ($mail->Send())
            {
                $this->messageManager->addSuccessMessage(
                    __('Su mensaje ha sido enviado con éxito.')
                );
                return $this->resultRedirectFactory->create()->setPath('forms/index/invoiceindex');
            }
            else
            {
                $this->messageManager->addErrorMessage(
                    __('Ocurrio un error al enviar el mensaje.')
                );
                return $this->resultRedirectFactory->create()->setPath('forms/index/invoiceindex');
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
