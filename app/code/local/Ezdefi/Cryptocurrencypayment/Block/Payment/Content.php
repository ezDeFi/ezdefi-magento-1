<?php

class Ezdefi_Cryptocurrencypayment_Block_Payment_Content extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->websiteData = Mage::helper('cryptocurrencypayment/GatewayApi')->getWebsiteData();
        parent::_construct();
        $this->setTemplate('cryptocurrencypayment/redirect.phtml');
    }

    public function getCoins()
    {
        $currencies = $this->websiteData->coins;
        $order      = new Mage_Sales_Model_Order();
        $orderId    = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order->loadByIncrementId($orderId);
        $defaultCoin = $this->getDefaultCoin();

        $currenciesWithPrice = Mage::helper('cryptocurrencypayment/GatewayApi')->getCurrenciesWithPrice($currencies, $order['grand_total'], $order['base_currency_code']);
        $html                = '';

        foreach ($currenciesWithPrice as $key => $currency) {
            $html .= '<label class="ezdefi__select-currency--label" for="ezdefi__select-currency-' . $currency->token->_id . '" >';

            if ($currency->token->description) {
                $html .= '<img src="' . $currency->token->logo . '" alt="" class="ezdefi-select-currency-item--logo" title="' . $currency->token->description . '">';
            } else {
                $html .= '<img src="' . $currency->token->logo . '" alt="" class="ezdefi__select-currency-item--logo">';
            }
            $html .= '<span class="ezdefi__select-currency-item--price">' . $currency->token->price . '</span>
                <span class="ezdefi__select-currency-item--symbol">' . $currency->token->symbol . '</span>
                <span class="ezdefi__select-currency-item--discount">-' . (float)$currency->discount . '%</span>
                <input  type="radio" name="payment[crypto_currency_id]" class="hidden ezdefi__select-currency--checkbox"
                    '.($currency->token->_id === $defaultCoin->token->_id ? 'checked' : '').'
                    id="ezdefi__select-currency-' . $currency->token->_id . '"
                    value="' . $currency->token->_id . '"
                    data-name="' . $currency->token->name . '"
                    data-coin-id="' . $currency->_id . '"
                    data-logo="' . $currency->token->logo . '"
                    data-symbol="' . $currency->token->symbol . '"
                    data-discount="' . $currency->discount . '">
            </label>';
        }
        return $html;
    }

    public function getDefaultCoin()
    {
        $currencies        = $this->websiteData->coins;
        $defaultCurrencyId = Mage::getSingleton('checkout/session')->getCryptoCurrencyId();
        foreach ($currencies as $currency) {
            if ($currency->token->_id === $defaultCurrencyId) {
                return $currency;
            }
        }
    }

    public function checkEnableSimple()
    {
        return $this->websiteData->website->payAnyWallet;
    }

    public function checkEnableEzdefi()
    {
        return $this->websiteData->website->payEzdefiWallet;
    }

    public function checkEnableOneMethod()
    {
        if ($this->websiteData->website->payAnyWallet && !$this->websiteData->website->payEzdefiWallet ||
            !$this->websiteData->website->payAnyWallet && $this->websiteData->website->payEzdefiWallet
        ) {
            return true;
        }
        return false;
    }

    public function getResponseUrl() {
        return Mage::getUrl('cryptocurrencypayment/payment/response', array('_secure' => false));
    }
}
