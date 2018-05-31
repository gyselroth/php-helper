gyselroth PHP Helper Library
============================

Collection of PHP helper methods upon primitive data types and structures.


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
 ...
 
 $app = new \Slim\App($settings);

 $container = $app->getContainer();
 
 ...

 $container['logger'] = function (...) {
     // Callback to PSR-7 logger, e.g. Monolog
     ...
 }
 
 // Register host application's logger component within gyselroth Helper's logger wrapper
 new \Gyselroth\Helper\LoggerWrapper($app->getContainer()['logger']);
```


#### Initialization Example 2: Within Zend Framework 1 application  

As there's no service container for dependency injection within Zend Framework 1 yet,
the helper classes will by convention draw your logger (probably Zend_Log) from the Zend_Registry

```php
<?php 
 use Gyselroth\Helper\;
 ...
 
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


Running Tests
-------------

```sh
composer test tests/
```


History
-------

See `CHANGELOG.md`


Author and License
------------------

Copyright 2017-2018 gyselroth™ (http://www.gyselroth.com)

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

Open source packages used by the gyselroth Document Engine are copyright of their vendors, see related licenses within
the vendor packages.
