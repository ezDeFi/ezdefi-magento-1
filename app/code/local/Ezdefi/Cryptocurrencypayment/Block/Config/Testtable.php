<?php

class Ezdefi_Cryptocurrencypayment_Block_Config_Testtable extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function _prepareToRender()
    {
        $this->addColumn('from_price', array(
            'label' => 'From Price',
            'style' => 'width:100px',
        ));
        $this->addColumn('cost', array(
            'label' => 'Shipping Cost',
            'style' => 'width:100px',
        ));

        $this->_addAfter       = false;
        $this->_addButtonLabel = 'Add';
    }
}
