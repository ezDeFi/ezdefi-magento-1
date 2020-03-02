<?php

class Ezdefi_Cryptocurrencypayment_Block_Form_Cryptocurrencypayment extends Mage_Payment_Block_Form
{
    private $websiteData;

    protected function _construct()
    {
        $this->websiteData = Mage::helper('cryptocurrencypayment/GatewayApi')->getWebsiteData();
        parent::_construct();
        $this->setTemplate('cryptocurrencypayment/form/cryptocurrencypayment.phtml');
    }

    public function getCoins()
    {
        $currencies          = $this->websiteData->coins;
        $order               = Mage::getSingleton('checkout/session')->getQuote();
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
                <input  type="radio" name="currency-selected-to-order" class="hidden ezdefi__select-currency--checkbox"
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

}