<?php

class Ezdefi_Cryptocurrencypayment_Frontend_CallbackController extends Mage_Core_Controller_Front_Action
{
    CONST PAY_ON_TIME   = 1;
    CONST PAID_OUT_TIME = 2;

    public function ConfirmOrderAction()
    {
        $requests  = Mage::app()->getRequest()->getParams();
        $paymentId = $requests['paymentid'];

        if ($paymentId) {
            $payment = Mage::helper('cryptocurrencypayment/GatewayApi')->checkPaymentComplete($paymentId);
            $uoid        = $payment['uoid'];
            $orderId     = explode('-', $uoid)[0];
            $hasAmountId = explode('-', $uoid)[1];

            if ($payment['status'] == 'DONE') {
                $message = 'Payment ID: ' . $paymentId . '<br> 
                            Status: ' . $payment['status'] . '<br>
                            Use Ezdefi Wallet: ' . ($hasAmountId ? 'false' : 'true').'<br>
                            Tx: '.($payment['explorer_url'] ? $payment['explorer_url'] : 'none');
                if ($hasAmountId == 1) {
                    $exceptionCollection = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                    $exception           = $exceptionCollection->getFirstItem();
                    $exception->setData('paid', self::PAY_ON_TIME);
                    $exception->setData('explorer_url', $payment['explorer_url']);
                    $exception->save();
                    $this->deleteExceptionByOrderId($orderId, $payment['_id']);
                } else {
                    $this->deleteExceptionByOrderId($orderId);
                }
                return $this->getResponse()->setBody(json_encode(['order_success' => $this->setProcessingForOrder($orderId, $message)]));
            }
            if ($payment['status'] == 'EXPIRED_DONE') {
                $exceptionCollection = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                $exception           = $exceptionCollection->getFirstItem();
                $exception->setData('paid', self::PAID_OUT_TIME);
                $exception->setData('explorer_url', $payment['explorer_url']);
                $exception->save();
                $this->deleteExceptionByOrderId($orderId, $payment['_id']);

                return $this->getResponse()->setBody(json_encode(['order_success' => "expired done"]));
            }
        } else {
            $transactionId = $this->_request->getParam('id');
            $explorerUrl   = $this->_request->getParam('explorerUrl');

            $transaction   = Mage::helper('cryptocurrencypayment/GatewayApi')->getTransaction($transactionId, $explorerUrl);
            $valueResponse = $transaction->value * pow(10, -$transaction->decimal);

            if ($transaction->status === 'ACCEPTED') {
                $this->addException($transaction->currency, $valueResponse,$transaction->explorerUrl);
            }
            return $this->getResponse()->setBody(json_encode(['order_success' => "unknown transaction"]));
        }
    }


    private function deleteExceptionByOrderId($orderId, $paymentId = null) {
        $collection = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->getCollection()
            ->addFieldToFilter('order_id', $orderId);

        if($paymentId) {
            $collection->addFieldToFilter('payment_id', array('neq' => $paymentId));
        }
        $collection->walk('delete');
    }

    private function addException($cryptoCurrency, $valueResponse, $exploreUrl)
    {
        $expiration = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
        $exceptionModel = Mage::getModel('ezdefi_cryptocurrencypayment/exception');
        $exceptionModel->setData([
            'payment_id' => null,
            'order_id'   => null,
            'currency'   => $cryptoCurrency,
            'amount_id'  => $valueResponse,
            'expiration' => $expiration,
            'paid'       => 3,
            'has_amount' => 1,
            'explorer_url' => $exploreUrl
        ]);
        $exceptionModel->save();
    }

    private function setProcessingForOrder($orderId, $message)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->setData('state', "processing");
        $order->setStatus("processing");
        $history = $order->addStatusHistoryComment($message, false);
        $history->setIsCustomerNotified(true);
        $order->save();

        return 'true';
    }
}