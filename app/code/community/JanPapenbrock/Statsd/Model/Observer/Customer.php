<?php

class JanPapenbrock_Statsd_Model_Observer_Customer extends JanPapenbrock_Statsd_Model_Observer_Abstract
{

    /**
     * Count successful customer registrations.
     *
     * @return void
     */
    public function registerSuccess()
    {
        $this->getTracker()->increment("customer.registered");
    }

    /**
     * Count successful customer logins.
     *
     * @return void
     */
    public function login()
    {
        $this->getTracker()->increment("customer.logged_in");
    }

    /**
     * Count successful customer logouts.
     *
     * @return void
     */
    public function logout()
    {
        $this->getTracker()->increment("customer.logged_out");
    }

    /**
     * Count successful customer authentications.
     *
     * @return void
     */
    public function customerAuthenticated()
    {
        $this->getTracker()->increment("customer.authenticated");
    }

}
