<?php

class Ezdefi_Cryptocurrencypayment_Block_Form_Cryptocurrencypayment extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cryptocurrencypayment/form/cryptocurrencypayment.phtml');
    }

    public function getCoins()
    {
        $currencies = Mage::getModel('ezdefi_cryptocurrencypayment/currency')->getCollection()->setOrder('`order`', 'ASC')->getData();
        $order = Mage::getSingleton('checkout/session')->getQuote();

        $currenciesWithPrice = Mage::helper('cryptocurrencypayment/GatewayApi')->getCurrenciesWithPrice($currencies, $order['grand_total'], $order['base_currency_code']);

        $html = '';
        foreach ($currenciesWithPrice as $key => $currency) {

            $html .= '<label class="ezdefi__select-currency--label" for="ezdefi__select-currency-' . $currency['currency_id'] . '" ';
            if ($key === 0) {
                $html .= 'style="border: 2px solid #54bdff; background: #c0dcf9db"';
            }
            $html .= '>';
            if ($currency['description']) {
                $html .= '<img src="' . $currency['logo'] . '" alt="" class="ezdefi-select-currency-item--logo" title="' . $currency['description'] . '">';
            } else {
                $html .= '<img src="' . $currency['logo'] . '" alt="" class="ezdefi__select-currency-item--logo">';
            }
            $html .= '<span class="ezdefi__select-currency-item--price">' . $currency['price'] . '</span>
                <span class="ezdefi__select-currency-item--symbol">' . $currency['symbol'] . '</span>
                <span class="ezdefi__select-currency-item--discount">-' . (float)$currency['discount'] . '%</span>
                <input  type="radio" name="currency-selected-to-order" class="hidden ezdefi__select-currency--checkbox"
                    id="ezdefi__select-currency-' . $currency['currency_id'] . '"
                    value="' . $currency['currency_id'] . '"
                    data-name="' . $currency['name'] . '"
                    data-logo="' . $currency['logo'] . '"
                    data-symbol="' . $currency['symbol'] . '"
                    data-discount="' . $currency['discount'] . '" ';
            if ($key === 0) {
                $html .= 'checked';
            }
            $html .= '>
            </label>';
        }
        return $html;
    }

    public function checkEnableSimple()
    {
        $paymentMethod = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/payment_method');
        return strpos($paymentMethod, 'simple') !== false;
    }

    public function checkEnableEzdefi()
    {
        $paymentMethod = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/payment_method');
        return strpos($paymentMethod, 'ezdefi') !== false;
    }

    public function checkEnableOneMethod()
    {
        $paymentMethod = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/payment_method');
        $methodArray   = explode(',', $paymentMethod);
        return count($methodArray) === 1;
    }

}