magento-statsd
==============

Statsd integration for Magento 1.x.

*Important: Cannot be installed via Composer right now! Working on it.*

Configuration
-------------



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
