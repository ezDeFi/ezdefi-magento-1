<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_PaymentInformation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $explorerUrl = $row->getExplorerUrl();
        $explorerUrlRow = isset($explorerUrl) ? '<tr>
                                <td class="border-none">Explorer url</td>
                                <td class="border-none"><a target="_blank" href="'.$explorerUrl.'"><span style="word-break: break-all">View Transaction Detail</span></a></td>
                            </tr>' : '';

        if($row->getOrderId()){
            $paid = $row->getPaid();

            $payStatus= 'No';
            if($paid == 1) {
                $payStatus = 'Paid on time';
            } else if ($paid == 2) {
                $payStatus = 'Paid on expiration';
            }

            $paymentHtml = '<table>
                <tbody>
                    <tr>
                        <td class="border-none" style="width: 130px">Expiration</td>
                        <td class="border-none">'.$row->getExpiration().'</td>
                    </tr>
                    <tr>
                        <td class="border-none">Paid</td>
                        <td class="border-none">'.$payStatus.'</td>
                    </tr>
                    <tr>
                        <td class="border-none">Pay by ezdefi wallet</td>
                        <td class="border-none">'.($row->getHasAmount() ? 'No' : 'Yes').'</td>
                    </tr>
                    <tr>
                    '.$explorerUrlRow.'
                </tbody>
            </table>';
        } else {
            $paymentHtml = '<table>
                <tbody>
                    '.$explorerUrlRow.'
                </tbody>
            </table>';
        }



        return $paymentHtml;
    }
}