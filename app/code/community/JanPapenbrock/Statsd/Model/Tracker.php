<?php

use Liuggio\StatsdClient\StatsdClient,
    Liuggio\StatsdClient\Factory\StatsdDataFactory,
    Liuggio\StatsdClient\Sender\SocketSender;

/**
 * Class JanPapenbrock_Statsd_Model_Tracker
 *
 * @method int getPort()
 * @method $this setPort(int)
 * @method string getHost()
 * @method $this setHost(string)
 * @method string getProtocol()
 * @method $this setProtocol(string)
 * @method string getPrefix()
 * @method $this setPrefix(string)
 */
class JanPapenbrock_Statsd_Model_Tracker extends Mage_Core_Model_Abstract
{
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = 8125;
    const DEFAULT_PROTOCOL = 'udp';
    const DEFAULT_PREFIX = 'magento';

    /** @var SocketSender $_client  */
    protected $_sender;

    /** @var StatsdClient $_client  */
    protected $_client;

    /** @var StatsdDataFactory $_factory */
    protected $_factory;

    /** @var array $_data */
    protected $_data = array();

    /**
     * Construct this tracker.
     */
    public function __construct()
    {
        $this->_initConfig();
        $this->_sender  = new SocketSender($this->getHost(), $this->getPort(), $this->getProtocol());
        $this->_client  = new StatsdClient($this->_sender);
        $this->_factory = new StatsdDataFactory('\Liuggio\StatsdClient\Entity\StatsdData');
    }

    /**
     * On destruction, any data collected is sent.
     */
    public function __destruct()
    {
        $this->send();
    }

    /**
     * Send data to statsd. Clear data cache afterwards.
     *
     * @return void.
     */
    public function send()
    {
        if (count($this->_data)) {
            $this->_client->send($this->_data);
            $this->_data = array();
        }
    }

    public function timing($key, $time)
    {
        $this->_data[] = $this->_getFactory()->timing($this->_prepareKey($key), $time);
    }

    public function gauge($key, $value)
    {
        $this->_data[] = $this->_getFactory()->gauge($this->_prepareKey($key), $value);
    }

    public function set($key, $value)
    {
        $this->_data[] = $this->_getFactory()->set($this->_prepareKey($key), $value);
    }

    public function increment($key)
    {
        $this->_data[] = $this->_getFactory()->increment($this->_prepareKey($key));
    }

    public function decrement($key)
    {
        $key = $this->_prepareKey($key);
        $this->_data[] = $this->_getFactory()->decrement($key);
    }

    /**
     * Get Statsd factory instance.
     *
     * @return StatsdDataFactory
     */
    protected function _getFactory()
    {
        return $this->_factory;
    }

    /**
     * Prepare a given key, i.e. prefix it with configured prefix.
     *
     * @param string $key Key.
     *
     * @return string
     */
    protected function _prepareKey($key)
    {
        $result = $key;

        if ($this->getPrefix()) {
            $result = $this->getPrefix().'.'.$key;
        }

        return $result;
    }

    /**
     * Read values from global configuration.
     *
     * Sample:
     *
     * <config>
     *   <global>
     *     <statsd>
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
