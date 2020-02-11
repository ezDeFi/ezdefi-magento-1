<?php

class Ezdefi_Cryptocurrencypayment_Block_Config_Cryptocurrencies extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml() {
        $currenciesElement = $this->oldCurrencyConfig();

        $html = '<div class="grid">
                    <div class="ezdefi__list-currency-delete"></div>
                    <table class="border">
                        <thead>
                            <tr class="headings">
                                <th class="ezdefi__table-head--currency">Name</th>
                                <th>Discount</th>
                                <th>Expiration (minutes)</th>
                                <th class="ezdefi__table-head--wallet-address">Wallet Address</th>
                                <th>Block Confirmation</th>
                                <th class="coin-decimal">Decimal</th>
                                <th class="col-actions" colspan="1">Action</th>
                            </tr>
                        </thead>
                        <tbody id="ezdefi-configuration-coin-table">
                            '.$currenciesElement.'
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6"></td>
                                <td>
                                    <button class="scalable add" title="Add" type="button" id="ezdefi-configuration-add-coin">
                                        <span>Add Coin</span>
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>';

        return $html;
    }
    private function oldCurrencyConfig() {
        $currenciesData =  Mage::getModel('ezdefi_cryptocurrencypayment/currency')->getCollection()->setOrder('`order`', 'ASC')->getData();

        if(empty($currenciesData)) {
            return $this->defaultCurrencyConfig();
        }
        return $this->renderCurrency($currenciesData);
    }

    private function defaultCurrencyConfig() {
        $defaultCurrencyData = [
            [
                'logo'        => 'https://s2.coinmarketcap.com/static/img/coins/64x64/1.png',
                'name'        => 'Bitcoin',
                'symbol'      => 'btc',
                'currency_id' => '5e144ac31565572569b8868a',
                'decimal'     => 8,
                'currency_decimal' => 8,
                'description' => '',
            ],
            [
                'logo'        => 'https://s2.coinmarketcap.com/static/img/coins/64x64/1027.png',
                'name'        => 'Ethereum',
                'symbol'      => 'eth',
                'currency_id' => '5e144af81565572569b8868b',
                'decimal'     => 8,
                'currency_decimal' => 18,
                'description' => '',
            ],
            [
                'logo'        => 'https://s2.coinmarketcap.com/static/img/coins/64x64/2714.png',
                'name'        => 'NewSD',
                'symbol'      => 'newsd',
                'currency_id' => '5e144d161565572569b88693',
                'decimal'     => 4,
                'currency_decimal' => 6,
                'description' => '',
            ]
        ];
        return $this->renderDefaultCurrency($defaultCurrencyData);
    }

    private function renderCurrency($currenciesData) {
        $html = '';
        foreach ($currenciesData as $currencyData) {
            $html .= '<tr>
                <td>
                    <p class="ezdefi__currency-symbol">
                        <img src="' . $currencyData['logo'] . '" alt="">
                        <span style="text-transform: uppercase">' . $currencyData['symbol'] . '</span>
                    </p>
                    <input type="hidden" class="ezdefi__currency-id-input" value="' . $currencyData['currency_id'] . '">
                     <input type="hidden" 
                        class="ezdefi__currency-orderby-input" 
                        name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][edit]['.$currencyData['currency_id'].'][order]" 
                        value="'.$currencyData['order'].'">
                    <input type="hidden" 
                        class="ezdefi__currency-logo-input" 
                        name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][edit]['.$currencyData['currency_id'].'][max_decimal]" 
                        value="'.$currencyData['currency_decimal'].'">
                </td>
                <td><input type="text" 
                    name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][edit][' . $currencyData['currency_id'] . '][discount]" 
                    class="ezdefi__config-input ezdefi__table-config-input ezdefi__currency-discount-input
                    validate-min-max max-100 validate-not-negative-number only-float"
                    value="' . (float)$currencyData['discount'] . '">
                    <span>%</span>
                </td>
                <td><input type="text" 
                    name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][edit][' . $currencyData['currency_id'] . '][lifetime]"
                    class="ezdefi__config-input ezdefi__payment-lifetime-input
                    ezdefi__table-config-input validate-not-negative-number validate-digits only-positive-integer"
                    value="' . ($currencyData['payment_lifetime'] / 60) . '"></td>
                <td><input type="text" 
                    class="ezdefi__config-input ezdefi__table-config-input ezdefi__wallet-address-input
                    required-entry"
                    name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][edit][' . $currencyData['currency_id'] . '][wallet_address]" 
                    value="' . $currencyData['wallet_address'] . '"></td>
                <td><input type="text"
                    class="ezdefi__config-input ezdefi__table-config-input ezdefi_block-confirmation-input
                    validate-not-negative-number validate-digits only-positive-integer"
                    name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][edit][' . $currencyData['currency_id'] . '][block_confirmation]" 
                    value="' . $currencyData['block_confirmation'] . '"></td>
                <td><input type="text"
                    class="ezdefi__config-input ezdefi__table-config-input ezdefi__currency-decimal-input
                    validate-min-max min-2 max-'.$currencyData['currency_decimal'].' validate-not-negative-number validate-digits required-entry only-positive-integer"
                    name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][edit][' . $currencyData['currency_id'] . '][decimal]"
                    value="' . $currencyData['decimal'] . '">
                    <span class="ezdefi__warning_edit_decimal" style="font-size: 12px;color: red; display:none"><b>Changing Decimal can cause to payment interruption</b></span>
                    </td>
                <td class="col-actions" colspan="1">
                    <button class="scalable delete btn-delete-curency-config" type="button" data-currency-id="' . $currencyData['currency_id'] . '"><span>Delete</span></button>
                </td>
            </tr>';
        }
        return $html;
    }

    private function renderDefaultCurrency($currenciesData) {
        $html = '';
        foreach ($currenciesData as $key => $currencyData) {
            $html .= '<tr>
                <td class="ezdefi__currency-td">
                    <input type="hidden" class="ezdefi__currency-symbol-input" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][symbol]" value="'.$currencyData['symbol'].'">
                    <input type="hidden" class="ezdefi__currency-name-input" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][name]" value="'.$currencyData['name'].'">
                    <input type="hidden" class="ezdefi__currency-id-input" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][id]" value="'.$currencyData['currency_id'].'">
                    <input type="hidden" class="ezdefi__currency-description-input" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][description]" value="'.$currencyData['description'].'">
                    <input type="hidden" class="ezdefi__currency-logo-input" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][logo]" value="'.$currencyData['logo'].'">
                    <input type="hidden" class="ezdefi__currency-max-decimal-input" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][max_decimal]" value="'.$currencyData['currency_decimal'].'">
                    <input type="hidden" class="ezdefi__currency-orderby-input" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][order]" value="'.$key.'">
                <p class="ezdefi__currency-symbol"><img src="'.$currencyData['logo'].'"><span>btc</span></p></td>
                <td>
                    <input type="text" 
                        class="ezdefi__config-input ezdefi__table-config-input ezdefi__currency-discount-input 
                        validate-not-negative-number only-float validate-min-max max-100" 
                        name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][discount]" value="0"> 
                    <span>%</span>
                </td>
                <td>
                    <input type="text" 
                    class="ezdefi__config-input ezdefi__table-config-input ezdefi__payment-lifetime-input 
                    validate-not-negative-number validate-digits only-positive-integer" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][lifetime]" value="15">
                </td>
                <td>
                    <input type="text" 
                    class="ezdefi__config-input ezdefi__table-config-input ezdefi__wallet-address-input 
                    required-entry" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][wallet_address]"></td>
                <td><input type="text" class="ezdefi__table-config-input ezdefi_block-confirmation-input validate-not-negative-number validate-digits only-positive-integer" name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][block_confirmation]" value="1"></td>
                <td><input type="text" 
                    class="ezdefi__config-input ezdefi__table-config-input ezdefi__currency-decimal-input
                    validate-min-max min-2 max-'.$currencyData['currency_decimal'].' validate-not-negative-number required-entry validate-digits only-positive-integer" 
                    name="groups[ezdefi_cryptocurrencypayment][fields][currency][value][add]['.$currencyData['currency_id'].'][decimal]" 
                    value="'.$currencyData['decimal'].'">
                </td>
                <td>
                    <button class="action-delete canel-add-currency" type="button"><span>Delete</span></button>
                </td>
            </tr>';
        }
        return $html;
    }
}