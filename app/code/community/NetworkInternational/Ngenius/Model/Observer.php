<?php
/**
 * Ngenius Observer Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Observer
{
    /**
     * Generate payment url when reorder
     * 
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function execute(Varien_Event_Observer $observer)
    {

        $redirectionUrl = Mage::getSingleton('checkout/session')->getPaymentUrl();
        if ($redirectionUrl) {
            $message = 'Go to <a href="' . $redirectionUrl . '" target="_blank">payment page</a> to do the transaction';
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        }
    }
}
