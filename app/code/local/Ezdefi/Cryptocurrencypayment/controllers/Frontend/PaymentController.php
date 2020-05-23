<?php

class Ezdefi_Cryptocurrencypayment_Frontend_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function contentAction() {
        if(!Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            return $this->_redirect("/");
        }

        $this->loadLayout();
        $block = $this->getLayout()
            ->createBlock('cryptocurrencypayment/payment_content','cryptocurrencypayment')
            ->setTemplate('cryptocurrencypayment/redirect.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function responseAction() {
        Mage::getSingleton('checkout/session')->unsQuoteId();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
        Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=> false));
    }

    public function createAction()
    {
        $orderId        = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $requests       = Mage::app()->getRequest()->getParams();
        $order          = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $cryptoCurrency = Mage::helper('cryptocurrencypayment/GatewayApi')->getCurrency($requests['coin_id']);
        $discount       = (float)number_format((100 - $cryptoCurrency['discount']) / 100, 6);

        $paymentType = $requests['type'];

        if ($paymentType === 'simple') {
            if ($requests['coin_id']) {
                $payment = $this->createPaymentSimple($order, $requests['coin_id'], $cryptoCurrency);
            } else {
                $payment = null;
            }

            $block = $this->getLayout()
                ->createBlock('cryptocurrencypayment/payment_simplemethod', 'render simple method block', [
                    'data' => [
                        'payment'        => $payment,
                        'originValue'    => Mage::helper('cryptocurrencypayment/GatewayApi')->convertExponentialToFloat($order['grand_total'] * $discount),
                        'originCurrency' => $order['base_currency_code']
                    ]
                ])
                ->setTemplate('cryptocurrencypayment/payment/simpleMethod.phtml')
                ->toHtml();
        } else if ($paymentType === 'ezdefi') {
            if ($requests['coin_id']) {
                $payment = $this->createPaymentEzdefi($order, $requests['coin_id'], $cryptoCurrency);
            } else {
                $payment = null;
            }

            $block = $this->getLayout()
                ->createBlock('cryptocurrencypayment/payment_ezdefimethod', 'render simple method block', [
                    'data' => [
                        'payment'     => $payment,
                        'originValue' => $order['grand_total']
                    ]
                ])
                ->setTemplate('cryptocurrencypayment/payment/ezdefiMethod.phtml')
                ->toHtml();
        }

        echo $block;
    }

    private function createPaymentSimple($order, $coinId, $cryptoCurrency)
    {
        $originCurrency   = $order['base_currency_code'];
        $originValue      = $order['grand_total'];
        $currencyExchange = Mage::helper('cryptocurrencypayment/GatewayApi')->getExchange($originCurrency, $cryptoCurrency['token']['symbol']);
        $amount           = $currencyExchange * $originValue * (100 - $cryptoCurrency['discount']) / 100;
        $value = Mage::helper('cryptocurrencypayment/GatewayApi')->convertExponentialToFloat($amount, $cryptoCurrency['decimal']);

        $payment = Mage::helper('cryptocurrencypayment/GatewayApi')->createPayment([
            'uoid'     => $order['entity_id'] . '-1',
            'amountId' => true,
            'coinId'   => $coinId,
            'value'    => $value,
            'to'       => $cryptoCurrency['walletAddress'],
            'currency' => $cryptoCurrency['token']['symbol'] . ':' . $cryptoCurrency['token']['symbol'],
            'safedist' => $cryptoCurrency['blockConfirmation'],
            'duration' => $cryptoCurrency['expiration'] * 60,
            'callback' => Mage::getUrl('ezdefi_frontend/callback/confirmorder')
        ]);

        $this->addException($order, $cryptoCurrency, $payment->_id, $payment->value * pow(10, -$payment->decimal), 1);

        return $payment;
    }

    private function createPaymentEzdefi($order, $coinId, $cryptoCurrency)
    {
        $discount = (float)number_format((100 - $cryptoCurrency['discount']) / 100, 6);
        $value = Mage::helper('cryptocurrencypayment/GatewayApi')->convertExponentialToFloat($order['grand_total'] * $discount);

        $payment = Mage::helper('cryptocurrencypayment/GatewayApi')->createPayment([
            'uoid'     => $order['entity_id'] . '-0',
            'coinId'   => $coinId,
            'value'    => $value,
            'to'       => $cryptoCurrency['walletAddress'],
            'currency' => $order['base_currency_code'] . ':' . $cryptoCurrency['token']['symbol'],
            'safedist' => $cryptoCurrency['blockConfirmation'],
            'duration' => $cryptoCurrency['expiration'] * 60,
            'callback' => Mage::getUrl('ezdefi_frontend/callback/confirmorder')
        ]);

        $cryptoValue = $payment->value * pow(10, -$payment->decimal);

        $this->addException($order, $cryptoCurrency, $payment->_id, $cryptoValue, 0);
        return $payment;
    }

    private function addException($order, $cryptoCurrency, $paymentId, $amountId, $hasAmount)
    {
        $expiration = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', strtotime('+' . ($cryptoCurrency['expiration'] * 60) . ' second'));

        $exceptionModel = Mage::getModel('ezdefi_cryptocurrencypayment/exception');
        $exceptionModel->setData([
            'payment_id' => $paymentId,
            'order_id'   => $order['entity_id'],
            'currency'   => $cryptoCurrency['token']['symbol'],
            'amount_id'  => $amountId,
            'expiration' => time() + $cryptoCurrency['expiration'] * 60,
            'paid'       => 0,
            'has_amount' => $hasAmount,
        ]);
        $exceptionModel->save();
    }

    public function checkOrderCompleteAction()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order   = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        if ($order->getStatus() === 'processing') {
            Mage::getSingleton('checkout/session')->unsQuoteId();
            Mage::getSingleton('checkout/session')->unsLastRealOrderId();
            $this->getResponse()->setBody(json_encode(['orderStatus' => 'processing']));
        } else {
            $this->getResponse()->setBody(json_encode(['orderStatus' => 'pending']));
        }
    }
}