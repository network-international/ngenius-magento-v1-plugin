<?php
/**
 * Ngenius Abstracttransaction Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

abstract class NetworkInternational_Ngenius_Model_Gateway_Http_Abstracttransaction
{

    /**
     * Ngenius Order status.
     */
    protected $_orderStatus = NetworkInternational_Ngenius_Model_Standard::STATUS;

    /**
     * Places request to gateway. Returns result as ENV array.
     *
     * @param TransferInterface $transferObject
     * @return array|null
     * @throws ClientException
     * @throws Zend_Http_Client_Exception
     * @throws Mage::throwException
     */
    public function placeRequest(NetworkInternational_Ngenius_Model_Gateway_Http_Transferfactory $transferObject)
    {

        $data = $this->preProcess($transferObject->getBody());
        $log = array(
            'request' => $data,
            'request_uri' => $transferObject->getUri()
        );

        $result = array();
        $client = new \Zend_Http_Client;
        $client->setMethod($transferObject->getMethod());
        $client->setRawData($data);
        $client->setHeaders($transferObject->getHeaders());
        $client->setUri($transferObject->getUri());

        try {
            $response = $client->request();
            if ($response->isSuccessful()) {
                $result = $response->getRawBody();
                $log['response'] = $result;
                return $this->postProcess($result);
            } else {
                $log['response'] = $response->getRawBody();
                $errCode = $response->getStatus();
                if ((int)$errCode == 409) {
                    $error = 'Failed! Please do the transaction after payment settlement.';
                } else {
                    $error = 'Failed! #' . $errCode . ': ' . $response->getMessage();
                }
                Mage::throwException($error);
            }
        } catch (\Zend_Http_Client_Exception $e) {
            Mage::throwException($e->getMessage());
        } finally {
            if (Mage::getModel("ngenius/config")->isDebugOn()) {
                Mage::log($log, null, 'payment.log', true);
            }
        }
    }

    /**
     * Processing of API request body
     *
     * @param array $data
     * @return string|array
     */
    abstract protected function preProcess(array $data);

    /**
     * Processing of API response
     *
     * @param array $response
     * @return array|null
     */
    abstract protected function postProcess($response);
}
