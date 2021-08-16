<?php

/**
 * Ngenius Salerequest Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Request_Salerequest extends NetworkInternational_Ngenius_Model_Gateway_Request_Abstractrequest
{

    /**
     * Builds ENV sale request array
     *
     * @param array $order
     * @param float $amount
     * @return array
     */
    public function getBuildArray($order, $amount)
    {
        return[
            'data' => [
                'action' => 'SALE',
                'amount' => [
                    'currencyCode' => $order->getOrderCurrencyCode(),
                    'value' => $amount * 100
                ],
                'merchantAttributes' => [
                    "redirectUrl" => Mage::getUrl('ngenius/payment/proceed'),
                    'skipConfirmationPage' => true,
                ],
                'merchantOrderReference' => $order->getIncrementId(),
                'emailAddress'           => $order->getBillingAddress()->getEmail(),
                'billingAddress' => [
                    'firstName' => $order->getBillingAddress()->getFirstname(),
                    'lastName' => $order->getBillingAddress()->getLastname(),
                ]
            ],
            'method' => \Zend_Http_Client::POST,
            'uri' => $this->config->getOrderRequestURL()
        ];
    }
}
