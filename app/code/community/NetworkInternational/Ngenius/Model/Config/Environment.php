<?php
/**
 * Ngenius Environment Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

class NetworkInternational_Ngenius_Model_Config_Environment
{
    
    /**
    * Return Environment Values.
    *
    * @return array
    */
    public function toOptionArray()
    {
        return array(
            array('value' => 'uat', 'label' => Mage::helper('adminhtml')->__('UAT')),
            array('value' => 'live', 'label' => Mage::helper('adminhtml')->__('Live'))
        );
    }
}
