<?php
/**
 * Viva Payments Gateway
 */

namespace Omnipay\VivaPayments;

use Omnipay\Common\AbstractGateway;

/**
 * Viva Payments Redirect (REST) Gateway
 *
 * See RestGateway for all documentation about this gateway code.
 *
 * @see \Omnipay\Common\AbstractGateway
 * @see \Omnipay\VivaPayments\Message\AbstractRestRequest
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 */
class RedirectGateway extends RestGateway
{
    /**
     * Get the gateway display name
     *
     * @return string
     */
    public function getName()
    {
        return 'Viva Payments v1.0 Redirect';
    }

    //
    // Direct API Purchase Calls -- purchase, refund
    //

    /**
     * Create a purchase request.
     *
     * @param array $parameters
     * @return \Omnipay\VivaPayments\Message\RedirectPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\VivaPayments\Message\RedirectPurchaseRequest', $parameters);
    }
}
