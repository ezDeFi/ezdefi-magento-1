<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $exceptionId =  $row->getId();
        $orderId = $row->getIncrementId();
        $paid = $row->getPaid();
        $explorerUrl = $row->getExplorerUrl();
        $expiration = $row->getExpiration();
        $hasAmount = $row->getHasAmount();

        $orderHtml = '';
        if($orderId) {
            $payStatus= 'No';
            if($paid == 1) {
                $payStatus = 'Paid on time';
            } else if ($paid == 2) {
                $payStatus = 'Paid on expiration';
            }
            $explorerUrlRow = isset($explorerUrl) ? '<tr>
                                <td class="border-none">Explorer url</td>
                                <td class="border-none"><a target="_blank" href="'.$explorerUrl.'">'.substr($explorerUrl, 0, 50).'</a></td>
                            </tr>' : '';
            $orderHtml .= '<table>
                        <tbody>
                            <tr>
                                <td class="border-none">Order id</td>
                                <td class="border-none">'.$orderId.'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Expiration</td>
                                <td class="border-none">'.$expiration.'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Paid</td>
                                <td class="border-none">'.$payStatus.'</td>
                            </tr>
                            <tr>
                                <td class="border-none">Pay by ezdefi wallet</td>
                                <td class="border-none">'.($hasAmount ? 'No' : 'Yes').'</td>
                            </tr>
                            '.$explorerUrlRow.'
                        </tbody>
                    </table>';
        }
        $urlGetOrderPending = Mage::helper("adminhtml")->getUrl('*/*/getOrderPending');
        $urlAssign = Mage::helper("adminhtml")->getUrl('*/*/assign/exception_id/'.$exceptionId);

        $orderHtml .= '<select class="ezdefi__select-pending-order" style="width: 300px" data-check-loaded="1" data-url-get-order="'.$urlGetOrderPending.'">
                        <option value=""></option>
                    </select>
                    <button class="ezdefi__btn-assign-order" 
                        data-url-assign="'.$urlAssign.'" data-order-to-assign="">Assign</button>';

        return $orderHtml;
    }
}