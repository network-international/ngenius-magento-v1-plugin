<?php

/**
 * Ngenius Config Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Config
{

    /**
     * Config tags
     */
    const CODE = 'ngeniusonline';
    const CONFIG_BASE = 'payment/ngeniusonline/';
    const ENVIRONMENT = 'environment';
    const ACTIVE = 'active';
    const OUTLET_REF = 'outlet_ref';
    const API_KEY = 'api_key';
    const UAT_IDENTITY_URL = 'uat_identity_url';
    const LIVE_IDENTITY_URL = 'live_identity_url';
    const UAT_API_URL = 'uat_api_url';
    const LIVE_API_URL = 'live_api_url';
    const TOKEN_ENDPOINT = 'token_endpoint';
    const ORDER_ENDPOINT = 'order_endpoint';
    const FETCH_ENDPOINT = 'fetch_endpoint';
    const CAPTURE_ENDPOINT = 'capture_endpoint';
    const REFUND_ENDPOINT = 'refund_endpoint';
    const VOID_ENDPOINT = 'void_auth_endpoint';
    const DEBUG = 'debug';
    const TENANT = 'tenant';

    /**
     * Gets value of configured environment.
     * Possible values: yes or no.
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::ACTIVE);
    }

    /**
     * Retrieve apikey and outletReferenceId empty or not
     *
     * @return bool
     */
    public function isComplete()
    {
        if (!empty($this->getApiKey()) && !empty($this->getOutletReferenceId())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets Identity Url.
     *
     * @return string
     */
    public function getIdentityUrl()
    {
        switch ($this->getEnvironment()) {
            case 'uat':
                $value = self::UAT_IDENTITY_URL;
                break;
            case 'live':
                $value = self::LIVE_IDENTITY_URL;
                break;
        }
        return $this->getValue($value);
    }

    /**
     * Gets Payment Action.
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->getValue('payment_action');
    }

    /**
     * Gets Environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->getValue(self::ENVIRONMENT);
    }

    /**
     * Gets Api Url.
     *
     * @return string
     */
    public function getApiUrl()
    {
        switch ($this->getEnvironment()) {
            case 'uat':
                $value = self::UAT_API_URL;
                break;
            case 'live':
                $value = self::LIVE_API_URL;
                break;
        }
        return $this->getValue($value);
    }

    /**
     * Gets Outlet Reference Id.
     *
     * @return string
     */
    public function getOutletReferenceId()
    {
        return $this->getValue(self::OUTLET_REF);
    }

    /**
     * Gets Api Key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getValue(self::API_KEY);
    }

    /**
     * Gets TokenRequest URL.
     *
     * @return string
     */
    public function getTokenRequestURL()
    {
        $tenant = $this->getValue(self::TENANT);
        $tenantArr = [
                'networkinternational' => [
                        'uat'  => 'ni',
                        'live' => 'networkinternational',
                ],
        ];
        if ( isset( $tenantArr[ $tenant ][ $this->getEnvironment() ] ) ) {
                $tenant = $tenantArr[ $tenant ][ $this->getEnvironment() ];
        }
        return $this->getIdentityUrl() .sprintf(  $this->getValue(self::TOKEN_ENDPOINT), $tenant );
    }

    /**
     * Gets Order Request URL.
     *
     * @return string
     */
    public function getOrderRequestURL()
    {
        $endpoint = sprintf($this->getValue(self::ORDER_ENDPOINT), $this->getOutletReferenceId());
        return $this->getApiUrl() . $endpoint;
    }

    /**
     * Gets Fetch Request URL.
     *
     * @return string
     */
    public function getFetchRequestURL($orderRef)
    {
        $endpoint = sprintf($this->getValue(self::FETCH_ENDPOINT), $this->getOutletReferenceId(), $orderRef);
        return $this->getApiUrl() . $endpoint;
    }

    /**
     * Gets Debug On.
     *
     * @return bool
     */
    public function isDebugOn()
    {
        return (bool) $this->getValue(self::DEBUG);
    }

    /**
     * Gets Order Capture URL.
     *
     * @param string $orderRef
     * @param string $paymentRef
     * @return string
     */
    public function getOrderCaptureURL($orderRef, $paymentRef)
    {
        $endpoint = sprintf($this->getValue(self::CAPTURE_ENDPOINT), $this->getOutletReferenceId(), $orderRef, $paymentRef);
        return $this->getApiUrl() . $endpoint;
    }

    /**
     * Gets Order Refund URL.
     *
     * @param string $orderRef
     * @param string $paymentRef
     * @param string $transactionId
     * @return string
     */
    public function getOrderRefundURL($orderRef, $paymentRef, $transactionId)
    {
        $endpoint = sprintf($this->getValue(self::REFUND_ENDPOINT), $this->getOutletReferenceId(), $orderRef, $paymentRef, $transactionId);
        return $this->getApiUrl() . $endpoint;
    }

    /**
     * Gets Order Void URL.
     *
     * @param string $orderRef
     * @param string $paymentRef
     * @return string
     */
    public function getOrderVoidURL($orderRef, $paymentRef)
    {
        $endpoint = sprintf($this->getValue(self::VOID_ENDPOINT), $this->getOutletReferenceId(), $orderRef, $paymentRef);
        return $this->getApiUrl() . $endpoint;
    }

    /**
     * Gets values from StoreConfig
     *
     * @param string $key
     * @return string
     */
    public function getValue($key)
    {
        $store = Mage::app()->getStore();
        return Mage::getStoreConfig(self::CONFIG_BASE . $key, $store);
    }
}
