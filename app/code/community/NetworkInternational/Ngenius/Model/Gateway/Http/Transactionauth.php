<?php
/**
 * Ngenius Transactionauth Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

class NetworkInternational_Ngenius_Model_Gateway_Http_Transactionauth extends NetworkInternational_Ngenius_Model_Gateway_Http_Abstracttransaction
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
        $response = json_decode($responseEnc);
        if (isset($response->_links->payment->href)) {
            $model = Mage::getModel('ngenius/standard');
            $data = Mage::getSingleton('checkout/session')->getTableData();
            $data['reference'] = isset($response->reference) ? $response->reference : '';
            $data['action'] = isset($response->action) ? $response->action : '';
            $data['state'] = isset($response->_embedded->payment[0]->state) ? $response->_embedded->payment[0]->state : '';
            $data['status'] = $model::STATUS[0]['status'];
            $model->addData($data);
            $model->save();

            return ['payment_url' => $response->_links->payment->href];
        } else {
            return null;
        }
    }
}
