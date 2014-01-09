# EddieJaoude\zf2Logger

#### Zend Framework 2 Event Logger.
#### Log incoming Requests &amp; Response data.

---

## Installation via Composer

### Steps 

#### 1. Add to composer.
```
    "require" : {
        "eddiejaoude/zf2-logger" : "dev-master"
    }
```

#### 2. Create `zf2logger.global.php` in `config/autoload` with configuration
```
    /module.config.php.dist to /config/autoload/zf2Logger.global.php
```

#### 3. Add module to application config (/config/application.config.php)
```
   ...
   'modules' => array(
        'Zf2Logger',
   ),
   ...
```


---




