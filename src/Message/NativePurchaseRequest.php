<?php
/**
 * Viva Payments Native Purchase Request
 */

namespace Omnipay\VivaPayments\Message;

/**
 * Viva Payments Native (REST) Purchase Request
 *
 * In order to create a purchase you must submit the following details:
 *
 * * Amount (numerical)
 * * Reference (string - maximum 30 characters) -- this must be unique.  More
 *   than one transaction using the same Reference value will raise an error.
 * * Customer IP (RFC standard IP address)
 *
 * Either
 *
 * * Card Holder (string)
 * * Card Number (string, numerical, 13 to 16 digits)
 * * Card Expiry (string, mm/yyyy format)
 * * CVV (numerical, 3 or 4 digits)
 *
 * Or
 *
 * * Card Token (String)
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Viva Payments REST Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('VivaPayments_Rest');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'username' => 'TEST',
 *       'token'    => 'TEST',
 *       'testMode' => true, // Or false when you are ready for live transactions
 *   ));
 *
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '4005550000000001',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '123',
 *   ));
 *
 *   // Do a purchase transaction on the gateway
 *   $transaction = $gateway->purchase(array(
 *       'amount'                   => '10.00',
 *       'transactionId'            => 'TestPurchaseTransaction123456',
 *       'clientIp'                 => $_SERVER['REMOTE_ADDR'],
 *       'card'                     => $card,
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Purchase transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * @see Omnipay\VivaPayments\RestGateway
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 */
class NativePurchaseRequest extends AbstractRestRequest
{
    public function getData()
    {
        // An amount parameter is required.  All amounts are in
        // Australian dollars.
        $this->validate('amount', 'transactionId');
        $data = array(
            'amount'        => $this->getAmount(),
            'reference'     => $this->getTransactionId(),
            'customer_ip'   => $this->getClientIp(),
        );

        // A card token can be provided if the card has been stored
        // in the gateway.
        if ($this->getCardReference()) {
            $data['card_token'] = $this->getCardReference();

        // If no card token is provided then there must be a valid
        // card presented.
        } else {
            $this->validate('card');
            $card = $this->getCard();
            $card->validate();
            $data['card_holder'] = $card->getName();
            $data['card_number'] = $card->getNumber();
            $data['card_expiry'] = $card->getExpiryDate('m/Y');
            $data['cvv']         = $card->getCvv();
        }

        return $data;
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
