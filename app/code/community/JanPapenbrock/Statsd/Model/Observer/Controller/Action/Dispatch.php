<?php

class JanPapenbrock_Statsd_Model_Observer_Controller_Action_Dispatch
    extends JanPapenbrock_Statsd_Model_Observer_Abstract
{

    protected $_eventName = "dispatched";

    public function predispatch($observer)
    {
        $this->_startTrackDuration($observer->getControllerAction());
    }

    public function postdispatch($observer)
    {
        $controllerAction = $observer->getControllerAction();
        $duration = $this->_stopTrackDuration($controllerAction);

        $class = $controllerAction->getFullActionName('_');

        $tracker = $this->getTracker();
        $tracker->timing('controllers.' . $class . '.' . $this->_eventName, $duration);
        $tracker->send();
    }

}
