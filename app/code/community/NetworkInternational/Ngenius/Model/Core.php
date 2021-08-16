<?php

/**
 * Ngenius Core Model
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */
class NetworkInternational_Ngenius_Model_Core extends Mage_Payment_Model_Method_Abstract
{

    /**
     * n-genius states
     */
    const NGENIUS_STARTED = 'STARTED';
    const NGENIUS_AUTHORISED = 'AUTHORISED';
    const NGENIUS_CAPTURED = 'CAPTURED';
    const NGENIUS_FAILED = 'FAILED';

    /**
     * Payment gatway code
     * @var _code
     */
    protected $_code = NetworkInternational_Ngenius_Model_Config::CODE;
    
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    protected $_canFetchTransactionInfo = false;
    protected $_formBlockType = 'ngenius/online_payment';
    protected $_ngeniusState;
    protected $_error;

    /**
     * Payment gatway order status
     * @var orderStatus
     */
    protected $_orderStatus = NetworkInternational_Ngenius_Model_Standard::STATUS;

    /**
     * core Model constructor
    */
    protected function _construct()
    {
        $this->_init('ngenius/core');
    }

    /**
     * Gets Order Place Redirect Url.
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getSingleton('checkout/session')->getPaymentUrl();
    }

    /**
     * Validate apikey and outletReferenceId.
     *
     * @return $this
     * @throws Mage::throwException
     */
    public function validate()
    {
        if (!Mage::getModel("ngenius/config")->isComplete()) {
            Mage::throwException('Error! Invalid configuration.');
        }
        return $this;
    }

    /**
     * Order Authorize.
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $order = $payment->getOrder();
        $payment->setIsTransactionPending(true);
        $requestData = Mage::getModel("ngenius/gateway_request_authorizationrequest")->build($order, $amount);
        $transferObject = Mage::getModel("ngenius/gateway_http_transferfactory")->create($requestData);
        $response = Mage::getModel("ngenius/gateway_http_transactionauth")->placeRequest($transferObject);
        Mage::getModel("ngenius/gateway_validator_responsevalidator")->validate($response);
        return $this;
    }

    /**
     * Order Capture.
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|null
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $order = $payment->getOrder();
        if ($payment->getAuthorizationTransaction()) {
            $requestData = Mage::getModel("ngenius/gateway_request_capturerequest")->build($order, $amount);
            $transferObject = Mage::getModel("ngenius/gateway_http_transferfactory")->create($requestData);
            $response = Mage::getModel("ngenius/gateway_http_transactioncapture")->placeRequest($transferObject);
            Mage::getModel("ngenius/gateway_validator_capturevalidator")->validate($payment, $response);
        } else {
            $payment->setIsTransactionPending(true);
            $requestData = Mage::getModel("ngenius/gateway_request_salerequest")->build($order, $amount);
            $transferObject = Mage::getModel("ngenius/gateway_http_transferfactory")->create($requestData);
            $response = Mage::getModel("ngenius/gateway_http_transactionsale")->placeRequest($transferObject);
            Mage::getModel("ngenius/gateway_validator_responsevalidator")->validate($response);
        }
        return $this;
    }

    /**
     * Order Void.
     *
     * @param Varien_Object $payment
     * @return $this|null
     * @throws Mage::throwException
     */
    public function void(Varien_Object $payment)
    {
        if (!$payment->getTransactionId()) {
            Mage::throwException('No authorization transaction to proceed.');
        }
        $order = $payment->getOrder();
        $requestData = Mage::getModel("ngenius/gateway_request_voidrequest")->build($order);
        $transferObject = Mage::getModel("ngenius/gateway_http_transferfactory")->create($requestData);
        $response = Mage::getModel("ngenius/gateway_http_transactionvoid")->placeRequest($transferObject);
        Mage::getModel("ngenius/gateway_validator_voidvalidator")->validate($payment, $response);
        return $this;
    }

    /**
     * Order Refund.
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $requestData = Mage::getModel("ngenius/gateway_request_refundrequest")->build($payment, $amount);
        $transferObject = Mage::getModel("ngenius/gateway_http_transferfactory")->create($requestData);
        $response = Mage::getModel("ngenius/gateway_http_transactionrefund")->placeRequest($transferObject);
        Mage::getModel("ngenius/gateway_validator_refundvalidator")->validate($payment, $response);
        return $this;
    }

    /**
     * Order Cancel.
     *
     * @param Varien_Object $payment
     * @return $this
     */
    public function cancel(Varien_Object $payment)
    {
        $this->void($payment);
        return $this;
    }

