<?php

class Ezdefi_Cryptocurrencypayment_Model_Test extends Mage_Core_Model_Config_Data
{
    protected $_eventPrefix = 'local_test';

    protected function _beforeSave()
    {
        echo "1";

    }
    public function _afterSave()
    {
        echo "1";
    }
}