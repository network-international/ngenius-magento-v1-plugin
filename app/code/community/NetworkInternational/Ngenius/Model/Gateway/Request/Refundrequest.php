<?php

/**
 * Ngenius Refundrequest Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Request_Refundrequest
{

    /**
     * Builds ENV refund request
     *
     * @param object $payment
     * @param float $amount
     * @return array|null
     * @throws Mage::throwException
     */
    public function build($payment, $amount)
    {
        $transactionId = $payment->getRefundTransactionId();
        $txnId = str_replace('-' . Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE, '', $transactionId);
        
        if (!$txnId) {
            Mage::throwException('Error! No capture transaction to proceed refund.');
        }
        
        $order = $payment->getOrder();
        $tokenRequest = Mage::getModel("ngenius/gateway_request_tokenrequest");
        $config = Mage::getModel("ngenius/config");
        $collection = Mage::getModel('ngenius/standard')->getCollection()->addFieldToFilter('order_id', $order->getIncrementId());
        $orderItem = $collection->getFirstItem();
        if ($config->isComplete()) {
            return[
                'token' => $tokenRequest->getAccessToken(),
                'request' => [
                    'data' => [
                        'amount' => [
                            'currencyCode' => $orderItem->getCurrency(),
                            'value' => $amount * 100
                        ]
                    ],
                    'method' => \Zend_Http_Client::POST,
                    'uri' => $config->getOrderRefundURL($orderItem->getReference(), $orderItem->getPaymentId(), $txnId)
                ]
            ];
        } else {
            Mage::throwException('Error! Invalid configuration.');
        }
    }
}
