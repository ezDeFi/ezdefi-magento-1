<?php
class Ezdefi_Cryptocurrencypayment_Model_Resource_Exception_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('ezdefi_cryptocurrencypayment/exception');
    }
}