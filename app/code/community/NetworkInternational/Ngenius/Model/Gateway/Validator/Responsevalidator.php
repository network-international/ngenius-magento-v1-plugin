<?php

/**
 * Ngenius Responsevalidator Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Validator_Responsevalidator
{

    /**
     * Performs response validation for transaction
     *
     * @param array $response
     * @return bool
     * @throws Mage::throwException
     */
    public function validate($response)
    {

        if (isset($response['payment_url']) && filter_var($response['payment_url'], FILTER_VALIDATE_URL)) {
            $session = Mage::getSingleton('checkout/session');
            $session->unsPaymentUrl();
            $session->setPaymentUrl($response['payment_url']);
            return true;
        } else {
            Mage::throwException(Mage::helper('checkout')->__('Error! Invalid payment gateway URL'));
            return false;
        }
    }
}
