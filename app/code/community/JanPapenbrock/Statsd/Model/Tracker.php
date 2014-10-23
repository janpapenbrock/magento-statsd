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
 * @method bool getActive()
 * @method $this setActive(bool)
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

    /** @var array $_dataItems */
    protected $_dataItems = array();

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
        if (!$this->getActive()) {
            return;
        }
        if (count($this->_dataItems)) {
            $this->_client->send($this->_dataItems);
            $this->_dataItems = array();
        }
    }

    /**
     * Track timing.
     *
     * @param string $key
     * @param int    $time
     *
     * @return $this
     */
    public function timing($key, $time)
    {
        if (!$this->getActive()) {
            return $this;
        }
        $this->_dataItems[] = $this->_getFactory()->timing($this->_prepareKey($key), $time);
    }

    /**
     * Set gauge value.
     *
     * @param string $key
     * @param int    $value
     *
     * @return $this
     */
    public function gauge($key, $value)
    {
        if (!$this->getActive()) {
            return $this;
        }
        $this->_dataItems[] = $this->_getFactory()->gauge($this->_prepareKey($key), $value);
    }

    /**
     * Set value.
     *
     * @param string $key
     * @param int    $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        if (!$this->getActive()) {
            return $this;
        }

        $this->_dataItems[] = $this->_getFactory()->set($this->_prepareKey($key), $value);

        return $this;
    }

    /**
     * Increment counter for key.
     *
     * @param string $key
     *
     * @return $this
     */
    public function increment($key)
    {
        if (!$this->getActive()) {
            return $this;
        }

        $this->_dataItems[] = $this->_getFactory()->increment($this->_prepareKey($key));

        return $this;
    }

    /**
     * Decrement counter for key.
     *
     * @param string $key
     *
     * @return $this
     */
    public function decrement($key)
    {
        if (!$this->getActive()) {
            return $this;
        }

        $key = $this->_prepareKey($key);
        $this->_dataItems[] = $this->_getFactory()->decrement($key);

        return $this;
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
