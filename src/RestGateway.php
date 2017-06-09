<?php
/**
 * Viva Payments Gateway
 */

namespace Omnipay\VivaPayments;

use Omnipay\Common\AbstractGateway;

/**
 * Viva Payments Common Gateway
 *
 * This contains all common code and documentation for the 3 Viva Payments gateways:
 *
 * * VivaPayments_Redirect
 * * VivaPayments_Native
 * * VivaPayments_VivaWallet
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
 *
 * // For a native gateway request
 * } elseif ($response->isSuccessful()) {
 *     echo "Purchase transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 *
 * ### Test modes
 *
 * Test mode uses a different endpoint.  The endpoints are:
 *
 * * http://demo.vivapayments.com -- test
 * * https://www.vivapayments.com -- production
 *
 * Note that the test endpoint is HTTP and not HTTPS.
 *
 * ### Authentication
 *
 * See: https://github.com/VivaPayments/API/wiki/API-Authentication
 *
 * The API uses basic HTTP Authentication over SSL, so every request should include a
 * standard Authorization header with the following:
 *
 * Username : Your unique MerchantId accessible from your profile.
 * It can be found in the web self care environment by following Settings > API Access
 * Note: You can create unlimited demo accounts at http://demo.vivapayments.com
 *
 * Password : Your transaction password (also known as API Key), is defined in your profile.
 * This is different from the password you use to access your account from the VivaPayments
 * website. It can be found in the web self care environment by following Settings > API Access
 * Note: For the demo environment, you are required to set a new transaction password in your
 * demo profile
 *
 * You can create a test account at this website: http://demo.vivapayments.com/el-gr/signup
 * however it is entirely in the Greek language.  You also need a European or
 * US mobile phone number to sign up.
 *
 * ### Quirks
 *
 * * All payments are in Euros (EUR). No other currency is supported.
 * * Creating a purchase is a two step process.  Firstly there needs to be
 *   an order created.  Then, depending on the gateway (Redirect vs REST),
 *   either the customer is redirected to the gateway or further customer
 *   information is provided by a second REST call.
 * * Direct card payments are not supported.  Either a JS plugin is required
 *   (Native gateway) which creates a card reference, or a redirect is required
 *   (Redirect gateway).
 * * It is impossible to tell from the gatway response whether the transaction
 *   requires a redirect or not.  It's only possible to tell from the type of
 *   request made.  So I have created separate gateway classes for the different
 *   types of purchase request (Native vs Redirect) which will return different
 *   types of response.
 * * When making a redirect payment, upon completion of the checkout form, the
 *   customer is redirected back to your website. The redirection URLs are defined
 *   in your vivapayments.com account under the Sources section.  You cannot provide
 *   a per-transaction returnUrl or cancelUrl parameter to redirect each transaction
 *   to a different URL as can be done in some gateways.
 *
 * ### TODO
 *
 * * This gateway code is unfinished.  It doesn't cover all of the methods of the Viva
 *   Payments API described in the API wiki.
 *
 * @see \Omnipay\Common\AbstractGateway
 * @see \Omnipay\VivaPayments\Message\AbstractRestRequest
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 */
abstract class RestGateway extends AbstractGateway
{
    /**
     * Get the gateway default parameters
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'merchantId'    => '',
            'apiKey'        => '',
            'testMode'      => false,
        );
    }

    /**
     * Get the gateway merchantId
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * Set the gateway merchantId
     *
     * Note that all test merchantIds begin with the word TEST in upper case.
     *
     * @param string $value
     * @return RedirectGateway provides a fluent interface.
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * Get the gateway apiKey -- used as the password in HTTP Basic Auth
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set the gateway apiKey -- used as the password in HTTP Basic Auth
     *
     * @param string $value
     * @return RedirectGateway provides a fluent interface.
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    //
    // Direct API Purchase Calls -- purchase, refund
    //

    /**
     * Create a refund request.
     *
     * @param array $parameters
     * @return \Omnipay\VivaPayments\Message\RefundRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\VivaPayments\Message\RefundRequest', $parameters);
    }

    /**
     * Create a fetch transactions request.
     *
     * @param array $parameters
     * @return \Omnipay\VivaPayments\Message\FetchTransactionsRequest
     */
    public function fetchTransactions(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\VivaPayments\Message\FetchTransactionsRequest', $parameters);
    }
}
