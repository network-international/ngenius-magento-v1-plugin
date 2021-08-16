<?php

/**
 * Ngenius Refundvalidator Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Validator_Refundvalidator
{

    /**
     * Performs refund validation for transaction
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
            Mage::throwException('Error! Invalid refund transaction');
        } else {
            $paymentData = ['Refunded Amount' => Mage::helper('core')->formatPrice($response['result']['refunded_amt'], false)];
            $payment->setTransactionId($response['result']['payment_id']);
            $transaction = $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, null, true);
            $transaction->setOrder($order);
            $transaction->setTxnId($response['result']['payment_id']);
            $transaction->setIsClosed(true);
            $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $paymentData);
            $transaction->save();
            $payment->setSkipTransactionCreation(true);
            $payment->save();
            $order->addStatusToHistory($response['result']['order_status'], 'The refund has been processed successfully.', false);
            $order->save();
            return true;
        }
    }
}
