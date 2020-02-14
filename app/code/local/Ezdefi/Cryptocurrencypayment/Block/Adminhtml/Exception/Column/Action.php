<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $exceptionId = $row->getId();
        $orderId     = $row->getOrderId();
        $paidStatus  = $row->getPaid();

        $amountHtml = '';
        $urlDelete  = Mage::helper("adminhtml")->getUrl('*/*/delete/exception_id/' . $exceptionId);
        $urlConfirm = Mage::helper("adminhtml")->getUrl('*/*/confirm/exception_id/' . $exceptionId);
        $urlRevert  = Mage::helper("adminhtml")->getUrl('*/*/revert/exception_id/' . $exceptionId);

        $amountHtml .= '<a href="' . $urlDelete . '" class="ezdefi__exception-action" data-confirm-content="Are you sure you want to delete this record?">Delete</a><br>';

        if ($paidStatus == 1) {
            $amountHtml .= '<a href="' . $urlRevert . '" class="ezdefi__exception-action" data-confirm-content="Are you sure you want to revert this record?">Revert</a><br>';
        } else if ($orderId != 0) {
            $amountHtml .= '<a href="' . $urlConfirm . '" class="ezdefi__exception-action" data-confirm-content="Are you sure you want to confirm this order?">Confirm</a><br>';
        }

        return $amountHtml;
    }
}