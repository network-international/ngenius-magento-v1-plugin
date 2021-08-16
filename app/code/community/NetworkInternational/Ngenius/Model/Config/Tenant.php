<?php
/**
 * Ngenius Tenant Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

class NetworkInternational_Ngenius_Model_Config_Tenant
{
    
    /**
    * Return tenant Values.
    *
    * @return array
    */
    public function toOptionArray()
    {
        return array(
            array('value' => 'networkinternational', 'label' => Mage::helper('adminhtml')->__('Network International'))
        );
    }
}
