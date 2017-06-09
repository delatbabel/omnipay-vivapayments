<?php
/**
 * Viva Payments Redirect (REST) Response
 */

namespace Omnipay\VivaPayments\Message;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Viva Payments Redirect (REST) Response
 *
 * This is the response class for Viva Payments Redirect requests.
 *
 * @see \Omnipay\VivaPayments\RestGateway
 */
class RedirectResponse extends RestResponse implements RedirectResponseInterface
{
    /** @var string  */
    protected $baseEndpoint;

    public function __construct(
        RequestInterface $request,
        $data,
        $statusCode = 200,
        $baseEndpoint = "http://demo.vivapayments.com"
    ) {
        parent::__construct($request, $data, $statusCode);
        $this->baseEndpoint = $baseEndpoint;
    }

    public function isRedirect()
    {
        // The Viva Payments gateway returns errors in several possible different ways.
        if ($this->getCode() >= 400) {
            return false;
        }

        if (! empty($this->data['ErrorCode'])) {
            return false;
        }

        return true;
    }

    public function getRedirectUrl()
    {
        return $this->baseEndpoint . '/web/checkout?ref=' . $this->getTransactionReference();
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }
}
