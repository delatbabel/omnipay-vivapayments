<?php
/**
 * Viva Payments Gateway
 */

namespace Omnipay\VivaPayments;

/**
 * Viva Payments Native Checkout (REST) Gateway
 *
 * See RestGateway for all documentation about this gateway code.
 *
 * @see \Omnipay\Common\AbstractGateway
 * @see \Omnipay\VivaPayments\Message\AbstractRestRequest
 * @link http://www.paystream.com.au/developer-guides/
 * @link https://www.fatzebra.com.au/
 */
class NativeGateway extends RestGateway
{
    /**
     * Get the gateway display name
     *
     * @return string
     */
    public function getName()
    {
        return 'Viva Payments v1.0 Native';
    }

    //
    // Direct API Purchase Calls -- purchase, refund
    //

    /**
     * Create a purchase request.
     *
     * @param array $parameters
     * @return \Omnipay\VivaPayments\Message\NativePurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\VivaPayments\Message\NativePurchaseRequest', $parameters);
    }
}
