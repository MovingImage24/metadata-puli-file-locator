# Metadata PuliFileLocator

[![Build Status](https://travis-ci.org/MovingImage24/metadata-puli-file-locator.svg?branch=master)](https://travis-ci.org/MovingImage24/metadata-puli-file-locator)
[![Latest Stable Version](https://poser.pugx.org/mi/metadata-puli-file-locator/v/stable)](https://packagist.org/packages/mi/metadata-puli-file-locator)
[![Latest Unstable Version](https://poser.pugx.org/mi/metadata-puli-file-locator/v/unstable)](https://packagist.org/packages/mi/metadata-puli-file-locator)
[![Total Downloads](https://poser.pugx.org/mi/metadata-puli-file-locator/downloads)](https://packagist.org/packages/mi/metadata-puli-file-locator)
[![License](https://poser.pugx.org/mi/metadata-puli-file-locator/license)](https://packagist.org/packages/mi/metadata-puli-file-locator)
[![StyleCI](https://styleci.io/repos/37872708/shield)](https://styleci.io/repos/37872708)

## Overview

...

## Installation

1. Add this library to your project as a composer dependency:

```bash
composer require mi/metadata-puli-file-locator
```

## Usage

### PuliDiscovery

For the usage of the puli discovery file locator use the PuliDiscoveryDriverFactory.
To configure the metadata bind the query to "jms/serializer-metadata" with the parameter 
for the namespace prefix and the extension.

```bash
php puli.phar bind /puli/path/to/file/*.xml jms/serializer-metadata --param extension="xml" \
--namespace-prefix="Vendor\Namespace\Prefix"
```


```php
$serializer =
JMS\Serializer\SerializerBuilder::create()
    ->setMetadataDriverFactory(new Mi\Puli\Serializer\Builder\PuliDiscoveryDriverFactory($puliDiscovery))
    ...
    ->build();
```

### PuliRepository

For the usage of the puli repository file locator use the PuliRepositoryDriverFactory.
Also the metadata are configure with the puli paths and namespace prefix.

```php
$serializer =
JMS\Serializer\SerializerBuilder::create()
    ->addMetadataDir('/puli/path/to/file', 'Vendor\Namespace\Prefix')
    ->setMetadataDriverFactory(new Mi\Puli\Serializer\Builder\PuliRepositoryDriverFactory($puliRepository))
    ...
    ->build();
```
## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

# License

This library is under the [MIT license](https://github.com/MovingImage24/metadata-puli-file-locator/blob/master/LICENSE).