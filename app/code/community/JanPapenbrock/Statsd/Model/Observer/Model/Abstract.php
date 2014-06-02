<?php

class JanPapenbrock_Statsd_Model_Observer_Model_Abstract extends JanPapenbrock_Statsd_Model_Observer_Abstract
{

    protected $_eventName = "abstracted";

    public function before($observer)
    {
        $this->_startTrackDuration($observer->getObject());
    }

    public function after($observer)
    {
        $object = $observer->getObject();
        $duration = $this->_stopTrackDuration($object);

        $class = strtolower(get_class($object));

        $tracker = $this->getTracker();
        $tracker->timing('models.model.' . $this->_eventName, $duration);
        $tracker->timing('models.' . $class . '.' . $this->_eventName, $duration);
        $tracker->send();
    }

}
