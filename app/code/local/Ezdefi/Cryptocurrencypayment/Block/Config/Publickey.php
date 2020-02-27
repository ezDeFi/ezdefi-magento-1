<?php


class Ezdefi_Cryptocurrencypayment_Block_Config_Publickey extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml()
    {
        $publicKey = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/public_key');

        $html = '<input id="payment_us_ezdefi_payment_public_key" 
                    name="groups[ezdefi_cryptocurrencypayment][fields][public_key][value]" 
                    value="' . $publicKey . '"
                    class="ezdefi__config-input input-text
                    required-entry validate-public-key"
                    id="ezdefi__public-key"
                    type="text">';
        return $html;
    }
}
