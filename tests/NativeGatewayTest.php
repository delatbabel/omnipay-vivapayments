<?php

namespace Omnipay\VivaPayments;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;
use Omnipay\VivaPayments\Message\RedirectResponse;

class NativeGatewayTest extends GatewayTestCase
{
    /** @var  RedirectGateway */
    protected $gateway;

    /** @var  array */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new NativeGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize(array(
            'merchantId'    => 'asdfasdfasdf',
            'apiKey'        => 'asdfasdfasdf',
            'testMode'      => true,
        ));

        $this->options = array(
            'amount'                   => '10.00',
            'currency'                 => 'EUR',
            'description'              => 'This is a test purchase transaction.',
            'transactionId'            => 'TestPurchaseTransaction' . rand(100000, 999999),
            'requestLang'              => 'en-US',
            'clientIp'                 => '127.0.0.1',
            'cardReference'            => 'asdfasdfasdfasdf',
        );
    }
}
