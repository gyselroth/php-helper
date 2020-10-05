PHP Helper
==========

[![Build Status](https://travis-ci.org/gyselroth/php-helper.svg?branch=master)](https://travis-ci.org/gyselroth/php-helper)
[![CodeFactor](https://www.codefactor.io/repository/github/gyselroth/php-helper/badge/master)](https://www.codefactor.io/repository/github/gyselroth/php-helper/overview/master)
[![GitHub license](https://img.shields.io/badge/license-apache_2.0-blue.svg)](https://raw.githubusercontent.com/gyselroth/php-helper/master/LICENSE)

PHP helper methods upon primitive data types (Array, Float, Integer, String, etc.) 
and common data structures (e.g. HTML, ZIP, XML etc.).


## Table of contents

* [Features](#features)
* [Log-Wrapper](#log-wrapper)
* [Minimum Requirements](#minimum-requirements)
* [Installation](#installation)
  * [For use within your application](#for-use-within-your-application)
  * [Standalone-installation / For developing on the php-helper package](#standalone-installation--for-developing-the-php-helper-package)
* [Running Tests](#running-tests)
* [Running phpstan](#running-phpstan)
* [Contributing](#contributing)
* [History](#history)
* [Author and License](#author-and-license)
* [Used Open Source Software](#used-open-source-software)


Features
--------

Provides helpers for conversion-/modification-, validation-, extraction-/search-, and many more for:

* Date/Time handling
* File I/O
* HTML
* Image
* JSON
* Numeric
* Reflection
* Server/Client 
* String
* XML
* ZIP


### Log-Wrapper

This package uses the [gyselroth logger wrapper](https://github.com/gyselroth/php-helper-log).
See it's documentation for further info on how to setup and use it from within PHP applications of different
frameworks.


Minimum Requirements
--------------------

* See [composer.json](https://github.com/gyselroth/php-helper/blob/master/composer.json)


Installation
------------

### For use within your application

```sh
composer require gyselroth/php-helper
```


### Standalone-installation / For developing the php-helper package

```sh
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer install
```


Running Tests
-------------

```sh
composer test
```


Running phpstan
---------------

```sh
 vendor/bin/phpstan analyse /srv/www/trunk/src -c /srv/www/trunk/var/ci/phpstan/phpstan.neon
```


Contributing
------------

See [CONTRIBUTING.md](https://github.com/gyselroth/php-helper/blob/master/CONTRIBUTING.md)


History
-------

See [CHANGELOG.md](https://github.com/gyselroth/php-helper/blob/master/CHANGELOG.md)


Author and License
------------------

Copyright 2017-2020 gyselroth™ (http://www.gyselroth.com)

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0":http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License. 


### Used Open Source Software

Open source packages used by the gyselroth Helper Library are copyright of their vendors, see related licenses within
the vendor packages.
