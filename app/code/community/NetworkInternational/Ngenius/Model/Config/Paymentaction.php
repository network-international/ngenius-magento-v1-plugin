<?php
/**
 * Ngenius Paymentaction Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Config_Paymentaction
{

    /**
     * Return payment action.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => 'Authorize', 'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE),
            array('label' => 'Sale', 'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE)
        );
    }
}
