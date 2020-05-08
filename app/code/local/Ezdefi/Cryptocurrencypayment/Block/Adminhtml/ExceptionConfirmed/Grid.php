<?php

class Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
                'email'        => 'od.customer_email',
                'customer'     => 'CONCAT(od.customer_firstname, " ", od.customer_lastname)',
                'total'        => 'od.grand_total',
                'date'         => 'od.created_at',
                'increment_id' => 'od.increment_id'
            ))
            ->join(
                array(
                    'new_order' => $orders
                ),
                'new_order.entity_id = main_table.order_assigned',
                array(
                    'new_email'        => 'new_order.customer_email',
                    'new_customer'     => 'CONCAT(new_order.customer_firstname, " ", new_order.customer_lastname)',
                    'new_total'        => 'new_order.grand_total',
                    'new_date'         => 'new_order.created_at',
                    'new_increment_id' => 'new_order.increment_id'
                ))
            ->where('main_table.confirmed = 1 AND main_table.order_assigned is NOT NULL')
            ->order('id DESC');

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
            'width'    => '30px',
            'index'    => 'currency',
            'type'     => 'text',
            'options'  => $this->getCurrencyOption(),
        ));

        $this->addColumn('amount_id', array(
            'header'                    => 'Amount Id',
            'sortable'                  => false,
            'width'                     => '70px',
            'type'                      => 'text',
            'index'                     => 'amount_id',
            'renderer'                  => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_Exception_Column_Amount',
            'filter_condition_callback' => array($this, '_filterAmountIdConditionCallback')
        ));

        $this->addColumn('new_increment_id', array(
            'header'   => 'Order',
            'sortable' => true,
            'width'    => '60',
            'renderer' => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Column_OrderAssigned',
            'index'    => 'new_increment_id',
            'filter_condition_callback' => array($this, '_filterNewIncrementIdConditionCallback')
        ));

        $this->addColumn('increment_id', array(
            'header'                    => 'Old Order',
            'sortable'                  => true,
            'width'                     => '60',
            'renderer'                  => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Column_Order',
            'index'                     => 'increment_id',
            'filter_condition_callback' => array($this, '_filterIncrementIdConditionCallback')
        ));

        $this->addColumn('payment_id', array(
            'header'   => 'Payment Info',
            'width'    => '60',
            'index'    => 'payment_id',
            'renderer' => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Column_PaymentInformation',
            'sortable' => false,
            'filter'   => false,
        ));

        $this->addColumn('action',
            array(
                'header'   => 'Action',
                'width'    => '50',
                'type'     => 'action',
                'renderer' => 'Ezdefi_Cryptocurrencypayment_Block_Adminhtml_ExceptionConfirmed_Column_Action',
                'index'    => 'action',
                'filter'   => false,
                'sortable' => false,
            ));

        return parent::_prepareColumns();
    }

    private function getCurrencyOption()
    {
        $result     = [];
        $currencies = Mage::helper('cryptocurrencypayment/GatewayApi')->getCurrencies();

        foreach ($currencies as $currency) {
            $result[$currency['token']['symbol']] = strtoupper($currency['token']['symbol']);
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

    public function _filterIncrementIdConditionCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()
            ->getSelect()
            ->where("od.increment_id LIKE '%" . $value . "%'");

        return $this;
    }

    public function _filterNewIncrementIdConditionCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()
            ->getSelect()
            ->where("new_order.increment_id LIKE '%" . $value . "%'");

        return $this;
    }

}