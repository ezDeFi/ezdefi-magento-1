<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Column_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        if(!$row->getOrderId()) {
            return '';
        }

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

        return $orderHtml;
    }
}