<?php


class Ezdefi_Cryptocurrencypayment_Adminhtml_ExceptionController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admingrid/adgrid');
    }


    public function indexAction()
    {

//        $exceptions = Mage::getModel('sales/order')->getCollection()->getData();
//        echo "<pre>";
//        print_r($exceptions);die;

        $this->loadLayout();
        $this->_title($this->__("Admin Grid"));
        $this->renderLayout();
    }

    public function deleteAction()
    {
        var_dump(1);die;
        $requests = Mage::app()->getRequest()->getParams();

        $exceptionId = $requests['exception_id'];
        Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->load($exceptionId)
            ->delete();

        $this->_redirect("*/*/");
    }

    public function confirmAction()
    {
        $requests = Mage::app()->getRequest()->getParams();

        $exceptionId = $requests['exception_id'];
        $exception   = Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->load($exceptionId);

        $orderId = $exception->getOrderId();
        $this->setStatusForOrder($orderId, 'processing', 'processing');

        $exceptionsToDelete = Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->getCollection()
            ->addFieldToFilter('order_id', $orderId);
        foreach ($exceptionsToDelete as $exceptionToDelete) {
            $exceptionToDelete->delete();
        }

        $this->_redirect("*/*/");
    }

    public function revertAction()
    {
        $requests    = Mage::app()->getRequest()->getParams();
        $exceptionId = $requests['exception_id'];
        $exception   = Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->load($exceptionId);

        // set order status to pendding
        $orderId = $exception->getOrderId();
        $this->setStatusForOrder($orderId, 'new', 'pending');

        // set exception to unknown transaction
        $exception->setOrderId(null);
        $exception->setPaid(3);
        $exception->save();

        $this->_redirect("*/*/");
    }

    public function assignAction()
    {
        $requests        = Mage::app()->getRequest()->getParams();
        $exceptionId     = $requests['exception_id'];
        $orderIdToAssign = $requests['order_id'];

        $exception = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->load($exceptionId);
        $this->setStatusForOrder($orderIdToAssign, 'processing', 'processing');


        //delete exception with order_id = exception.order_id
        $orderIdTodelete = $exception->getData()['order_id'];
        if ($orderIdTodelete) {
            $exceptionsToDelete = Mage::getModel('ezdefi_cryptocurrencypayment/exception')
                ->getCollection()
                ->addFieldToFilter('order_id', $orderIdTodelete);
            foreach ($exceptionsToDelete as $exceptionToDelete) {
                $exceptionToDelete->delete();
            }
        }

        //delete exception with id = 'exception_id'
        $exception->delete();

        $this->_redirect("*/*/");
    }

    public function getOrderPendingAction()
    {
        $requests = Mage::app()->getRequest()->getParams();
        $keyword  = $requests['keyword'];

        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToSelect(['id' => 'entity_id'])
            ->addAttributeToSelect('customer_email')
            ->addAttributeToSelect('customer_firstname')
            ->addAttributeToSelect('customer_lastname')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('order_currency_code')
            ->addFieldToFilter('status', 'pending')
            ->addFieldToFilter([
                'customer_email',
                'increment_id',
                'customer_lastname',
                'customer_firstname'
            ], [
                ['like' => '%' . $keyword . '%'],
                ['like' => '%' . $keyword . '%'],
                ['like' => '%' . $keyword . '%'],
                ['like' => '%' . $keyword . '%']
            ])
            ->getData();
        $this->getResponse()->setBody(json_encode(['data' => $orders, 'status' => 'success']));
    }

    private function setStatusForOrder($orderId, $state, $status)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        $order->setData('state', $state);
        $order->setStatus($status);
        $history = $order->addStatusHistoryComment('Order was set to ' . $status . ' by Ezdefi Exception management.', false);
        $history->setIsCustomerNotified(true);
        $order->save();
    }
}