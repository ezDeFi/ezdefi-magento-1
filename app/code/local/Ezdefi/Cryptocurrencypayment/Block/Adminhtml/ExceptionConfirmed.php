<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cryptocurrencypayment';
        $this->_controller = 'adminhtml_exceptionConfirmed';
        $this->_headerText = 'Exception Confirmed';

        parent::__construct();
    }

}