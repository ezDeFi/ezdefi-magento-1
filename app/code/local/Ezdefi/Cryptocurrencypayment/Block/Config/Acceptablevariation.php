<?php

class Ezdefi_Cryptocurrencypayment_Block_Config_Acceptablevariation extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml() {
        $variation = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/acceptable_variation');

        $html = '<input id="payment_us_ezdefi_payment_variation" 
                    name="groups[ezdefi_cryptocurrencypayment][fields][acceptable_variation][value]" 
                    value="'.$variation.'"
                    class="ezdefi__config-input input-text 
                    required-entry only-float validate-min-max min-0 max-100"
                    type="text">';
        return $html;
    }
}