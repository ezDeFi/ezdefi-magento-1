<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $exceptionId = $row->getId();
        $orderId = $row->getOrderId();

        $urlDelete  = Mage::helper("adminhtml")->getUrl('*/*/delete/exception_id/' . $exceptionId);
        $urlConfirm = Mage::helper("adminhtml")->getUrl('*/*/confirm/exception_id/' . $exceptionId);

        $amountHtml = '<a href="' . $urlDelete . '" class="ezdefi__exception-action" data-confirm-content="Are you sure you want to delete this record?">'.$this->__('Delete').'</a><br>';

        if($orderId) {
            $amountHtml .= '<a href="' . $urlConfirm . '" class="ezdefi__exception-action" data-confirm-content="Are you sure you want to confirm this order?">'.$this->__('Confirm').'</a><br>';
        }

        return $amountHtml;
    }
}