<?php

class JanPapenbrock_Statsd_Model_Observer_Abstract
{

    protected $_timings = array();

    /** @var JanPapenbrock_Statsd_Model_Tracker $_tracker */
    protected $_tracker;

    /**
     * Get tracker instance.
     *
     * @return JanPapenbrock_Statsd_Model_Tracker
     */
    public function getTracker()
    {
        if (!$this->_tracker) {
            $this->_tracker = Mage::getSingleton("statsd/tracker");
        }

        return $this->_tracker;
    }

    /**
     * Retrieve the object hash for the given model.
     *
     * @param Object $object Object to hash
     *
     * @return string Hashed object
     */
    protected function _getObjectHash($object)
    {
        return spl_object_hash($object);
    }

    /**
     * Obtain time tracking key for given object.
     *
     * @param string|Object $object Object.
     *
     * @return string
     */
    protected function getTimeTrackingKey($object)
    {
        if (is_string($object)) {
            $result = $object;
        } else {
            $result = $this->_getObjectHash($object);
        }

        return $result;
    }

    /**
     * Start a time tracker for the given object.
     *
     * @param string|Object $object An object.
     *
     * @return void
     */
    protected function _startTrackDuration($object)
    {
        $key = $this->getTimeTrackingKey($object);
        $this->_timings[$key] = microtime(true);
    }

    /**
     * Stop a started time tracker for the given object.
     *
     * @param Object $object An object.
     *
     * @return int Duration in milliseconds.
     */
    protected function _stopTrackDuration($object)
    {
        $key = $this->getTimeTrackingKey($object);

        if (isset($this->_timings[$key])) {
            $duration = microtime(true) - $this->_timings[$key];
            $duration = round($duration * 1000);
        } else {
            $duration = 0;
        }

        return $duration;
    }

}
