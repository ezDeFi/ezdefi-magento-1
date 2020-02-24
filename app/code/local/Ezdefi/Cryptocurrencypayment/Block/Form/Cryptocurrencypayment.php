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
        $websiteData         = Mage::helper('cryptocurrencypayment/GatewayApi')->getWebsiteData();
        $currencies          = $websiteData->coins;
        $order               = Mage::getSingleton('checkout/session')->getQuote();
        $currenciesWithPrice = Mage::helper('cryptocurrencypayment/GatewayApi')->getCurrenciesWithPrice($currencies, $order['grand_total'], $order['base_currency_code']);
        $html = '';
        foreach ($currenciesWithPrice as $key => $currency) {
            $html .= '<label class="ezdefi__select-currency--label" for="ezdefi__select-currency-' . $currency->token->_id . '" ';
            if ($key === 0) {
                $html .= 'style="border: 2px solid #54bdff; background: #c0dcf9db"';
            }
            $html .= '>';
            if ($currency->token->description) {
                $html .= '<img src="' . $currency->token->logo . '" alt="" class="ezdefi-select-currency-item--logo" title="' . $currency->token->description . '">';
            } else {
                $html .= '<img src="' . $currency->token->logo . '" alt="" class="ezdefi__select-currency-item--logo">';
            }
            $html .= '<span class="ezdefi__select-currency-item--price">' . $currency->token->price . '</span>
                <span class="ezdefi__select-currency-item--symbol">' . $currency->token->symbol . '</span>
                <span class="ezdefi__select-currency-item--discount">-' . (float)$currency->token->discount . '%</span>
                <input  type="radio" name="currency-selected-to-order" class="hidden ezdefi__select-currency--checkbox"
                    id="ezdefi__select-currency-' . $currency->token->_id . '"
                    value="' . $currency->token->_id . '"
                    data-name="' . $currency->token->name . '"
                    data-coin-id="' . $currency->_id . '"
                    data-logo="' . $currency->token->logo . '"
                    data-symbol="' . $currency->token->symbol . '"
                    data-discount="' . $currency->token->discount . '" ';
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