<?php

class Ezdefi_Cryptocurrencypayment_Block_Payment_SimpleMethod extends Mage_Core_Block_Template
{
    public function __construct(array $args)
    {
        $this->_data = $args['data'];
        $this->setTemplate('cryptocurrencypayment/payment/ezdefimethod.phtml');
    }

    public function isError()
    {
        return !$this->_data['payment'];
    }

    public function getPaymentId()
    {
        return __($this->_data['payment']->_id);
    }

    public function getOriginCurrency()
    {
        return __($this->_data['originCurrency']);
    }

    public function getOriginValue()
    {
        return __($this->_data['originValue']);
    }

    public function getCryptoCurrency()
    {
        return __($this->_data['payment']->currency);
    }

    public function getCryptoValue()
    {
        return __($this->_data['payment']->originValue);
    }

    public function getGatewayQrCode()
    {
        return __($this->_data['payment']->qr);
    }

    public function getExpiration()
    {
        return __($this->_data['payment']->expiredTime);
    }

    public function getWalletAddress()
    {
        return __($this->_data['payment']->to);
    }

    public function getDeepLink()
    {
        return __($this->_data['payment']->deepLink);
    }
}
