<?php

class Ezdefi_Cryptocurrencypayment_Frontend_CallbackController extends Mage_Core_Controller_Front_Action
{
    CONST PAY_ON_TIME   = 1;
    CONST PAID_OUT_TIME = 3;

    public function ConfirmOrderAction()
    {
        $requests  = Mage::app()->getRequest()->getParams();
        $paymentId = $requests['paymentid'];

        if ($paymentId) {
            $payment = Mage::helper('cryptocurrencypayment/GatewayApi')->checkPaymentComplete($paymentId);
            if ($payment['status'] == 'DONE') {
                $uoid        = $payment['uoid'];
                $orderId     = explode('-', $uoid)[0];
                $hasAmountId = explode('-', $uoid)[1];

                if ($hasAmountId == 1) {
                    $exceptionCollection = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                    $exception           = $exceptionCollection->getFirstItem();
                    $exception->setData('paid', self::PAY_ON_TIME);
                    $exception->setData('explorer_url', $payment['explorer_url']);
                    $exception->save();
                } else {
                    $exceptions = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->getCollection()->addFieldToFilter('order_id', $orderId);
                    foreach ($exceptions as $exceptionToDelete) {
                        $exceptionToDelete->delete();
                    }
                }
                return $this->getResponse()->setBody(json_encode(['order_success' => $this->setProcessingForOrder($orderId)]));
            }
            if ($payment['status'] == 'EXPIRED_DONE') {
                $exceptionCollection = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->getCollection()->addFieldToFilter('payment_id', $payment['_id']);
                $exception           = $exceptionCollection->getFirstItem();
                $exception->setData('paid', self::PAID_OUT_TIME);
                $exception->setData('explorer_url', $payment['explorer_url']);
                $exception->save();
                return $this->getResponse()->setBody(json_encode(['order_success' => "expired done"]));
            }
        } else {
            $transactionId = $this->_request->getParam('id');
            $explorerUrl   = $this->_request->getParam('explorerUrl');

            $transaction   = Mage::helper('cryptocurrencypayment/GatewayApi')->getTransaction($transactionId, $explorerUrl);
            $valueResponse = $transaction->value * pow(10, -$transaction->decimal);

            if ($transaction->status === 'ACCEPTED') {
                $this->addException(null, $transaction->currency, $valueResponse, null, 1, 3, $transaction->explorerUrl);
                $exceptionModel = $this->_exceptionFactory->create();
                $exceptionModel->addData([
                    'payment_id'   => null,
                    'order_id'     => null,
                    'currency'     => $transaction->currency,
                    'amount_id'    => $valueResponse,
                    'expiration'   => $this->_date->gmtDate(),
                    'paid'         => 3,
                    'has_amount'   => 1,
                    'explorer_url' => $transaction->explorerUrl
                ]);
                $exceptionModel->save();
            }
            return $this->getResponse()->setBody(json_encode(['order_success' => "unknown transaction"]));
        }
    }

    private function setProcessingForOrder($orderId)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->setData('state', "processing");
        $order->setStatus("processing");
        $history = $order->addStatusHistoryComment('Order was set to Processing by Ezdefi payment method.', false);
        $history->setIsCustomerNotified(true);
        $order->save();

        return 'true';
    }
}