    /**
     * Gets Config Payment Action.
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return Mage::getModel("ngenius/config")->getPaymentAction();
    }

    /**
     * Update Order
     *
     * @return null
     */
    public function updateOrder()
    {
        $orderItems = $this->fetchOrder('state', self::NGENIUS_STARTED)->addFieldToFilter('payment_id', null)->addFieldToFilter('created_at', ['lteq' => date('Y-m-d H:i:s', strtotime('-1 hour'))])->setOrder('nid', 'DESC');
        if ($orderItems) {
            foreach ($orderItems as $orderItem) {
                $orderRef = $orderItem->getReference();
                $result = $this->getResponseAPI($orderRef);
                if ($result && isset($result['_embedded']['payment']) && is_array($result['_embedded']['payment'])) {
                    $action = isset($result['action']) ? $result['action'] : '';
                    $paymentResult = $result['_embedded']['payment'][0];
                    $this->processOrder($paymentResult, $orderItem, $orderRef, $action);
                }
            }
        }
    }
    
    /**
     * Process Order.
     *
     * @param array $paymentResult
     * @param object $orderItem
     * @param string $orderRef
     * @param string $action
     * @return $this|null
     */
    public function processOrder($paymentResult, $orderItem, $orderRef, $action)
    {
        $dataTable = [];
        $incrementId = $orderItem->getOrderId();

        if ($incrementId) {
            $paymentId = '';
            $capturedAmt = 0;
            if (isset($paymentResult['_id'])) {
                $paymentIdArr = explode(':', $paymentResult['_id']);
                $paymentId = end($paymentIdArr);
            }

            $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
            if ($order->getId()) {
                if ($this->_ngeniusState != self::NGENIUS_FAILED) {
                    if ($this->_ngeniusState != self::NGENIUS_STARTED) {
                        $order->setState(NetworkInternational_Ngenius_Model_Standard::STATE, $this->_orderStatus[1]['status']);
                        $order->save();
                        switch ($action) {
                            case "AUTH":
                                $this->orderAuthorize($order, $paymentResult, $paymentId);
                                break;
                            case "SALE":
                                $capturedAmt = $this->orderSale($order, $paymentResult, $paymentId);
                                break;
                        }
                        $dataTable['status'] = $order->getStatus();
                    } else {
                        $dataTable['status'] = $this->_orderStatus[0]['status'];
                    }
                } else {
                    $this->_error = true;
                    $this->updateInvoice($order, false);
                    $order->setStatus(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW);
                    $order->addStatusHistoryComment('The payment on order has failed.')
                            ->setIsCustomerNotified(false)->save();
                    $dataTable['status'] = $this->_orderStatus[2]['status'];
                }
                $dataTable['entity_id'] = $order->getId();
                $dataTable['payment_id'] = $paymentId;
                $dataTable['captured_amt'] = $capturedAmt;
                return $this->updateTable($dataTable, $orderItem);
            } else {
                $orderItem->setPaymentId($paymentId);
                $orderItem->setState($this->_ngeniusState);
                $orderItem->setStatus($this->_ngeniusState);
                $orderItem->save();
            }
        }
    }
    
    /**
     * Order Authorize.
     *
     * @param object $order
     * @param array $paymentResult
     * @param string $paymentId
     * @return null
     */
    public function orderAuthorize($order, $paymentResult, $paymentId)
    {

        if ($this->_ngeniusState == self::NGENIUS_AUTHORISED) {
            $payment = $order->getPayment();
            $payment->setLastTransId($paymentId);
            $payment->setTransactionId($paymentId);
            $payment->setIsClosed(false);
            $formatedPrice = Mage::helper('core')->formatPrice($order->getGrandTotal(), false);

            $paymentData = [
                'Card Type' => isset($paymentResult['paymentMethod']['name']) ? $paymentResult['paymentMethod']['name'] : '',
                'Card Number' => isset($paymentResult['paymentMethod']['pan']) ? $paymentResult['paymentMethod']['pan'] : '',
                'Amount' => $formatedPrice
            ];

            $transaction = $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH, null, true);
            $transaction->setOrder($order);
            $transaction->setTxnId($paymentId);
            $transaction->setIsClosed(false);
            $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $paymentData);
            $transaction->save();
            $payment->save();

