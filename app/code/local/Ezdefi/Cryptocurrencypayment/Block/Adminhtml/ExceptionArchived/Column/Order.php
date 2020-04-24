<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionArchived_Column_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $orderId = $row->getIncrementId();

        if($orderId) {
            $orderHtml = '<table>
                <tbody>
                    <tr>
                        <td class="border-none" style="width: 130px">Order id</td>
                        <td class="border-none">'.$row->getIncrementId().'</td>
                    </tr>
                    <tr>
                        <td class="border-none">Email</td>
                        <td class="border-none">'.$row->getEmail().'</td>
                    </tr>
                    <tr>
                        <td class="border-none">Customer</td>
                        <td class="border-none">'.$row->getCustomer().'</td>
                    </tr>
                    <tr>
                        <td class="border-none">Total</td>
                        <td class="border-none">'.$row->getTotal().'</td>
                    </tr>
                    <tr>
                        <td class="border-none">Created at</td>
                        <td class="border-none">'.$row->getDate().'</td>
                    </tr>
                </tbody>
            </table>';
        }
        //$urlGetOrderPending = Mage::helper("adminhtml")->getUrl('*/*/getOrderPending');
        //$urlAssign = Mage::helper("adminhtml")->getUrl('*/*/assign/exception_id/'.$exceptionId);
        //
        //$orderHtml .= '<select class="ezdefi__select-pending-order" style="width: 300px" data-check-loaded="1" data-url-get-order="'.$urlGetOrderPending.'">
        //                <option value=""></option>
        //            </select>
        //            <button class="ezdefi__btn-assign-order"
        //                data-url-assign="'.$urlAssign.'" data-order-to-assign="">Assign</button>';

        return $orderHtml;
    }
}