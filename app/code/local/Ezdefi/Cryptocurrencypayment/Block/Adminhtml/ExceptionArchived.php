<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionArchived extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cryptocurrencypayment';
        $this->_controller = 'adminhtml_ExceptionArchived';
        $this->_headerText = 'Exception Archived';

        parent::__construct();
    }

}