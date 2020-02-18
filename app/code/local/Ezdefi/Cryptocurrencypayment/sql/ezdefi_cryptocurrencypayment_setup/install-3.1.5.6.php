<?php
CONST TIME_REMOVE_AMOUNT_ID = 3;
CONST TIME_REMOVE_EXCEPTION = 7;

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('ezdefi_cryptocurrencypayment/currency'))
    ->addColumn(
        'entity_id',
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
        'currency_id',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        50,
        [
            'nullable' => false,
        ],
        'currency\'s id'
    )
    ->addColumn(
        'order',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [
            'nullable' => false,
            'default'  => 0
        ],
        'order factor, the factor to sort currency'
    )
    ->addColumn(
        'logo',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'logo'
    )
    ->addColumn(
        'symbol',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'symbol'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'name'
    )->addColumn(
        'discount',
        Varien_Db_Ddl_Table::TYPE_FLOAT,
        '5,2',
        [],
        'discount'
    )->addColumn(
        'payment_lifetime',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [],
        'payment lifetime'
    )
    ->addColumn(
        'wallet_address',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'wallet address'
    )
    ->addColumn(
        'block_confirmation',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [],
        'block confirmation'
    )->addColumn(
        'decimal',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [],
        'decimal, to create amount id'
    )->addColumn(
        'currency_decimal',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [],
        'currency decimal'
    )->addColumn(
        'description',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        ['default' => ''],
        'description'
    );
$installer->getConnection()->createTable($table);

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
    );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('ezdefi_cryptocurrencypayment/amount'))
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
        'temp',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        [
            'nullable' => false,
        ],
        'temp'
    )
    ->addColumn(
        'amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '60,30',
        ['nullable' => false],
        'amount'
    )
    ->addColumn(
        'tag_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '60,30',
        ['nullable' => false],
        'tag_amount'
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
        'decimal',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        5,
        ['nullable' => false],
        'decimal'
    );
$installer->getConnection()->createTable($table);

$installer->run("
    CREATE EVENT IF NOT EXISTS `ezdefi_remove_amount_id_event`
    ON SCHEDULE EVERY ".TIME_REMOVE_AMOUNT_ID." DAY
    STARTS DATE(NOW())
    DO
    DELETE FROM `{$installer->getTable('ezdefi_cryptocurrencypayment/amount')}` WHERE DATEDIFF( NOW( ) ,  expiration ) >= 86400;
");

$installer->run("
    CREATE EVENT  IF NOT EXISTS `ezdefi_remove_exception_event`
    ON SCHEDULE EVERY ".TIME_REMOVE_EXCEPTION." DAY
    STARTS DATE(NOW())
    DO
    DELETE FROM `{$installer->getTable('ezdefi_cryptocurrencypayment/exception')}` WHERE DATEDIFF( NOW( ) ,  expiration ) >= 86400;
");

$installer->endSetup();