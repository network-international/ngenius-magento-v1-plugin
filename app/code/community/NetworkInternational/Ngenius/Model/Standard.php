<?php

/**
 * Ngenius Standard Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Standard extends Mage_Core_Model_Abstract
{

    /**
     * Ngenius state.
     */
    const STATE = 'ngenius_state';

    /**
     * Array of status.
     */
    const STATUS = array(
        array('status' => 'ngenius_pending', 'label' => 'n-genius Pending'),
        array('status' => 'ngenius_processing', 'label' => 'n-genius Processing'),
        array('status' => 'ngenius_failed', 'label' => 'n-genius Failed'),
        array('status' => 'ngenius_complete', 'label' => 'n-genius Complete'),
        array('status' => 'ngenius_authorised', 'label' => 'n-genius Authorised'),
        array('status' => 'ngenius_fully_captured', 'label' => 'n-genius Fully Captured'),
        array('status' => 'ngenius_partially_captured', 'label' => 'n-genius Partially Captured'),
        array('status' => 'ngenius_fully_refunded', 'label' => 'n-genius Fully Refunded'),
        array('status' => 'ngenius_partially_refunded', 'label' => 'n-genius Partially Refunded'),
        array('status' => 'ngenius_auth_reversed', 'label' => 'n-genius Auth Reversed')
    );

    /**
     * Initialize standard model
     */
    protected function _construct()
    {
        $this->_init('ngenius/standard');
    }
}
