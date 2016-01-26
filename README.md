# WebHooker SDK for PHP

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

Interact with the WebHooker.io API

## Install

Via Composer

```bash
composer require webhooker/webhooker-sdk
```

You must also have Guzzle installed. By default, the package is only compatible with Guzzle 6:

```bash
composer require guzzlehttp/guzzle
```

## Usage

Instantiate:

```php
$webhooker = Webhooker\Webhooker::usingGuzzle('YOUR-API-KEY');
```

Add a subscriber:

```php
$subscriber = $webhooker->addSubscriber('Their Name Here');

$subscriber->id;
```

Add an endpoint for the subscriber to receive messages:

```php
// you can use "jsonSubscription" or "xmlSubscription"
$subscription = $webhooker->subscriber($id)->jsonSubscription($tenantKey, $deliveryUrl, $secret)->save();
$subscription = $webhooker->subscriber($id)->xmlSubscription($tenantKey, $deliveryUrl, $secret)->save();

// additional options for a subscription, e.g. Basic Auth, or "Legacy Payloads"
$subscription = $webhooker->subscriber($id)
  ->jsonSubscription($tenantKey, $deliveryUrl, $secret)
  ->basicAuth($username, $password)
  ->legacyMode('p_reply')
  ->save();

$subscription->id;
```

Send a JSON message:

```php
// $jsonData can be something JSON-able (array/object) or a pre-encoded JSON string
$webhooker->notify('account-1', 'something.happened')->json($jsonData)->send();
```

Or, because JSON is very common, just pass it directly to the `send()` method:

```php
$webhooker->notify('account-1', 'something.happened')->send($jsonData);
```

Send an XML message:

```php
// $xmlData must be an XML string
$webhooker->notify('account-1', 'something.happened')->xml($xmlData)->send();
```

Send both XML and JSON messages:

```php
$webhooker->notify('account-1', 'something.happened')->xml($xmlData)->json($jsonData)->send();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email dan@radweb.co.uk instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/webhooker/webhooker-sdk.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/WebHooker/webhooker-sdk-php/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/webhooker/webhooker-sdk.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/webhooker/webhooker-sdk
[link-travis]: https://travis-ci.org/WebHooker/webhooker-sdk-php
[link-downloads]: https://packagist.org/packages/webhooker/webhooker-sdk
