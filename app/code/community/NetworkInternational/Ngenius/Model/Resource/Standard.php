<?php


/**
 * Ngenius Standard Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Resource_Standard extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Standard Model constructor
     */
    protected function _construct()
    {
        $this->_init('ngenius/standard', 'nid');
    }
}
