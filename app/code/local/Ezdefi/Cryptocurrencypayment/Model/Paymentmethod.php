<?php

class Ezdefi_Cryptocurrencypayment_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code          = 'ezdefi_cryptocurrencypayment';
    protected $_formBlockType = 'cryptocurrencypayment/form_cryptocurrencypayment';
    protected $_infoBlockType = 'cryptocurrencypayment/info_cryptocurrencypayment';

    public function assignData($data)
    {
        $info = $this->getInfoInstance();

        if ($data->getCryptoCurrencyId()) {
            $info->setCryptoCurrencyId($data->getCryptoCurrencyId());
        }

        return $this;
    }

    public function validate()
    {
        parent::validate();
        $info = $this->getInfoInstance();

        if (!$info->getCryptoCurrencyId())
        {
            $errorMsg = $this->_getHelper()->__("Please specify coin.");
        }

        if ($errorMsg)
        {
            Mage::throwException($errorMsg);
        }
        Mage::getSingleton('checkout/session')->setCryptoCurrencyId($info->getCryptoCurrencyId());

        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('ezdefi_frontend/payment/content', array('_secure' => false));
    }
}