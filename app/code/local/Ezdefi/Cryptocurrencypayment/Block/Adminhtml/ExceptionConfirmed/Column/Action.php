<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Column_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $exceptionId = $row->getId();
        $urlDelete  = Mage::helper("adminhtml")->getUrl('*/*/delete/exception_id/' . $exceptionId);
        $urlRevert  = Mage::helper("adminhtml")->getUrl('*/*/revert/exception_id/' . $exceptionId);

        $amountHtml = '<a href="' . $urlDelete . '" class="ezdefi__exception-action" data-confirm-content="Are you sure you want to delete this record?">Delete</a><br>
                    <a href="' . $urlRevert . '" class="ezdefi__exception-action" data-confirm-content="Are you sure you want to revert this record?">Revert</a><br>';

        return $amountHtml;
    }
}