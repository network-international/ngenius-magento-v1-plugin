<?php
/**
 * Ngenius Orderstatus Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Config_Orderstatus
{

    /**
     * Return status of new order.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'ngenius_pending', 'label' => Mage::helper('adminhtml')->__('n-genius Pending'))
        );
    }
}
