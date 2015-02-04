magento-statsd
==============

Statsd integration for Magento 1.x.

Install
-------

Add this to your composer.json

```
    "require": {
        "janpapenbrock/magento-statsd": "dev-master"
    },
    "repositories": [
        {
            "url": "https://github.com/janpapenbrock/magento-statsd",
            "type": "git"
        }
    ]
```

Some sort of autoload manager is required to load additional libraries, namely [liuggio/statsd-php-client](https://github.com/liuggio/statsd-php-client), to Magento autoloader.

Require e.g. [firegento/psr0autoloader](https://github.com/magento-hackathon/Magento-PSR-0-Autoloader) via composer:

```
    "require": {
        "firegento/psr0autoloader":"*"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "http://packages.firegento.com"
        }
    ]
```


Configure
---------

```
# app/etc/statsd.xml

<?xml version="1.0"?>
<config>
  <global>
    <statsd>
      <active>1</active>
      <host>123.123.123.123</host>
      <port>8125</port>
      <protocol>udp</protocol>
      <prefix>servername.magento.production</prefix>
    </statsd>
  </global>
</config>
```
