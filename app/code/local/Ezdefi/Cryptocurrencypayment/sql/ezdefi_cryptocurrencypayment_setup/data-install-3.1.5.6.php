<?php

$model = Mage::getModel('ezdefi_cryptocurrencypayment/currency');
$model->setData([
            'entity_id'                 => 1,
    'currency_id'        => '12321',
    'logo'               => 'aaaa.png',
    'symbol'             => 'pnds',
    'name'               => 'asdasd',
    'discount'           => 10,
    'payment_lifetime'   => 10,
    'wallet_address'     => 'asdasd',
    'block_confirmation' => 12,
    'decimal'            => 12,
    'description'        => 'asdsa',
    'currency_decimal'   => 122,
    'order'              => 1
]);
$model->save(); //save data