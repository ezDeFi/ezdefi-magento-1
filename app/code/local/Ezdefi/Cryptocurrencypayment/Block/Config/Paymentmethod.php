<?php

class Ezdefi_Cryptocurrencypayment_Block_Config_Paymentmethod extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml()
    {
        $paymentMethod = Mage::getStoreConfig('payment/ezdefi_cryptocurrencypayment/payment_method');

        $simpleMethod = strpos($paymentMethod, 'simple') !== false ? 'checked' : false;
        $ezdefiMethod = strpos($paymentMethod, 'ezdefi') !== false ? 'checked' : false;

        $checkPaymentMethodValue = $simpleMethod === false && $ezdefiMethod === false ? '' : '1';

        $html = '<div class="nested">
                    <div class="field choice admin__field admin__field-option">
                        <input id="payment_us_ezdefi_payment_payment_method_simple" 
                            type="checkbox" 
                            class="admin__control-checkbox ezdefi__simple-payment-checkbox"
                            name="groups[ezdefi_cryptocurrencypayment][fields][payment_method][value][]" 
                            value="simple"
                            ' . $simpleMethod . '
                            >
                        <label for="payment_us_ezdefi_payment_payment_method_simple" class="admin__field-label"><span><b>Pay with any crypto wallet</b></span></label>
                        <div><i>This method will adjust payment amount of each order by an acceptable number to help payment gateway identifying the uniqueness of that order</i></div>
                    </div>
                    <div class="field choice admin__field admin__field-option">
                        <input id="payment_us_ezdefi_payment_payment_method_ezdefi" 
                            type="checkbox" 
                            class="admin__control-checkbox ezdefi__ezdefi-payment-checkbox" 
                            name="groups[ezdefi_cryptocurrencypayment][fields][payment_method][value][]" 
                            value="ezdefi"
                            ' . $ezdefiMethod . '
                            >
                        <label for="payment_us_ezdefi_payment_payment_method_ezdefi" class="admin__field-label"><span><b>Pay with ezDeFi wallet</b></span></label>
                        <div><i>This method is more powerful when amount uniqueness above method reaches allowable limit. Users just need to install ezDeFi wallet then import their private key to pay using qrCode.</i></div>
                    </div>
                    <div>
                        <input type="hidden" class="ezdefi__config-input
                        validate-payment-method check-payment-method-input" value="' . $checkPaymentMethodValue . '">
                    </div>
                </div>';
        return $html;
    }
}
