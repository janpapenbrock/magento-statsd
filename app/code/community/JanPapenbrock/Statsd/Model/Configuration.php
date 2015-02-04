<?php

/**
 * Class JanPapenbrock_Statsd_Model_Configuration
 *
 * @method int getPort()
 * @method $this setPort(int)
 * @method string getHost()
 * @method $this setHost(string)
 * @method string getProtocol()
 * @method $this setProtocol(string)
 * @method string getPrefix()
 * @method $this setPrefix(string)
 * @method bool getActive()
 * @method $this setActive(bool)
 */
class JanPapenbrock_Statsd_Model_Configuration extends Mage_Core_Model_Abstract
{
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = 8125;
    const DEFAULT_PROTOCOL = 'udp';
    const DEFAULT_PREFIX = 'magento';

    protected function _construct()
    {
        parent::_construct();
        $this->_initConfig();
    }

    /**
     * Read values from global configuration.
     *
     * Sample:
     *
     * <config>
     *   <global>
     *     <statsd>
     *       <active>1</active>
     *       <host>123.123.123.123</host>
     *       <port>8125</port>
     *       <protocol>udp</protocol>
     *       <prefix>magento</prefix>
     *     </statsd>
     *   </global>
     * </config>
     */
    protected function _initConfig()
    {
        $config = Mage::getConfig()->getNode('global/statsd');

        $configs = array(
            'active' => 0,
            'host' => static::DEFAULT_HOST,
            'port' => static::DEFAULT_PORT,
            'protocol' => static::DEFAULT_PROTOCOL,
            'prefix' => static::DEFAULT_PREFIX
        );

        foreach ($configs as $configKey => $defaultValue) {
            $this->setDataUsingMethod($configKey, $this->_getConfigValue($config, $configKey, $defaultValue));
        }
    }

    /**
     * @param Mage_Core_Model_Config|null $config  Configuration node.
     * @param string                      $key     Config key to get value for.
     * @param mixed                       $default Default value for this key.
     *
     * @return mixed
     */
    protected function _getConfigValue($config, $key, $default)
    {
        if ($config) {
            $result = (string) $config->descend($key) ?: $default;
        } else {
            $result = $default;
        }
        return $result;
    }
}
