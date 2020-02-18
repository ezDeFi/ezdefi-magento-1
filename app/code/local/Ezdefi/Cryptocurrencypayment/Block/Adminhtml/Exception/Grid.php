<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('payment_id');
        $this->setId('cryptocurrencypayment_exception_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'ezdefi_cryptocurrencypayment/exception_collection';
    }

    protected function _prepareCollection()
    {
        $this->getCurrencyOption();
        $orders     = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->joinLeft(
            array(
                'od' => $orders
            ),
            'od.entity_id = main_table.order_id',
            array(
                'od.status', 'od.increment_id', 'od.customer_email'
            ))->order('id DESC');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    public function getRowClickCallback()
    {
        return 'fakeJsFunction';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('currency', array(
            'header'   => 'Currency',
            'sortable' => true,
            'width'    => '60',
            'index'    => 'currency',
            'type'     => 'options',
            'options'  => $this->getCurrencyOption(),
            'index'    => 'currency'
        ));

        $this->addColumn('amount_id', array(
            'header'   => 'Amount Id',
            'sortable' => false,
            'width'    => '60',
            'type'     => 'text',
            'index'                     => 'amount_id',
            'renderer'                  => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_Amount',
            'filter_condition_callback' => array($this, '_filterAmountIdConditionCallback')
        ));
        $this->addColumn('customer_email', array(
            'header'   => 'Customer email',
            'sortable' => true,
            'width'    => '60',
            'index'    => 'customer_email'
        ));
        $this->addColumn('increment_id', array(
            'header'   => 'Order',
            'sortable' => true,
            'width'    => '60',
            'renderer' => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_Order',
            'index'    => 'increment_id'
        ));
        $this->addColumn('action',
            array(
                'header'    => 'Action',
                'width'     => '100',
                'type'      => 'action',
                'renderer' => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_Action',
                'index'    => 'action',
                'filter'    => false,
                'sortable'  => false,
            ));

        return parent::_prepareColumns();
    }

    private function getCurrencyOption() {
        $result = [];
        $currencies = Mage::getModel('ezdefi_cryptocurrencypayment/currency')
            ->getCollection()->setOrder('`order`', 'ASC')->getData();

        foreach ($currencies as $currency) {
            $result[$currency['symbol']] = strtoupper($currency['symbol']);
        }

        return $result;
    }

    public function _filterAmountIdConditionCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()
            ->getSelect()
            ->reset(Zend_Db_Select::ORDER)
            ->where("main_table.amount_id LIKE '" . $value . "%'")->order(['amount_id ASC', 'id DESC']);

        return $this;
    }
}