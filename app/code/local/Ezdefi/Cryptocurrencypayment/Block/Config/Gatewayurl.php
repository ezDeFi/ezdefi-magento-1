<?php

class Ezdefi_Cryptocurrencypayment_Block_Config_Gatewayurl extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $gatewayUrl = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/gateway_api_url');

        return '<input 
                    id="payment_us_ezdefi_payment_gateway_api_url" 
                    name="groups[ezdefi_cryptocurrencypayment][fields][gateway_api_url][value]"  
                    value="' . $gatewayUrl . '" 
                    class="ezdefi__gateway-api-url-input
                        validate-url required-entry input-text" 
                    placeholder="http://merchant-api.ezdefi.com/api"
                    type="text">';
    }
}