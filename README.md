gyselroth PHP Helper Library
============================

Collection of PHP helper methods upon primitive data types (Array, Float, Integer, String, etc.) 
and common data structures (e.g. HTML, ZIP, XML etc.).


## Table of contents

* [Features](#features)
* [Log-Wrapper](#log-wrapper)
  * [Initialization Example 1: Within Slim PHP application](#initialization-example-1-within-slim-php-application)
  * [Initialization Example 2: Within Zend Framework 1 application](#initialization-example-2-within-zend-framework-1-application)
* [Minimum Requirements](#minimum-requirements)
* [Installation](#installation)
  * [For use within your application](#for-use-as-a-dependency-within-your-application)
  * [Standalone-installation / For developing on the php-helper package](#standalone-installation-for-developing-on-the-php-helper-package)
* [Running Tests](#running-tests)
* [Contributing](#contributing)
* [History](#history)
* [Author and License](#author-and-license)
* [Used Open Source Software](#used-open-source-software)


Features
--------

Contains helpers for conversion-/modification-, validation-, extraction-/search-, and many more for:

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

To allow classes of this helper library to log, using the standard logger of the framework used in the rest of 
your application, the library includes ```Gyselroth\Helper\LogWrapper```.
After bootstrapping your application or its service container (if using a framework able of dependency-injection),
the host application's logger can be registered with the Logger-Wrapper (skip this step if you do not use the helper library 
to write any log-entries):

  
#### Initialization Example 1: Within Slim PHP application
```php
<?php 
 use Gyselroth\Helper;
 //...
 
 $app = new \Slim\App($settings);

 $container = $app->getContainer();
 
 //...

 $container['logger'] = function (/*...*/) {
     // Callback to PSR-7 logger, e.g. Monolog
     //...
 };
 
 // Register host application's logger component within gyselroth Helper's logger wrapper
 new \Gyselroth\Helper\LoggerWrapper($app->getContainer()['logger']);
```


#### Initialization Example 2: Within Zend Framework 1 application

As there's no service container for dependency injection within Zend Framework 1 yet,
the helper classes will by convention draw your logger (probably Zend_Log) from the Zend_Registry

```php
<?php 
 use Gyselroth\Helper\;
 //...
 
 $app = new Zend_Application($env, $config);
 $app->bootstrap();
  
 // Register ZF1 logger class within the helper library's logger-wrapper
 new LoggerWrapper('App_Log');
 
 $app->run();
```


Minimum Requirements
--------------------

* PHP7.1-frm with extensions: php7.1-fpm, openssl, php7.1-xml
* Build-tools: git, composer


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

Contributing
------------

See [CONTRIBUTING.md](https://github.com/gyselroth/php-helper/blob/master/CONTRIBUTING.md)


History
-------

See [CHANGELOG.md](https://github.com/gyselroth/php-helper/blob/master/CHANGELOG.md)


Author and License
------------------

Copyright 2017-2019 gyselroth™ (http://www.gyselroth.com)

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
