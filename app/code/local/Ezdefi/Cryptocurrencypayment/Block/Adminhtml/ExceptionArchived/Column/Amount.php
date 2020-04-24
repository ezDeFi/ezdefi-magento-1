<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionArchived_Column_Amount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $amountId    = $row->getAmountId();

        $lengthToCut = 0;
        for ($i = strlen($amountId) - 1; $i > 0; $i--) {
            if ($amountId[$i] === '0') {
                $lengthToCut++;
            } else {
                break;
            }
        }
        $amount = substr($amountId, 0, strlen($amountId) - $lengthToCut);

        if ($amount[strlen($amount) - 1] === '.') {
            $amount = substr($amount, 0, -1);
        }

        $amountHtml = '<p>' . $amount . '</p>';

        $items['amount_id'] = $amountHtml;
        return $amountHtml;
    }
}