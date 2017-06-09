<?php
/**
 * Viva Payments Common (REST) Response
 */

namespace Omnipay\VivaPayments\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Viva Payments Common (REST) Response
 *
 * This is the response class for all Viva Payments REST requests.
 *
 * @see \Omnipay\VivaPayments\RestGateway
 */
class RestResponse extends AbstractResponse
{
    /** @var int */
    protected $statusCode;

    public function __construct(RequestInterface $request, $data, $statusCode = 200)
    {
        parent::__construct($request, $data);
        $this->statusCode = $statusCode;
    }

    public function isRedirect()
    {
        // This can be over-ridden when we know we are expecting a redirect response.
        // Otherwise there is no way of telling from the response data whether a response
        // is a redirect or not.
        return false;
    }

    public function isSuccessful()
    {
        // The Viva Payments gateway returns errors in several possible different ways.
        if ($this->getCode() >= 400) {
            return false;
        }

        if (! empty($this->data['ErrorCode'])) {
            return false;
        }

        if ($this->isRedirect()) {
            return false;
        }

        return true;
    }

    public function getTransactionReference()
    {
        // This is usually correct for refunds
        if (! empty($this->data['TransactionId'])) {
            return $this->data['TransactionId'];
        }

        // This is usually correct for payments, authorizations, etc
        if (! empty($this->data['OrderCode'])) {
            return $this->data['OrderCode'];
        }

        return null;
    }

    public function getMessage()
    {
        if (isset($this->data['ErrorText'])) {
            return $this->data['ErrorText'];
        }
        if (isset($this->data['Message'])) {
            return $this->data['Message'];
        }

        return null;
    }

    public function getCode()
    {
        if (isset($this->data['ErrorCode'])) {
            return $this->data['ErrorCode'];
        }

        return $this->statusCode;
    }
}
