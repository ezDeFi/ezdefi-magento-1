<?php
class Ezdefi_Cryptocurrencypayment_Frontend_PaymentController extends Mage_Core_Controller_Front_Action {
    public function createAction() {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $requests = Mage::app()->getRequest()->getParams();
        $currencyId = $requests['currency_id'];
        $cryptoCurrency    = Mage::getModel('ezdefi_cryptocurrencypayment/currency')->getCollection()->addFieldToFilter('currency_id', $currencyId)->getData()[0];
        $order             = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        $paymentType = $requests['type'];

        if($paymentType === 'simple') {
            $payment = $this->createPaymentSimple($order, $cryptoCurrency);

            $block = $this->getLayout()
                ->createBlock('cryptocurrencypayment/payment_simplemethod', 'render simple method block', [
                    'data' => [
                        'payment'        => $payment,
                        'originValue'    => $order['grand_total'] * (100 - $cryptoCurrency['discount']) / 100,
                        'originCurrency' => $order['base_currency_code']]])
                ->setTemplate('cryptocurrencypayment/payment/simplemethod.phtml')
                ->toHtml();
        } else if ($paymentType === 'ezdefi') {
            $payment = $this->createPaymentEzdefi($order, $cryptoCurrency);

            $block = $this->getLayout()
                ->createBlock('cryptocurrencypayment/payment_ezdefimethod', 'render simple method block', [
                    'data' => [
                        'payment' => $payment,
                        'originValue' => $order['grand_total']
                    ]])
                ->setTemplate('cryptocurrencypayment/payment/ezdefimethod.phtml')
                ->toHtml();
        }

        echo $block;
    }

    private function createPaymentSimple($order, $cryptoCurrency) {
        $amountCollection  = Mage::getModel('ezdefi_cryptocurrencypayment/amount');
        $originCurrency    = $order['base_currency_code'];
        $originValue       = $order['grand_total'];
        $currencyExchange  =  Mage::helper('cryptocurrencypayment/GatewayApi')->getExchange($originCurrency, $cryptoCurrency['symbol']);
        $amount            = round($currencyExchange * $originValue * (100 - $cryptoCurrency['discount'])/100, $cryptoCurrency['decimal']);;
        $variation = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/acceptable_variation');

        $amountId = (float)$amountCollection->getCollection()->createAmountId(
            $cryptoCurrency['symbol'],
            (float)$amount,
            $cryptoCurrency['payment_lifetime'],
            $cryptoCurrency['decimal'],
            $variation
        );

        $payment = Mage::helper('cryptocurrencypayment/GatewayApi')->createPayment([
            'uoid'     => $order['entity_id'].'-1',
            'amountId' => true,
            'value'    => $amountId,
            'to'       => $cryptoCurrency['wallet_address'],
            'currency' => $cryptoCurrency['symbol'].':'.$cryptoCurrency['symbol'],
            'safedist' => $cryptoCurrency['block_confirmation'],
            'duration' => $cryptoCurrency['payment_lifetime'],
            'callback' => Mage::getUrl('ezdefi_frontend/callback/confirmorder')
        ]);
        $this->addException($order, $cryptoCurrency, $payment->_id, $amountId, 1);

        return $payment;
    }

    private function createPaymentEzdefi($order, $cryptoCurrency) {
        $payment = Mage::helper('cryptocurrencypayment/GatewayApi')->createPayment([
            'uoid'     => $order['entity_id'].'-0',
            'value'    => $order['grand_total'] * (100 - $cryptoCurrency['discount'])/100,
            'to'       => $cryptoCurrency['wallet_address'],
            'currency' => $order['base_currency_code'].':'.$cryptoCurrency['symbol'],
            'safedist' => $cryptoCurrency['block_confirmation'],
            'duration' => $cryptoCurrency['payment_lifetime'],
            'callback' => Mage::getUrl('ezdefi_frontend/callback/confirmorder')
        ]);

        $cryptoValue = $payment->value * pow(10, - $payment->decimal);

        $this->addException($order, $cryptoCurrency, $payment->_id, $cryptoValue, 0);
        return $payment;
    }

    private function addException($order, $cryptoCurrency, $paymentId, $amountId, $hasAmount) {
        $expiration = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', strtotime('+'.$cryptoCurrency['payment_lifetime'].' second'));

        $exceptionModel = Mage::getModel('ezdefi_cryptocurrencypayment/exception');
        $exceptionModel->setData([
            'payment_id' => $paymentId,
            'order_id' => $order['entity_id'],
            'currency' => $cryptoCurrency['symbol'],
            'amount_id' => $amountId,
            'expiration' => $expiration,
            'paid' => 0,
            'has_amount' => $hasAmount,
        ]);
        $exceptionModel->save();
    }

    public function checkOrderCompleteAction() {
        $orderId =  Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order   = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        if ($order->getStatus() === 'processing') {
//            $this->_cart->setLastOrderId(null)
//                ->setLastRealOrderId(null)
//                ->setLastOrderStatus(null);
            $this->getResponse()->setBody(json_encode(['orderStatus' => 'processing']));
        } else {
            $this->getResponse()->setBody(json_encode(['orderStatus' => 'pending']));
        }
    }


}