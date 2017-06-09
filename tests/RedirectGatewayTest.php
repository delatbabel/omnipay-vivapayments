<?php

namespace Omnipay\VivaPayments;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Common\CreditCard;
use Omnipay\VivaPayments\Message\RedirectResponse;

class RedirectGatewayTest extends GatewayTestCase
{
    /** @var  RedirectGateway */
    protected $gateway;

    /** @var  array */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new RedirectGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize(array(
            'merchantId'    => 'asdfasdfasdf',
            'apiKey'        => 'asdfasdfasdf',
            'testMode'      => true,
        ));

        $this->card = new CreditCard(array(
            'firstName' => 'Example',
            'lastName' => 'User',
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2020',
            'cvv' => '123',
        ));
        $this->options = array(
            'amount'                   => '10.00',
            'currency'                 => 'EUR',
            'description'              => 'This is a test purchase transaction.',
            'transactionId'            => 'TestPurchaseTransaction' . rand(100000, 999999),
            'requestLang'              => 'en-US',
            'clientIp'                 => '127.0.0.1',
        );
    }

    public function testPurchase()
    {
        $this->setMockHttpResponse('RedirectPurchaseSuccess.txt');

        /** @var RedirectResponse $response */
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isRedirect());
        $this->assertEquals('7685364763872608', $response->getTransactionReference());
        $this->assertEquals('http://demo.vivapayments.com/web/checkout?ref=7685364763872608', $response->getRedirectUrl());
        $this->assertEquals('GET', $response->getRedirectMethod());
        $this->assertEmpty($response->getRedirectData());
        $this->assertEmpty($response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('RedirectPurchaseFailure.txt');

        /** @var RedirectResponse $response */
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('401', $response->getCode());
        $this->assertEmpty($response->getMessage());
    }

    public function testRefund()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $response = $this->gateway->refund(array(
            'transactionReference'  => "57a7cdd1-b9b6-4061-b7a2-d108f21e5d2b",
            'amount'                => 10.00,
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertEquals('d051e50c-ad9e-4218-b635-edccb36fe71a', $response->getTransactionReference());
    }

    public function testRefundFailure()
    {
        $this->setMockHttpResponse('RefundFailure.txt');

        $response = $this->gateway->refund(array(
            'transactionReference'  => "57a7cdd1-b9b6-4061-b7a2-d108f21e5d2b",
            'amount'                => 10.00,
        ))->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals(403, $response->getCode());
        $this->assertEquals('Non reversible transaction', $response->getMessage());
    }

    public function testFetchTransactions()
    {
        $this->setMockHttpResponse('FetchTransactionsSuccess.txt');

        $response = $this->gateway->fetchTransactions(array(
            'transactionReference'  => "1313495f-cd9e-48bc-8bfc-ee4903cfb308",
        ))->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEmpty($response->getMessage());
        $this->assertEmpty($response->getCode());
        $this->assertArrayHasKey('Transactions', $response->getData());
    }
}
