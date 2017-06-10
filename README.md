# Omnipay: VivaPayments

**VivaPayments driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/delatbabel/omnipay-vivapayments.png?branch=master)](https://travis-ci.org/delatbabel/omnipay-vivapayments)
[![StyleCI](https://styleci.io/repos/93733234/shield)](https://styleci.io/repos/93733234)
[![Latest Stable Version](https://poser.pugx.org/delatbabel/omnipay-vivapayments/version.png)](https://packagist.org/packages/delababel/omnipay-vivapayments)
[![Total Downloads](https://poser.pugx.org/delatbabel/omnipay-vivapayments/d/total.png)](https://packagist.org/packages/delatbabel/omnipay-vivapayments)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements VivaPayments support for Omnipay.

[Viva Payments](https://www.vivawallet.com/en-us/company) is a licensed e-money institution
for operations in the EEA-31 region by the Bank of Greece.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "omnipay/vivapayments": "~2.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* VivaPayments_Redirect
* VivaPayments_Native
* VivaPayments_VivaWallet (not yet implemented)

All of these gateways use similar principles (REST) with many of the methods being common between
the three gateways.  I have therefore used an abstract RestGateway class to hold the common methods
but this is not intended to be instantiated separately.

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Documentation

For all documentation, usage examples, etc, see the documentation in the class
docblocks.

There is a copy of the documentation online here (not necessarily up to date):

https://www.babel.com.au/docs/omnipay-vivapayments/namespace-Omnipay.VivaPayments.html

### Quirks

* All payments are in Euros (EUR). No other currency is supported.
* Creating a purchase is a two step process.  Firstly there needs to be
  an order created.  Then, depending on the gateway (Redirect vs REST),
  either the customer is redirected to the gateway or further customer
  information is provided by a second REST call.
* Direct card payments are not supported.  Either a JS plugin is required
  (Native gateway) which creates a card reference, or a redirect is required
  (Redirect gateway).
* It is impossible to tell from the gateway response whether the transaction
  requires a redirect or not.  It's only possible to tell from the type of
  request made.  So I have created separate gateway classes for the different
  types of purchase request (Native vs Redirect) which will return different
  types of response.
* When making a redirect payment, upon completion of the checkout form, the
  customer is redirected back to your website. The redirection URLs are defined
  in your vivapayments.com account under the Sources section.  You cannot provide
  a per-transaction returnUrl or cancelUrl parameter to redirect each transaction
  to a different URL as can be done in some gateways.
* There is no separate void() method.  The refund() method assumes a void() call
  is being made if it is within the same day as the transaction was created.  In
  this case the refund amount must exactly equal the transaction amount.
* There is an authorize() transaction but there is no capture() transaction.
  Pre-authorized amounts stay on hold until they are cancelled or time out (up to 30 days).

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/delatbabel/omnipay-vivapayments/issues),
or better yet, fork the library and submit a pull request.


