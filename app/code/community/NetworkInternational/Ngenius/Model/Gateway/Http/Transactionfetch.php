<?php
/**
 * Ngenius Transactionfetch Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

class NetworkInternational_Ngenius_Model_Gateway_Http_Transactionfetch extends NetworkInternational_Ngenius_Model_Gateway_Http_Abstracttransaction
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
     * @return array
     */
    protected function postProcess($responseEnc)
    {
        return json_decode($responseEnc, true);
    }
}
