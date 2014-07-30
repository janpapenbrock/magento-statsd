magento-statsd
==============

Statsd integration for Magento 1.x.

Install
-------

Add this to your composer.json

```
    "require": {
        "janpapenbrock/magento-statsd":"dev-master"
    },
    "repositories": [
        {
            "url": "https://github.com/janpapenbrock/magento-statsd",
            "type": "git"
        }
    ]
```

Some sort of autoload manager is required to load additional libraries, namely [liuggio/statsd-php-client](https://github.com/liuggio/statsd-php-client), to Magento autoloader.

Require e.g. [ajbonner/magento-composer-autoload](https://github.com/ajbonner/magento-composer-autoload) via composer:

```
    "require": {
        "ajbonner/magento-composer-autoload":"*"
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
# app/etc/local.xml

<config>
  ...
  <global>
    ...
    <statsd>
      <host>123.123.123.123</host>
      <port>8125</port>
      <protocol>udp</protocol>
    </statsd>
  </global>
  ...
</config>
```
