[![Build Status](https://travis-ci.org/eddiejaoude/zf2-logger.svg?branch=master)](https://travis-ci.org/eddiejaoude/zf2-logger)
[![Coverage Status](https://coveralls.io/repos/eddiejaoude/zf2-logger/badge.png?branch=master)](https://coveralls.io/r/eddiejaoude/zf2-logger?branch=master)
[![Total Downloads](https://poser.pugx.org/eddiejaoude/zf2-logger/downloads.png)](https://packagist.org/packages/eddiejaoude/zf2-logger)
[![Dependency Status](https://www.versioneye.com/user/projects/531978c3ec1375b69c0009f2/badge.png)](https://www.versioneye.com/user/projects/531978c3ec1375b69c0009f2)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/eddiejaoude/zf2-logger/badges/quality-score.png?s=caf5d25d4eb79a84e168cf6836e11ac68518d7c8)](https://scrutinizer-ci.com/g/eddiejaoude/zf2-logger/)

# EddieJaoude\Zf2Logger

#### Zend Framework 2 Event Logger.
#### Log incoming Requests &amp; Response data with host name
#### Manually log your application information with priorities (i.e. emerg..debug)
#### Change your logging output via config without changing code

---

## Installation via Composer

### Steps 

#### 1. Add to composer.

```
    "require" : {
        "eddiejaoude/zf2-logger" : "0.*"
    }
```

#### 2. [OPTIONAL] To override or add additional configuration create the file ```zf2Logger.global.php``` in ```config/autoload``` with configuration (/config/module.config.php)

```
    /module.config.php to /config/autoload/zf2Logger.global.php
```

#### 3. Add module to application config (/config/application.config.php)

```PHP
   //...
   'modules' => array(
        'EddieJaoude\Zf2Logger',
   ),
   //...
```

Then you are good to go. Logging READY! All requests & responses will be logged automatically as ```DEBUG```

---

## Example usage of manual logging & prority

As the ```Zend\Log\Logger``` is returned from the Service call, one can use the methods:
* emerg  // Emergency: system is unusable
* alert  // Alert: action must be taken immediately
* crit   // Critical: critical conditions
* err    // Error: error conditions
* warn   // Warning: warning conditions
* notice // Notice: normal but significant condition
* info   // Informational: informational messages
* debug  // Debug: debug messages

```PHP
    //...
    $serviceLocator->get('EddieJaoude\Zf2Logger')->emerg('Emergency message');
    //...
```

### Use an alias for decoupling

Instead of using `EddieJaoude\Zf2Logger` in your code, put an `Alias` in your service manager, therefore allowing you to swap out different logger libraries later on without modifying your code & usage.

i.e.
```
    //...
    'aliases'    => array(
        // alias used, so can be swapped out later without changing any code
        'Logger' => 'EddieJaoude\Zf2Logger'
    ),
    //...
```

Then your usage in your code becomes...

```PHP
    //...
    $serviceLocator->get('Logger')->emerg('Emergency message');
    //...
```

---

## Example - built in logging

Each output includes & is prepended with the host - this is especially useful when working with multi layer/tier architecture, i.e. F/E (UI) -> B/E (API). As these can all write to the same output in the stack execution order or alternatively to different outputs.

### Request (priority DEBUG)

```
    2014-01-09T16:28:23+00:00 DEBUG (7): Array
    (
        [zf2.local] => Array
            (
                [Request] => Zend\Uri\Http Object
                    (
                        [validHostTypes:protected] => 19
                        [user:protected] =>
                        [password:protected] =>
                        [scheme:protected] => http
                        [userInfo:protected] =>
                        [host:protected] => zf2.local
                        [port:protected] =>
                        [path:protected] => /api/user
                        [query:protected] =>
                        [fragment:protected] =>
                    )

            )

    )
```

### Response (priority DEBUG)

```
    2014-01-09T16:28:24+00:00 DEBUG (7): Array
    (
        [zf2.local] => Array
            (
                [Response] => Array
                    (
                        [statusCode] => 200
                        [content] => {"total":2,"data":[{"id":"12345 ...
                        ...
                    )
            )
    )
```

---

## Configuration (config)

```PHP
    return array(
        'EddieJaoude\Zf2Logger' => array(

            // will add the $logger object before the current PHP error handler
            'registerErrorHandler'     => 'true', // errors logged to your writers
            'registerExceptionHandler' => 'true', // exceptions logged to your writers

            // multiple zend writer output & zend priority filters
            'writers' => array(
                'standard-file' => array(
                    'adapter'  => '\Zend\Log\Writer\Stream',
                    'options'  => array(
                        'output' => 'data/application.log', // path to file
                    ),
                    // options: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG
                    'filter' => \Zend\Log\Logger::DEBUG,
                    'enabled' => true
                ),
                'tmp-file' => array(
                    'adapter'  => '\Zend\Log\Writer\Stream',
                    'options'  => array(
                        'output' => '/tmp/application-' . $_SERVER['SERVER_NAME'] . '.log', // path to file
                    ),
                    // options: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG
                    'filter' => \Zend\Log\Logger::DEBUG,
                    'enabled' => false
                ),
                'standard-output' => array(
                    'adapter'  => '\Zend\Log\Writer\Stream',
                    'options'  => array(
                        'output' => 'php://output'
                    ),
                    // options: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG
                    'filter' => \Zend\Log\Logger::NOTICE,
                    'enabled' => $_SERVER['APPLICATION_ENV'] == 'development' ? true : false
                ),
                'standard-error' => array(
                    'adapter'  => '\Zend\Log\Writer\Stream',
                    'options'  => array(
                        'output' => 'php://stderr'
                    ),
                    // options: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG
                    'filter' => \Zend\Log\Logger::NOTICE,
                    'enabled' => true
                )
            )
        )
    );

```

---

## Unit tests

To run unit tests (from root diectory)

```
vendor/bin/phpunit -c tests/phpunit.xml
```

---

## What Next...

* Additional events

Ideas & requirements welcome.

---

## Contributing

* Discussions from Ideas & Discussions to Pull Requests
* Pull requests with Unit tests

---

## Resources

* Github https://github.com/eddiejaoude/zf2-logger
* Packagist https://packagist.org/packages/eddiejaoude/zf2-logger
* Zend Framework 2 Modules http://modules.zendframework.com/eddiejaoude/zf2-logger
* Travis CI https://travis-ci.org/eddiejaoude/zf2-logger
* Coveralls https://coveralls.io/r/eddiejaoude/zf2-logger
* Scrutinizer https://scrutinizer-ci.com/g/eddiejaoude/zf2-logger/

