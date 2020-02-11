<?php
class Ezdefi_Cryptocurrencypayment_Model_Resource_Currency_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_isPkAutoIncrement = false;
    public function _construct()
    {
        $this->_init('ezdefi_cryptocurrencypayment/currency');
    }
}