<?php
/**
 * Viva Payments Abstract REST Request
 */

namespace Omnipay\VivaPayments\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * Viva Payments Abstract REST Request
 *
 * This is the parent class for all Viva Payments REST requests.
 *
 * @link http://www.paystream.com.au/developer-guides/
 * @see \Omnipay\VivaPayments\RestGateway
 */
abstract class AbstractRestRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * Sandbox Endpoint URL
     *
     * @var string URL
     */
    protected $testEndpoint = 'http://demo.vivapayments.com';

    /**
     * Live Endpoint URL
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://www.vivapayments.com';

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * Get base endpoint URL
     *
     * @return string
     */
    protected function getBaseEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * Get API endpoint URL
     *
     * @return string
     */
    protected function getEndpoint()
    {
        $base = $this->getBaseEndpoint();
        return $base . '/api';
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
     * @return AbstractRestRequest provides a fluent interface.
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
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get the gateway language
     *
     * The language (in ISO format) that the payment form is displayed. This
     * parameter is also included in the target URL the application uses to
     * redirect the user after the successful/unsuccessful completion of a payment.
     *
     * Note: If no parameter is passed, the system assumes el-GR. The payment form
     * is currently displayed in the English language for all values other than el-GR.
     *
     * @return string
     */
    public function getRequestLang()
    {
        return $this->getParameter('requestLang');
    }

    /**
     * Set the gateway language
     *
     * The language (in ISO format) that the payment form is displayed. This
     * parameter is also included in the target URL the application uses to
     * redirect the user after the successful/unsuccessful completion of a payment.
     *
     * Note: If no parameter is passed, the system assumes el-GR. The payment form
     * is currently displayed in the English language for all values other than el-GR.
     *
     * @param string $value
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setRequestLang($value)
    {
        return $this->setParameter('requestLang', $value);
    }

    /**
     * Get the transaction source code.
     *
     * Sources can be created and managed from the merchant's profile. A source is
     * used for grouping and filtering transactions (ie a merchant may have more
     * than one websites, or different product categories).
     *
     * Note: SourceCode is case-sensitive.
     *
     * @return string
     */
    public function getSourceCode()
    {
        return $this->getParameter('sourceCode');
    }

    /**
     * Set the transaction source code.
     *
     * Sources can be created and managed from the merchant's profile. A source is
     * used for grouping and filtering transactions (ie a merchant may have more
     * than one websites, or different product categories).
     *
     * Note: SourceCode is case-sensitive.
     *
     * @param string $value
     * @return AbstractRestRequest provides a fluent interface.
     */
    public function setSourceCode($value)
    {
        return $this->setParameter('sourceCode', $value);
    }

    public function getData()
    {
        // Optional and common parameters
        // https://github.com/VivaPayments/API/wiki/Optional-Parameters
        $data = array(
            'RequestLang'   => $this->getRequestLang(),
            'MerchantTrns'  => $this->getTransactionId(),
            'CustomerTrns'  => $this->getDescription(),
            'SourceCode'    => $this->getSourceCode(),
        );

        return $data;
    }

    /**
     * Creates the correct type of response for this request.
     *
     * This is a hook to allow different types of request to create their own response
     * types, normally used when there is a redirect request of some kind.
     *
     * @param RequestInterface $request
     * @param                  $data
     * @param int              $statusCode
     * @return RestResponse
     */
    protected function createResponse(RequestInterface $request, $data, $statusCode = 200)
    {
        return new RestResponse($this, $data, $statusCode);
    }

    public function sendData($data)
    {
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        // POST requests require different parameter sending methods to the other requests.
        if ($this->getHttpMethod() == 'POST') {
            $httpRequest = $this->httpClient->createRequest(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                array(
                    'Accept'         => 'application/json',
                    'Content-type'   => 'application/json',
                ),
                json_encode($data)
            )->setAuth($this->getMerchantId(), $this->getApiKey());
        } else {
            $httpRequest = $this->httpClient->createRequest(
                $this->getHttpMethod(),
                $this->getEndpoint() . '?' . http_build_query($data),
                array(
                    'Accept'         => 'application/json',
                )
            )->setAuth($this->getMerchantId(), $this->getApiKey());
        }

        // Might be useful to have some debug code here.  Perhaps hook to whatever
        // logging engine is being used.
        // echo "Data == " . json_encode($data) . "\n";

        $httpResponse = $httpRequest->send();

        // HTTP Response raw data used for making mocks
        echo "HTTP Raw Headers\n\n";
        echo $httpResponse->getRawHeaders();
        echo "\n\n";
        echo "HTTP Raw Body\n\n";
        echo $httpResponse->getBody(true);
        echo "\n\n";

        // Get body parsed from JSON if it's not empty
        $rawBody = $httpResponse->getBody(true);
        if (empty($rawBody)) {
            return $this->response = $this->createResponse($this, array(), $httpResponse->getStatusCode());
        }

        return $this->response = $this->createResponse($this, $httpResponse->json(), $httpResponse->getStatusCode());
    }
}
