<?php

/**
 * Ngenius Tokenrequest Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Gateway_Request_Tokenrequest
{

    /**
     * Builds access token request
     *
     * @param array $order
     * @param float $amount
     * @return string|null
     * @throws Mage::throwException
     * @throws \Zend_Http_Client_Exception
     */
    public function getAccessToken()
    {

        $result = array();
        $config = Mage::getModel("ngenius/config");
        $client = new \Zend_Http_Client;
        $client->setMethod($client::POST);
        $client->setRawData(http_build_query(array('grant_type' => 'client_credentials')));
        $client->setHeaders(
            array(
            'Authorization' => 'Basic ' . $config->getApiKey(),
            'Content-Type' => $client::ENC_URLENCODED
            )
        );
        $client->setUri($config->getTokenRequestURL());

        try {
            $response = $client->request();
            $result = json_decode($response->getBody());
            $log['response'] = $result;
            if (isset($result->access_token)) {
                return $result->access_token;
            } else {
                Mage::throwException('Error! Invalid Token.');
            }
        } catch (\Zend_Http_Client_Exception $e) {
            Mage::throwException($e->getMessage());
        } finally {
            //Mage::log($log);
        }
    }
}
