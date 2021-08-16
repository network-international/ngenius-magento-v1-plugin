<?php

/**
 * Ngenius Voidvalidator Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Validator_Voidvalidator
{

    /**
     * Performs reversed the authorization
     *
     * @param object $payment
     * @param array $response
     * @return bool
     * @throws Mage::throwException
     */
    public function validate($payment, $response)
    {

        if (isset($response['result']['order_status'])) {
            $order = $payment->getOrder();
            $order->addStatusToHistory($response['result']['order_status'], 'The authorization has been reversed successfully.', false);
            $order->save();
            return true;
        } else {
            Mage::throwException('Error! Invalid transaction');
            return false;
        }
    }
}
