<?php

class Ezdefi_Cryptocurrencypayment_Model_Resource_Currency extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_isPkAutoIncrement = false;
    protected function _construct()
    {
        $this->_init('ezdefi_cryptocurrencypayment/currency', 'entity_id');
    }
}