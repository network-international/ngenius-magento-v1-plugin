<?php

/**
 * Ngenius Transactionvoid Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

class NetworkInternational_Ngenius_Model_Gateway_Http_Transactionvoid extends NetworkInternational_Ngenius_Model_Gateway_Http_Abstracttransaction
{

    /**
     * Processing of API request body
     *
     * @param array $data
     * @return string
     */
    protected function preProcess(array $data)
    {
        return json_encode($data);
    }

    /**
     * Processing of API response
     *
     * @param array $response
     * @return array|null
     */
    protected function postProcess($responseEnc)
    {

        $response = json_decode($responseEnc, true);
        if (isset($response['errors']) && is_array($response['errors'])) {
            return null;
        } else {
            $collection = Mage::getModel('ngenius/standard')->getCollection()->addFieldToFilter('reference', $response['orderReference']);
            $orderItem = $collection->getFirstItem();

            $state = isset($response['state']) ? $response['state'] : '';
            $order_status = ($state == 'REVERSED') ? $this->_orderStatus[9]['status'] : '';

            $orderItem->setState($state);
            $orderItem->setStatus($order_status);
            $orderItem->save();
            return [
                'result' => [
                    'state' => $state,
                    'order_status' => $order_status
                ]
            ];
        }
    }
}
