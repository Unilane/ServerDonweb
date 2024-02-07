<?php
namespace Unilane\Openpay\Model\Rewrite\Cards\Model;

use \Openpay\Cards\Model\Payment as OriginalPayment;

class Payment extends OriginalPayment
{
    /**
     * @param Exception $e
     * @return string
     */
    public function error($e) {
        /* 6001 el webhook ya existe */
        switch ($e->getCode()) {
            case '1000':
                $msg = 'Por favor contacta a tu banco o intenta con otro método de pago.';
                break;
            case '1004':
                $msg = 'Por favor contacta a tu banco o intenta con otro método de pago.';
                break;
            case '1005':
                $msg = 'Por favor contacta a tu banco o intenta con otro método de pago.';
                break;
            /* ERRORES TARJETA */
            case '3002':
                $msg = 'La tarjeta ha expirado.';
                break;
            case '3003':
                $msg = 'La tarjeta no tiene fondos suficientes.';
                break;
            case '3001':
                $msg = 'La tarjeta fue rechazada.';
                break;
            case '3006':
                $msg = 'La operación no esta permitida para este cliente o esta transacción.';
                break;
            case '3008':
                $msg = 'Por favor contacta a tu banco o intenta con otro método de pago.';
                break;
            case '3012':
                $msg = 'Se requiere solicitar al banco autorización para realizar este pago.';
                break;
            case '3004':
                $msg = 'La tarjeta ha sido identificada como una tarjeta robada.';
                break;
            case '3005':
                $msg = 'La tarjeta ha sido rechazada por el sistema antifraudes.';
                break;
            case '3009':
                $msg = 'La tarjeta fue reportada como perdida.';
                break;
            case '3010':
                $msg = 'El banco ha restringido la tarjeta.';
                break;
            case '3011':
                $msg = 'El banco ha solicitado que la tarjeta sea retenida. Contacte al banco.';
                break;
            case '1018':
                $msg = 'La tarjeta ha sido rechazada por el sistema antifraudes.';
                break;
            default: /* Demás errores 400 */
                $msg = 'La petición no pudo ser procesada.';
                break;
        }

        return 'error, '.$msg;
    }
}

