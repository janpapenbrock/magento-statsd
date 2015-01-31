<?php

use Liuggio\StatsdClient\StatsdClient,
    Liuggio\StatsdClient\Factory\StatsdDataFactory,
    Liuggio\StatsdClient\Sender\SocketSender;

/**
 * Class JanPapenbrock_Statsd_Model_Tracker
 */
class JanPapenbrock_Statsd_Model_Tracker extends Mage_Core_Model_Abstract
{
    /** @var  JanPapenbrock_Statsd_Model_Configuration */
    protected $_configuration;

    /** @var SocketSender $_client  */
    protected $_sender;

    /** @var StatsdClient $_client  */
    protected $_client;

    /** @var StatsdDataFactory $_factory */
    protected $_factory;

    /** @var array $_dataItems */
    protected $_dataItems = array();

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
        if (!$this->isActive()) {
            return;
        }
        if (count($this->_dataItems)) {
            $this->getClient()->send($this->_dataItems);
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
        if (!$this->isActive()) {
            return $this;
        }
        $this->_dataItems[] = $this->getFactory()->timing($this->getPrefixedKey($key), $time);
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
        if (!$this->isActive()) {
            return $this;
        }
        $this->_dataItems[] = $this->getFactory()->gauge($this->getPrefixedKey($key), $value);
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
        if (!$this->isActive()) {
            return $this;
        }

        $this->_dataItems[] = $this->getFactory()->set($this->getPrefixedKey($key), $value);

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
        if (!$this->isActive()) {
            return $this;
        }

        $this->_dataItems[] = $this->getFactory()->increment($this->getPrefixedKey($key));

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
        if (!$this->isActive()) {
            return $this;
        }

        $key = $this->getPrefixedKey($key);
        $this->_dataItems[] = $this->getFactory()->decrement($key);

        return $this;
    }

    /**
     * Get Statsd factory instance.
     *
     * @return StatsdDataFactory
     */
    protected function getFactory()
    {
        if (is_null($this->_factory)) {
            $this->_factory = new StatsdDataFactory('\Liuggio\StatsdClient\Entity\StatsdData');
        }
        return $this->_factory;
    }

    /**
     * Get statsd client instance.
     *
     * @return StatsdClient
     */
    protected function getClient()
    {
        if (is_null($this->_client)) {
            $this->_client  = new StatsdClient($this->getSender());
        }
        return $this->_client;
    }

    /**
     * Get socket sender instance.
     *
     * @return SocketSender
     */
    protected function getSender()
    {
        if (is_null($this->_sender)) {
            $this->_sender  = new SocketSender(
                $this->getConfiguration()->getHost(),
                $this->getConfiguration()->getPort(),
                $this->getConfiguration()->getProtocol()
            );
        }
        return $this->_sender;
    }

    /**
     * Prepare a given key, i.e. prefix it with configured prefix.
     *
     * @param string $key Key.
     *
     * @return string
     */
    protected function getPrefixedKey($key)
    {
        $result = $this->getFullPrefix().$key;
        return $result;
    }

    /**
     * Get stats key prefix.
     *
     * @return string
     */
    protected function getFullPrefix()
    {
        $result = '';
        $prefix = $this->getConfiguration()->getPrefix();
        if ($prefix) {
            $result = $prefix . '.';
        }

        return $result;
    }

    /**
     * Is tracking enabled?
     *
     * @return bool
     */
    protected function isActive()
    {
        $result = $this->getConfiguration()->getActive();
        return $result;
    }

    /**
     * Get configuration.
     *
     * @return JanPapenbrock_Statsd_Model_Configuration
     */
    protected function getConfiguration()
    {
        if (is_null($this->_configuration)) {
            $this->_configuration = Mage::getModel('statsd/configuration');
        }
        return $this->_configuration;
    }

}
