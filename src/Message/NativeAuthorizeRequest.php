<?php
/**
 * Viva Payments Native Authorize Request
 */

namespace Omnipay\VivaPayments\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Viva Payments Native (REST) Authorize Request
 *
 * This is similar to a purchase request.  See the documentation in NativePurchaseRequest.
 *
 * Note that there is no capture() transaction.  Pre-authorized amounts stay on hold until
 * they are cancelled or time out (up to 30 days).
 *
 * @see Omnipay\VivaPayments\RestGateway
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 * @link https://github.com/VivaPayments/API/wiki/Native-Checkout
 */
class NativeAuthorizeRequest extends NativePurchaseRequest
{
    public function getData()
    {
        $data = array(
            'isPreAuth'   => true,
        );

        return array_merge($data, parent::getData());
    }
}
