<?php
/**
 * Viva Payments Redirect Purchase Request
 */

namespace Omnipay\VivaPayments\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * Viva Payments Redirect (REST) Purchase Request
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
 * ### Return to Your Website
 *
 * Upon completion of the checkout form, the customer is redirected back to your website.
 * The redirection URLs are defined in your vivapayments.com account under the Sources section.
 *
 * Note that the redirection always happens at the 'Source' level. If you have defined
 * multiple sources on your profile, you need to use the optional parameter 'SourceCode'
 * when creating the order, so that the system selects the appropriate redirection url.
 *
 * The redirection uses the HTTP GET method and may append the following parameters to the URL:
 *
 * * s (int64): The Payment Order unique 12 digit ID
 * * t (uuid): The Transaction ID (may not be returned for some failed transactions)
 * * Lang (string): The language of the destination page in ISO format (el-GR for Greek, en-US for English)
 *
 * NOTE: It is highly recommended you always verify the status of a transaction and
 * not blindly depend on whether your success or failure url is called. You can make a
 * GetTransactions call to verify the status of an Order. To get notified for offline
 * payment methods you can make use of the Webhooks notification service.
 *
 * The Transaction ID in the redirect GET parameter is the transactionReference that has
 * to be used for subsequent refund() requests, not the transactionReference used in this
 * purchase() call.
 *
 * You may also see this TransactionID in the Transaction Details page in the vivapayments.com
 * account under "My Sales -> Sales" (click on the "Info" link).
 *
 * @see Omnipay\VivaPayments\RestGateway
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 * @link https://github.com/VivaPayments/API/wiki/Redirect-Checkout
 */
class RedirectPurchaseRequest extends AbstractRestRequest
{
    public function getData()
    {
        // An amount parameter is required.  All amounts are in EUR

        // The only parameter required by CreateOrder is amount
        // https://github.com/VivaPayments/API/wiki/CreateOrder
        $this->validate('amount');

        // Also optional parameters
        // https://github.com/VivaPayments/API/wiki/Optional-Parameters
        $data = array(
            'Amount'        => $this->getAmountInteger(),
        );

        return array_merge($data, parent::getData());
    }

    protected function createResponse(RequestInterface $request, $data, $statusCode = 200)
    {
        return new RedirectResponse($this, $data, $statusCode, $this->getBaseEndpoint());
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
        return parent::getEndpoint() . '/Orders';
    }
}