            $message = 'The payment has been approved and the authorized amount is ' . $formatedPrice;
            $order->addStatusToHistory($this->_orderStatus[4]['status'], $message, true);
            $order->sendNewOrderEmail();
            $order->save();
        }
    }

    /**
     * Order Sale.
     *
     * @param object $order
     * @param array $paymentResult
     * @param string $paymentId
     * @return null|float
     */
    public function orderSale($order, $paymentResult, $paymentId)
    {

        if ($this->_ngeniusState == self::NGENIUS_CAPTURED) {
            $payment = $order->getPayment();
            $payment->setLastTransId($paymentId);
            $payment->setTransactionId($paymentId);
            $payment->setIsClosed(true);
            $grandTotal = $order->getGrandTotal();
            $formatedPrice = Mage::helper('core')->formatPrice($grandTotal, false);

            $paymentData = [
                'Card Type' => isset($paymentResult['paymentMethod']['name']) ? $paymentResult['paymentMethod']['name'] : '',
                'Card Number' => isset($paymentResult['paymentMethod']['pan']) ? $paymentResult['paymentMethod']['pan'] : '',
                'Amount' => $formatedPrice
            ];

            $transactionId = '';

            if (isset($paymentResult['_embedded']['cnp:capture'][0])) {
                $lastTransaction = $paymentResult['_embedded']['cnp:capture'][0];
                if (isset($lastTransaction['_links']['self']['href'])) {
                    $transactionArr = explode('/', $lastTransaction['_links']['self']['href']);
                    $transactionId = end($transactionArr);
                }elseif ($lastTransaction['_links']['cnp:refund']['href']) {
                    $transactionArr = explode('/', $lastTransaction['_links']['cnp:refund']['href']);
                    $transactionId = $transactionArr[count($transactionArr)-2];
                }
            }

            $transaction = $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE, null, true);
            $transaction->setOrder($order);
            $transaction->setTxnId($transactionId);
            $transaction->setIsClosed(true);
            $transaction->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $paymentData);
            $transaction->save();
            $payment->save();

            $message = 'The payment has been approved and the captured amount is ' . $formatedPrice;
            $order->addStatusToHistory($this->_orderStatus[3]['status'], $message, true);
            $order->sendNewOrderEmail();
            $this->updateInvoice($order, true, $transactionId);
            $order->save();
            return $grandTotal;
        }
    }

    /**
     * Update Invoice.
     *
     * @param object $order
     * @param bool $flag
     * @param string $transactionId
     * @return null
     */
    public function updateInvoice($order, $flag, $transactionId = null)
    {

        if ($order->hasInvoices()) {
            if ($flag === false) {
                foreach ($order->getInvoiceCollection() as $invoice) {
                    $invoice->cancel()->save();
                }
            } else {
                foreach ($order->getInvoiceCollection() as $invoice) {
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                    $invoice->setTransactionId($transactionId);
                    $invoice->pay();
                    $invoice->save();
                    $transactionSave = Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder());
                    $transactionSave->save();
                    try {
                        $invoice->sendEmail(true, '');
                        $invoice->save();
                        $order->addStatusHistoryComment('Notified the customer about invoice #' . $invoice->getIncrementId())
                                ->setIsCustomerNotified(true)->save();
                    } catch (\Exception $e) {
                        Mage::getSingleton('core/session')->addError('We can\'t send the invoice email right now.');
                    }
                }
            }
        }
    }

    /**
     * Gets Response API.
     *
     * @param string $orderRef
     * @return array|boolean
     */
    public function getResponseAPI($orderRef)
    {
        $tokenRequest = Mage::getModel("ngenius/gateway_request_tokenrequest");
        $config = Mage::getModel("ngenius/config");

        $requestData = [
            'token' => $tokenRequest->getAccessToken(),
            'request' => [
                'data' => [],
                'method' => \Zend_Http_Client::GET,
                'uri' => $config->getFetchRequestURL($orderRef)
            ]
        ];

        $transferObject = Mage::getModel("ngenius/gateway_http_transferfactory")->create($requestData);
        $response = Mage::getModel("ngenius/gateway_http_transactionfetch")->placeRequest($transferObject);
        return $this->resultValidator($response);
    }

    /**
     * Result Validator.
     *
     * @param array $result
     * @return array|boolean
     */
    public function resultValidator($result)
    {

        if (isset($result['errors']) && is_array($result['errors'])) {
            $this->_error = true;
            return false;
        } else {
            $this->_error = false;
            $this->_ngeniusState = isset($result['_embedded']['payment'][0]['state']) ? $result['_embedded']['payment'][0]['state'] : '';
            return $result;
        }
    }

    /**
     * Fetch Order details.
     *
     * @param string $key
     * @param string $value
     * @return object
     */
    public function fetchOrder($key, $value)
    {
        $model = Mage::getModel('ngenius/standard');
        return $model->getCollection()->addFieldToFilter($key, $value);
    }

    /**
     * Update Table.
     *
     * @param array $data
     * @param object $orderItem
     * @return bool true
     */
    public function updateTable(array $data, $orderItem)
    {
        $orderItem->setEntityId($data['entity_id']);
        $orderItem->setState($this->_ngeniusState);
        $orderItem->setStatus($data['status']);
        $orderItem->setPaymentId($data['payment_id']);
        $orderItem->setCapturedAmt($data['captured_amt']);
        $orderItem->save();
        return true;
    }

    /**
     * Is Error.
     *
     * @return bool true
     */
    public function isError()
    {
        return (bool) $this->_error;
    }
}
