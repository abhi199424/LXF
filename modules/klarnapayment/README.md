# Klarna Official PrestaShop integration

## PS Addons link
https://addons.prestashop.com/en/other-payment-methods/43440-klarna-payments-official.html

## Tech stack
* PrestaShop 1.7.2-9.0.x
* PHP 7.1+

## Local environment setup
* Standard setup using [ps-module-workspace](https://github.com/Invertus/ps-module-workspace) - follow readme there.
* Use `make prepare` in order to develop the module locally.
* Register for a free Klarna Playground account [here](https://portal.playground.klarna.com/). Get API credentials and client ID from the playground.
* Make sure you use valid currency + address combo for Klarna payment methods to appear in the checkout. Currency must match the country.


## Sample data
* [Sample klarna customer data](https://docs.klarna.com/resources/test-environment/sample-customer-data/)
* [Sample Klarna Official data](https://docs.klarna.com/resources/test-environment/sample-payment-data/)

## Release workflow
https://invertus.atlassian.net/wiki/spaces/IBI/pages/3139502114/Release+workflow

## Demo environments
* Client demo: https://klarnatest.vustweb.online/

## Klarna Official with PS 1.7.2-1.7.4 (PHP <7.2)
On module install:
```
Installation of module klarnapayment failed. Unfortunately, the module did not return additional details.
```
In order Klarna Official to work with PHP versions lower than 7.2, you need to make `klarnapayment.php`
file as simple as possible. It needs to pass Prestashop PHP parser validation (PHP 5.6).
