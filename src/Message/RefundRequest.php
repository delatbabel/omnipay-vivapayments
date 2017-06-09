<?php
/**
 * Viva Payments (REST) Refund Request
 */

namespace Omnipay\VivaPayments\Message;

/**
 * Viva Payments (REST) Refund Request
 *
 * To complete a redirect payment is a 3 step process.  The explanation is at
 * this link: https://github.com/VivaPayments/API/wiki/Redirect-Checkout
 *
 * ### 1. Creation of the Payment Order
 *
 * The code in this gateway plugin completes the payment order using the
 * /api/orders endpoint.
 *
 * ### 2. Completion of the Payment Details (Redirection)
 *
 * This is done by redirecting the customer to the Viva checkout page.
 *
 * ### 3. Confirmation of the Transaction
 *
 * The customer lands back on your website at the URL defined in your vivapayments.com
 * account under the Sources section.  There is no completePurchase() call required.
 *
 * ### Example
 *
 * <code>
 * // Create a gateway for the Viva Payments REST Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('VivaPayments_Redirect');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'merchantId'   => 'TEST',
 *     'apiKey'       => 'TEST',
 *     'testMode'     => true, // Or false when you are ready for live transactions
 * ));
 *
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'amount'                   => '10.00',
 *     'transactionId'            => 'TestPurchaseTransaction123456',
 *     'clientIp'                 => $_SERVER['REMOTE_ADDR'],
 *     'cardReference'            => $card_reference,
 * ));
 * $response = $transaction->send();
 *
 * // For a Redirect gateway request
 * if ($response->isRedirect()) {
 *     echo "Gateway response is a redirect.\n";
 *
 *     $redirect_url = $response->getRedirectUrl();
 *     echo "Redirect URL = $redirect_url\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 *
 * At the completion of this code the customer needs to be redirected to $redirect_url
 *
 * @see Omnipay\VivaPayments\RestGateway
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 * @link https://github.com/VivaPayments/API/wiki/Redirect-Checkout
 */
class RefundRequest extends AbstractRestRequest
{
    public function getData()
    {
        // An amount parameter is required.  All amounts are in EUR
        // The transaction reference is the transaction reference from the card payment,
        // not the order reference.
        $this->validate('amount', 'transactionReference');

        // Also optional parameters
        // https://github.com/VivaPayments/API/wiki/Optional-Parameters
        $data = array(
            'Amount'        => $this->getAmountInteger(),
            'ActionUser'    => $this->getDescription(),
        );

        return array_merge($data, parent::getData());
    }

    protected function getHttpMethod()
    {
        return 'DELETE';
    }

    /**
     * Get transaction endpoint.
     *
     * Purchases are created using the /purchases resource.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return parent::getEndpoint() . '/transactions/' . $this->getTransactionReference();
    }
}
