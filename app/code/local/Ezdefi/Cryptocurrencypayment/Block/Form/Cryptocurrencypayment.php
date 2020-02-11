<?php
// app/code/local/Ezdefi/Cryptocurrencypayment/Block/Form/Cryptocurrencypayment.php
class Ezdefi_Cryptocurrencypayment_Block_Form_Cryptocurrencypayment extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cryptocurrencypayment/form/cryptocurrencypayment.phtml');
    }
}