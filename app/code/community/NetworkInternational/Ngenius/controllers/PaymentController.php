<?php
/**
 * Ngenius Payment Controller
 *
 * @category   NetworkInternational
 * @package    NetworkInternational_Ngenius
 * @author     Abzer <info@abzer.com>
 */

class NetworkInternational_Ngenius_PaymentController extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Action proceedAction
     *
     * Processing action after payment
     * @return string URL
     */
    public function proceedAction()
    {
        $model = Mage::getModel("ngenius/core");
        $orderRef = $this->getRequest()->getParam('ref');

        if ($orderRef) {
            $result = $model->getResponseAPI($orderRef);
            if ($result && isset($result['_embedded']['payment']) && is_array($result['_embedded']['payment'])) {
                $action = isset($result['action']) ? $result['action'] : '';
                $paymentResult = $result['_embedded']['payment'][0];
                $orderItem = $model->fetchOrder('reference', $orderRef)->getFirstItem();
                $model->processOrder($paymentResult, $orderItem, $orderRef, $action);
            }
            if ($model->isError()) {
                Mage::getSingleton('core/session')->addError('Failed! There is an issue with your payment transaction.');
                return $this->_redirect('checkout/onepage/failure');
            } else {
                return $this->_redirect('checkout/onepage/success');
            }
        } else {
            return $this->_redirect('checkout');
        }
    }
}
