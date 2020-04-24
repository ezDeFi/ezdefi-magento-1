<?php
CONST TIME_REMOVE_AMOUNT_ID = 3;
CONST TIME_REMOVE_EXCEPTION = 7;

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('ezdefi_cryptocurrencypayment/exception'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [
            'identity' => true,
            'nullable' => false,
            'primary'  => true,
            'unsigned' => true,
        ],
        'id'
    )
    ->addColumn(
        'payment_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        50,
        [
            'nullable' =>true,
            'default'  => null,
        ],
        'payment id'
    )
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        ['nullable' => true],
        'order id'
    )
    ->addColumn(
        'order_assigned',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [
            'nullable' => true,
            'default'  => null
        ],
        'order assigned id'
    )
    ->addColumn(
        'amount_id',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '60,30',
        ['nullable' => false],
        'amount id'
    )
    ->addColumn(
        'expiration',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false],
        'expiration'
    )->addColumn(
        'currency',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        50,
        [],
        'currency'
    )->addColumn(
        'paid',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        4,
        ['default' => 0],
        'paid status'
    )->addColumn(
        'has_amount',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        4,
        ['nullable' => false],
        '1: if payment use simple method, 0 if payment use ezdefi method'
    )->addColumn(
        'explorer_url',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        [
            'nullable' => true,
            'default' => null
        ],
        'explorer url'
    )->addColumn(
        'confirmed',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        2,
        ['default' => 0],
        '0: not confirm from exceptin, 1: be confirmed from exception'
    );
$installer->getConnection()->createTable($table);

$installer->run("
    CREATE EVENT  IF NOT EXISTS `ezdefi_remove_exception_event`
    ON SCHEDULE EVERY ".TIME_REMOVE_EXCEPTION." DAY
    STARTS DATE(NOW())
    DO
    DELETE FROM `{$installer->getTable('ezdefi_cryptocurrencypayment/exception')}` WHERE DATEDIFF( NOW( ) ,  expiration ) >= 5;
");

$installer->endSetup();