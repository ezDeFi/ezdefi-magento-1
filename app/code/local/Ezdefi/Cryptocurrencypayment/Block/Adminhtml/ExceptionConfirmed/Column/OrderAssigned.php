<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Column_OrderAssigned extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $orderHtml = '<table>
            <tbody>
                <tr>
                    <td class="border-none" style="width: 130px">Order id</td>
                    <td class="border-none">'.$row->getNewIncrementId().'</td>
                </tr>
                <tr>
                    <td class="border-none">Email</td>
                    <td class="border-none">'.$row->getNewEmail().'</td>
                </tr>
                <tr>
                    <td class="border-none">Customer</td>
                    <td class="border-none">'.$row->getNewCustomer().'</td>
                </tr>
                <tr>
                    <td class="border-none">Customer</td>
                    <td class="border-none">'.$row->getNewCustomer().'</td>
                </tr>
                <tr>
                    <td class="border-none">Total</td>
                    <td class="border-none">'.$row->getNewTotal().'</td>
                </tr>
                <tr>
                    <td class="border-none">Created at</td>
                    <td class="border-none">'.$row->getNewDate().'</td>
                </tr>
            </tbody>
        </table>';

        return $orderHtml;
    }
}