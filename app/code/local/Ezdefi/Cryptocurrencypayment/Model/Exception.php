<?php

class ezdefi_cryptocurrencypayment_Model_Exception extends Mage_Core_Model_Abstract
{
    protected $_isPkAutoIncrement = false;

    protected function _construct()
    {
        $this->_init('ezdefi_cryptocurrencypayment/exception');
    }
}