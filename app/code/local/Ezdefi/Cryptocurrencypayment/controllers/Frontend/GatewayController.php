<?php

class Ezdefi_Cryptocurrencypayment_Frontend_GatewayController extends Mage_Core_Controller_Front_Action
{
    public function checkApiKeyAction()
    {
        $requests     = Mage::app()->getRequest()->getParams();
        $apiKeyStatus = Mage::helper('cryptocurrencypayment/GatewayApi')->checkApiKey($requests['api_key'], $requests['gateway_api_url']);
        $this->getResponse()->setBody(json_encode($apiKeyStatus));
    }

    public function checkPublicKeyAction()
    {
        $requests        = Mage::app()->getRequest()->getParams();
        $publicKeyStatus = Mage::helper('cryptocurrencypayment/GatewayApi')->checkPublicKey($requests['public_key'], $requests['api_key'], $requests['gateway_api_url']);
        $this->getResponse()->setBody(json_encode($publicKeyStatus));
    }

    public function testAction()
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
                    id="ezdefi__select-currency-' . $currency->token->currency_id . '"
                    value="' . $currency->token->currency_id . '"
                    data-name="' . $currency->token->name . '"
                    data-logo="' . $currency->token->logo . '"
                    data-symbol="' . $currency->token->symbol . '"
                    data-discount="' . $currency->token->discount . '" ';
            if ($key === 0) {
                $html .= 'checked';
            }
            $html .= '>
            </label>';
        }
        echo $html;
    }
}