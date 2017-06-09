<?php
/**
 * Viva Payments Native Purchase Request
 */

namespace Omnipay\VivaPayments\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Viva Payments Native (REST) Purchase Request
 *
 * Native Checkout allows you to create a custom payment form, so that your
 * customers never leave your page for making a card payment.  However there is a JavaScript
 * plugin that must be used on the payment page to make this happen, as it encodes the card
 * details to a card reference directly between the customer's browser and the gateway,
 * leaving just the card reference in the form.
 *
 * Read the documentation at this page about configuring your vivapayments account for
 * Native payments:  https://github.com/VivaPayments/API/wiki/Native-Checkout
 *
 * Steps to complete the payment.  This is a brief guide -- for full details see the
 * above documentation page.
 *
 * * Build the payment form as per *Step 3* of the documentation page.  Note the use of
 *   the special `data-vp` attribute.
 * * Add a reference to jQuery in the <head> section of your site
 * * Add a reference to the Native Checkout script in the <head> section of your site
 * * Initialize the process by calling the cards.setup() method once the document is
 *   fully loaded (e.g. on $(document).ready()).  Note that you need to fill the publicKey
 *   attribute in the JavaScript with the public key you get from the viva payments web
 *   site.
 * * Native Checkout sets the generated token in the hidden field `hidToken` and then submits
 *   the form.
 * * Add a button to your form that requests a token.
 * * On the server side, call the API to process the payment.
 *
 * ### Example
 *
 * Note that this only covers the server side transaction -- everything else is done on
 * the browser.
 *
 * <code>
 * // Create a gateway for the Viva Payments REST Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('VivaPayments_Native');
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
 * // For a Native gateway request
 * if ($response->isSuccessful()) {
 *     echo "Gateway response is successful.\n";
 *
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 *
 * @see Omnipay\VivaPayments\RestGateway
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 * @link https://github.com/VivaPayments/API/wiki/Native-Checkout
 */
class NativePurchaseRequest extends AbstractRestRequest
{
    /**
     * This says whether the order is complete and we have moved to making a transaction
     *
     * This flips to "true" once we create the first API call.
     *
     * @var  boolean
     */
    protected $orderComplete = false;

    public function getData()
    {
        // The only parameter required by CreateOrder is amount, but a cardReference is
        // required to complete the transaction.
        // https://github.com/VivaPayments/API/wiki/CreateOrder
        $this->validate('amount', 'cardReference');

        if ($this->orderComplete) {
            $data = array(
                'Amount'        => $this->getAmountInteger(),
                'OrderCode'     => $this->getTransactionReference(),
                'CreditCard'    => array(
                    'Token'     => $this->getCardReference(),
                ),
            );
            return array_merge($data, parent::getData());

        } else {
            $data = array(
                'Amount'        => $this->getAmountInteger(),
            );

            return array_merge($data, parent::getData());
        }
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
        if ($this->orderComplete) {
            return parent::getEndpoint() . '/transactions';
        }
        return parent::getEndpoint() . '/orders';
    }

    /**
     * Send the request
     *
     * Note that for the Native Purchase request there are 2 separate API calls.
     * One to create the order, and one to create the transaction.
     *
     * @return ResponseInterface
     */
    public function send()
    {
        // Call 1 -- create order
        $data = $this->getData();
        $response = $this->sendData($data);
        if (! $response->isSuccessful()) {
            return $response;
        }
        $this->orderComplete = true;

        // Call 2 -- create transaction
        $this->setTransactionReference($response->getTransactionReference());
        $data = $this->getData();
        return $this->sendData($data);
    }
}
