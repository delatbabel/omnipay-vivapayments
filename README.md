# Omnipay: VivaPayments

**VivaPayments driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/delatbabel/omnipay-vivapayments.png?branch=master)](https://travis-ci.org/delatbabel/omnipay-vivapayments)
[![StyleCI](https://styleci.io/repos/93733234/shield)](https://styleci.io/repos/93733234)

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

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/delatbabel/omnipay-vivapayments/issues),
or better yet, fork the library and submit a pull request.


