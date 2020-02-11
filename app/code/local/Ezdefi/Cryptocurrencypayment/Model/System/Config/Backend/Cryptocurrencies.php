<?php

class Ezdefi_Cryptocurrencypayment_Model_System_Config_Backend_Cryptocurrencies extends Mage_Core_Model_Config_Data
{

    protected $_eventPrefix = 'local_config_backend_cryptocurrencies';

    protected function _beforeSave()
    {
        $request = $this->getValue();

        if (isset($request['ids_delete'])) {
            $this->deleteCurrency($request['ids_delete']);
        }

        if (isset($request['add'])) {
            $this->validateAddCurrency($request['add'], 'add');
            $this->saveCurrency($request['add']);
        }

        if (isset($request['edit'])) {
            $this->validateAddCurrency($request['edit'], 'edit');
            $this->updateCurrency($request['edit']);
        }

        $this->setValue(intval($this->getValue()));
    }

    private function deleteCurrency($ids) {
        foreach ($ids as $id) {
            $currencyRecords = Mage::getModel('ezdefi_cryptocurrencypayment/currency')
                ->getCollection()
                ->addFieldToFilter('currency_id', $id);
            foreach ($currencyRecords as $currencyRecord) {
                $currencyRecord->delete();
            }
        }
    }

    private function validateAddCurrency($currenciesData, $type) {
        foreach($currenciesData as $currencyData) {
            $id                 = isset($currencyData['id'])                 ? $currencyData['id'] : '';
            $symbol             = isset($currencyData['symbol'])             ? $currencyData['symbol'] : '';
            $name               = isset($currencyData['name'])               ? $currencyData['name'] : '';
            $logo               = isset($currencyData['logo'])               ? $currencyData['logo'] : '';
            $discount           = isset($currencyData['discount'])           ? $currencyData['discount'] : '';
            $lifetime           = isset($currencyData['lifetime'])           ? $currencyData['lifetime'] : '';
            $walletAddress      = isset($currencyData['wallet_address'])     ? $currencyData['wallet_address'] : '';
            $blockConfirmation  = isset($currencyData['block_confirmation']) ? $currencyData['block_confirmation'] : '';
            $decimal            = isset($currencyData['decimal'])            ? $currencyData['decimal'] : '';
            $maxDecimal         = isset($currencyData['max_decimal'])        ? $currencyData['max_decimal'] : '';

            if($type === 'add') {
                if ($symbol == '') {
                    Mage::throwException(__('Currency symbol is required.'));
                } else if ($name == '') {
                    Mage::throwException(__('Currency name is required.'));
                } else if ($id == '') {
                    Mage::throwException(__('Currency id is required.'));
                } else if ($logo == '') {
                    Mage::throwException(__('Currency logo is required.'));
                }
            }
            if($discount && (filter_var($discount,FILTER_VALIDATE_FLOAT) === false || (float)$discount > 100 || (float)$discount < 0)) {
                Mage::throwException(__('Discount should be float and less than 100.'));
            } else if ($lifetime && (filter_var($lifetime,FILTER_VALIDATE_INT) === false || (int)$lifetime < 0)) {
                Mage::throwException(__('Payment life time is not a positive number.'));
            } else if ($walletAddress == '') {
                Mage::throwException(__('Wallet address is required.'));
            } else if($blockConfirmation && (filter_var($blockConfirmation, FILTER_VALIDATE_INT) === false || (int)$blockConfirmation < 0)) {
                Mage::throwException(__('Block confirmation is not a positive number.'));
            } else if($decimal && (filter_var($decimal, FILTER_VALIDATE_INT) === false || $decimal < 2 || $decimal > $maxDecimal)) {
                Mage::throwException(__('Decimal is invalid'));
            }
        }
    }

    private function saveCurrency($currenciesData) {
        foreach ($currenciesData as $currencyData) {
            Mage::getModel('ezdefi_cryptocurrencypayment/currency')
                ->setData([
                    'currency_id'        => $currencyData['id'],
                    'logo'               => $currencyData['logo'],
                    'symbol'             => $currencyData['symbol'],
                    'name'               => $currencyData['name'],
                    'discount'           => $currencyData['discount'] == '' ? 0 : $currencyData['discount'],
                    'payment_lifetime'   => $currencyData['lifetime'] == '' ? 900: $currencyData['lifetime'] * 60,
                    'wallet_address'     => $currencyData['wallet_address'],
                    'block_confirmation' => $currencyData['block_confirmation'] == '' ? 1 : $currencyData['block_confirmation'],
                    'decimal'            => $currencyData['decimal'] == '' ? $currencyData['max_decimal'] : $currencyData['decimal'],
                    'description'        => $currencyData['description'],
                    'currency_decimal'   => $currencyData['max_decimal'],
                    'order'              => $currencyData['order']
                ])
                ->save(); //save data
        }
    }

    private function updateCurrency($currenciesData) {
        foreach ($currenciesData as $currencyId => $currencyData) {
            $collection =  Mage::getModel('ezdefi_cryptocurrencypayment/currency')->getCollection()->addFieldToFilter('currency_id', $currencyId);
            $currency = $collection->getFirstItem();

            $currency->setData('discount', $currencyData['discount'] == '' ? 0 : $currencyData['discount']);
            $currency->setData('payment_lifetime', $currencyData['lifetime'] == '' ? 900 : $currencyData['lifetime'] * 60);
            $currency->setData('wallet_address', $currencyData['wallet_address']);
            $currency->setData('block_confirmation', $currencyData['block_confirmation'] == '' ? 1 : $currencyData['block_confirmation']);
            $currency->setData('decimal', $currencyData['decimal'] == '' ? $currencyData['max_decimal'] : $currencyData['decimal']);
            $currency->setData('order', $currencyData['order']);

            $currency->save();
        }
    }
}