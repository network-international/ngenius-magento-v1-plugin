<?php

/**
 * Ngenius Capturevalidator Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Validator_Capturevalidator
{

    /**
     * Performs validation for capture transaction
     *
     * @param object $payment
     * @param array $response
     * @return bool|null
     * @throws Mage::throwException
     */
    public function validate($payment, $response)
    {

        $order = $payment->getOrder();

        if (!isset($response['result']) && !is_array($response['result'])) {
            Mage::throwException('Error! Invalid capture transaction');
        } else {
            $paymentData = ['Captured Amount' => Mage::helper('core')->formatPrice($response['result']['captured_amt'], false)];
            $payment->setTransactionId($response['result']['payment_id']);
            $transaction = $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE, null, true);
            $transaction->setOrder($order);
            $transaction->setTxnId($response['result']['payment_id']);
            $transaction->setIsClosed(true);
            $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $paymentData);
            $transaction->save();
            $payment->setSkipTransactionCreation(true);
            $payment->save();
            $order->addStatusToHistory($response['result']['order_status'], 'The capture has been processed successfully.', false);
            $order->save();
            return true;
        }
    }
}
