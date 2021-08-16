<?php

/**
 * Ngenius Capturerequest Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Request_Capturerequest
{

    /**
     * Builds ENV Capture request
     *
     * @param array $order
     * @param float $amount
     * @return array|null
     * @throws Mage::throwException
     */
    public function build($order, $amount)
    {
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
                    'uri' => $config->getOrderCaptureURL($orderItem->getReference(), $orderItem->getPaymentId())
                ]
            ];
        } else {
            Mage::throwException('Error! Invalid configuration.');
        }
    }
}
