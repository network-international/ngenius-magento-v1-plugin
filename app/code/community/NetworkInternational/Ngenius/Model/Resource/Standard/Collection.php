<?php

/**
 * Ngenius Collection Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Resource_Standard_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    
    /**
     * Collection Model constructor
     */
    protected function _construct()
    {
        $this->_init('ngenius/standard');
    }
}
