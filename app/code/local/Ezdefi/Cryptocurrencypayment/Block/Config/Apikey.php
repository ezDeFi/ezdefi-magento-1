<?php


class Ezdefi_Cryptocurrencypayment_Block_Config_Apikey extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml()
    {
        $apiKey = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/api_key');

        $html = '<input id="payment_us_ezdefi_payment_api_key" 
                    name="groups[ezdefi_cryptocurrencypayment][fields][api_key][value]" 
                    value="' . $apiKey . '"
                    class="ezdefi__config-input ezdefi__api-key input-text
                    required-entry validate-api-key"
                    id="ezdefi__api-key"
                    type="text">';
        return $html;
    }
}
