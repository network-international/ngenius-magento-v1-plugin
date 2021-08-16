<?php

/**
 * Ngenius Voidrequest Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Request_Voidrequest
{

    /**
     * Builds ENV void request
     *
     * @param object $order
     * @return array|null
     * @throws Mage::throwException
     */
    public function build($order)
    {

        $tokenRequest = Mage::getModel("ngenius/gateway_request_tokenrequest");
        $config = Mage::getModel("ngenius/config");
        $collection = Mage::getModel('ngenius/standard')->getCollection()->addFieldToFilter('order_id', $order->getIncrementId());
        $orderItem = $collection->getFirstItem();
        if ($config->isComplete()) {
            return[
                'token' => $tokenRequest->getAccessToken(),
                'request' => [
                    'data' => [],
                    'method' => \Zend_Http_Client::PUT,
                    'uri' => $config->getOrderVoidURL($orderItem->getReference(), $orderItem->getPaymentId())
                ]
            ];
        } else {
            Mage::throwException('Error! Invalid configuration.');
        }
    }
}
