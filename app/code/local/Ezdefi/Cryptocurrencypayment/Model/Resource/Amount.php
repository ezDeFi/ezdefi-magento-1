<?php

class Ezdefi_Cryptocurrencypayment_Model_Resource_Amount extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ezdefi_cryptocurrencypayment/amount', 'id');
    }
}