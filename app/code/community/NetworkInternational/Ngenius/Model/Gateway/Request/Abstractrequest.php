<?php
/**
 * Ngenius Abstractrequest Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

abstract class NetworkInternational_Ngenius_Model_Gateway_Request_Abstractrequest
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * Abstractrequest constructor
     * Initialize ngenius config model
     */
    public function __construct()
    {
        $this->config = Mage::getModel("ngenius/config");
    }

    /**
     * Builds ENV request
     *
     * @param array $order
     * @param float $amount
     * @return array
     * @throws Mage::throwException
     */
    public function build($order, $amount)
    {
        $tokenRequest = Mage::getModel("ngenius/gateway_request_tokenrequest");

        if ($this->config->isComplete()) {
            $this->setTableData($order, $amount);

            return[
                'token' => $tokenRequest->getAccessToken(),
                'request' => $this->getBuildArray($order, $amount)
            ];
        } else {
            Mage::throwException('Error! Invalid configuration.');
        }
    }

    /**
     * Set table data for checkout session
     *
     * @param array $order
     * @param float $amount
     * @return array
     */
    public function setTableData($order, $amount)
    {
        $data = [
            'order_id' => $order->getIncrementId(),
            'currency' => $order->getOrderCurrencyCode(),
            'amount' => $amount
        ];
        Mage::getSingleton('checkout/session')->setTableData($data);
    }

    /**
     * Builds abstract ENV request array
     *
     * @param array $order
     * @param float $amount
     * @return array
     */
    abstract public function getBuildArray($order, $amount);
}
