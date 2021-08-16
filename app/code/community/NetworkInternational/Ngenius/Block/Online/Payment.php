<?php

/**
 * Ngenius Payment Block
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Block_Online_Payment extends Mage_Payment_Block_Info
{

    /**
     * Initialize Payment Block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ngenius/online/payment.phtml');
    }

    /**
     * Gets Method Code.
     *
     * @return string
     */
    public function getMethodCode()
    {
        return NetworkInternational_Ngenius_Model_Config::CODE;
    }
}
