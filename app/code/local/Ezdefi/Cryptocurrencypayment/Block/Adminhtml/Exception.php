<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cryptocurrencypayment';
        $this->_controller = 'adminhtml_Exception';
        $this->_headerText = 'Exception pending';

        parent::__construct();
    }

}