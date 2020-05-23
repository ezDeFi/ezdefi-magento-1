<?php


class Ezdefi_Cryptocurrencypayment_Adminhtml_ExceptionController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admingrid/adgrid');
    }


    public function indexAction()
    {
        $lastTimeDelete =  Mage::getStoreConfig('ezdefi_cron/last_time_delete');

        if(time() - (int)$lastTimeDelete > 86400 * 7) {
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $table = $resource->getTableName('ezdefi_cryptocurrencypayment/exception');
            $query =  "DELETE FROM {$table} WHERE DATEDIFF( NOW( ) ,  expiration ) >= 5";
            $writeConnection->query($query);

            Mage::getModel('core/config')->saveConfig('ezdefi_cron/last_time_delete', time());
        }

        $this->loadLayout();
        $this->_title($this->__("Admin Grid"));
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $requests = Mage::app()->getRequest()->getParams();

        $exceptionId = $requests['exception_id'];
        Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->load($exceptionId)
            ->delete();

        $this->_redirect("*/*/");
    }

    public function assignAction()
    {
        $requests        = Mage::app()->getRequest()->getParams();
        $exceptionId     = $requests['exception_id'];
        $orderIdToAssign = $requests['order_id'];

        $exception = Mage::getModel('ezdefi_cryptocurrencypayment/exception')->load($exceptionId);
        $exception->setData('order_assigned', $orderIdToAssign);
        $exception->setData('confirmed', 1);
        $exception->save();

        if($exception['order_id']  && $orderIdToAssign  != $exception['order_id']) {
            $this->setStatusForOrder($exception['order_id'], 'pending', 'pending');
        }
        $this->setStatusForOrder($orderIdToAssign, 'processing', 'processing');

        //hide exception with order_id = exception.order_assigned
        $exceptionsToDelete = Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->getCollection()
            ->addFieldToFilter('order_id', $orderIdToAssign);
        foreach ($exceptionsToDelete as $exceptionToDelete) {
            $exceptionToDelete->setData('confirmed', 2);
            $exceptionToDelete->save();
        }

        $this->_redirect("*/*/");
    }

    public function confirmAction()
    {
        $requests = Mage::app()->getRequest()->getParams();

        $exceptionId = $requests['exception_id'];
        $exception   = Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->load($exceptionId);

        $exception->setData('order_assigned',$exception['order_id']);
        $exception->save();

        $orderId = $exception->getOrderId();
        $this->setStatusForOrder($orderId, 'processing', 'processing');

        $exceptionsToUpdate = Mage::getModel('ezdefi_cryptocurrencypayment/exception')
            ->getCollection()
            ->addFieldToFilter('order_id', $orderId);
        foreach ($exceptionsToUpdate as $exceptionToUpdate) {
            $exceptionToUpdate->setData('confirmed', 1);
            $exceptionToUpdate->save();
        }

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