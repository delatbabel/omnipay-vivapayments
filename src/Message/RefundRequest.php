<?php
/**
 * Viva Payments (REST) Refund Request
 */

namespace Omnipay\VivaPayments\Message;

/**
 * Viva Payments (REST) Refund Request
 *
 * This method allows you to:
 *
 * * Cancel a card payment occurred within the same business day (before 22:00 GMT+2).
 * * Make a partial or full refund of a successful payment that has already been cleared.
 *
 * ### Example
 *
 * This example assumes that the payment is successful and the transaction ID is stored
 * in $transaction_id
 *
 * <code>
 * $transaction = $gateway->refund(array(
 *     'amount'                => '10.00',
 *     'transactionReference'  => $transaction_id,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     $refund_id = $response->getTransactionReference();
 *     echo "Refund transaction successful.\n";
 *     echo "Refund transaction reference = " . $refund_id . "\n";
 * } else {
 *     echo "Refund transaction failed.\n";
 *     echo "Error code == " . $response->getCode() . "\n";
 *     echo "Error message == " . $response->getMessage() . "\n";
 * }
 * </code>
 *
 * ### Quirks
 *
 * * If this refund request is happening on the same day, the gateway assumes that
 *   the card payment is being cancelled, and the refund amount must exactly match
 *   the payment amount, or an error will be thrown.
 *
 * @see Omnipay\VivaPayments\RestGateway
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 * @link https://github.com/VivaPayments/API/wiki/CancelTransaction
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
