<?php
/**
 * Viva Payments (REST) Fetch Transactions Request
 */

namespace Omnipay\VivaPayments\Message;

/**
 * Viva Payments (REST) Fetch Transactions Request
 *
 * This method allows you to obtain:
 *
 * * Details for all transactions for a given Payment Order.
 * * A list of all transactions that occurred on a given day.
 *
 * ### Example
 *
 * Possible parameters are:
 *
 * * date -- the date on which the transaction was made.
 * * clearanceDate -- the date on which the transaction was cleared.
 * * transactionReference -- the ID of a transaction, as returned by the GET parameter
 *   when redirecting a customer back to your site.  This will be a 36 character UUID.
 * * transactionId -- the Order Code which will be the 12 or 16 digit code returned from
 *   a purchase() request.
 *
 * <code>
 * $transaction = $gateway->fetchTransactions(array(
 *     'transactionReference'  => $transaction_id,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     $transactionList = $response->getData();
 * } else {
 *     echo "Fetch transactions failed.\n";
 *     echo "Error code == " . $response->getCode() . "\n";
 *     echo "Error message == " . $response->getMessage() . "\n";
 * }
 * </code>
 *
 * @see Omnipay\VivaPayments\RestGateway
 * @link https://github.com/VivaPayments/API/wiki
 * @link https://www.vivawallet.com/en-us/company
 * @link https://github.com/VivaPayments/API/wiki/GetTransactions
 */
class FetchTransactionsRequest extends AbstractRestRequest
{
    /**
     * Get the transaction date.
     *
     * YYYY-MM-DD format appears to be used by the gateway.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->getParameter('date');
    }

    /**
     * Set the transaction date.
     *
     * YYYY-MM-DD format appears to be used by the gateway.
     *
     * @param string $value
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setDate($value)
    {
        return $this->setParameter('date', $value);
    }

    /**
     * Get the transaction clearance date.
     *
     * YYYY-MM-DD format appears to be used by the gateway.
     *
     * @return string
     */
    public function getClearanceDate()
    {
        return $this->getParameter('clearanceDate');
    }

    /**
     * Set the transaction clearance date.
     *
     * YYYY-MM-DD format appears to be used by the gateway.
     *
     * @param string $value
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setClearanceDate($value)
    {
        return $this->setParameter('clearanceDate', $value);
    }

    public function getData()
    {
        $data = array();

        if ($this->getDate()) {
            $data['date'] = $this->getDate();
        }
        if ($this->getClearanceDate()) {
            $data['clearancedate'] = $this->getClearanceDate();
        }
        if ($this->getTransactionId()) {
            $data['ordercode'] = $this->getTransactionId();
        }

        return array_merge($data, parent::getData());
    }

    protected function getHttpMethod()
    {
        return 'GET';
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
        if ($this->getTransactionReference()) {
            return parent::getEndpoint() . '/transactions/' . $this->getTransactionReference();
        }
        return parent::getEndpoint() . '/transactions/';
    }
}
