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
 */
class JanPapenbrock_Statsd_Model_Tracker extends Mage_Core_Model_Abstract
{
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = 8125;
    const DEFAULT_PROTOCOL = 'udp';

    /** @var SocketSender $_client  */
    protected $_sender;

    /** @var StatsdClient $_client  */
    protected $_client;

    /** @var StatsdDataFactory $_factory */
    protected $_factory;

    /** @var array $_data */
    protected $_data = array();

    public function __construct()
    {
        $this->_initConfig();
        $this->_sender  = new SocketSender($this->getHost(), $this->getPort(), $this->getProtocol());
        $this->_client  = new StatsdClient($this->_sender);
        $this->_factory = new StatsdDataFactory('\Liuggio\StatsdClient\Entity\StatsdData');
    }

    public function __destruct()
    {
        $this->send();
    }

    /**
     * Send data to statsd. Clear data array afterwards.
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

    /**
     * Get Statsd factory instance.
     *
     * @return StatsdDataFactory
     */
    public function getFactory()
    {
        return $this->_factory;
    }

    /**
     * Prepare a given key, i.e. prefix it with configured prefix.
     *
     * @param string $key
     *
     * @return string
     */
    public function prepareKey($key)
    {
        return "magento.".$key;
    }

    public function timing($key, $time)
    {
        $key = $this->prepareKey($key);
        $this->_data[] = $this->getFactory()->timing($key, $time);
    }

    public function gauge($key, $value)
    {
        $key = $this->prepareKey($key);
        $this->_data[] = $this->getFactory()->gauge($key, $value);
    }

    public function set($key, $value)
    {
        $key = $this->prepareKey($key);
        $this->_data[] = $this->getFactory()->set($key, $value);
    }

    public function increment($key)
    {
        $key = $this->prepareKey($key);
        $this->_data[] = $this->getFactory()->increment($key);
    }

    public function decrement($key)
    {
        $key = $this->prepareKey($key);
        $this->_data[] = $this->getFactory()->decrement($key);
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
            'protocol' => static::DEFAULT_PROTOCOL
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
