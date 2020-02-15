<?php
class Ezdefi_Cryptocurrencypayment_Block_Payment_EzdefiMethod extends Mage_Core_Block_Template
{
    public function __construct(array $args)
    {
        $this->_data = $args['data'];
        $this->setTemplate('cryptocurrencypayment/payment/ezdefimethod.phtml');
    }

    public function getPaymentId()
    {
        return $this->_data['payment']->_id;
    }

    public function getOriginCurrency()
    {
        return $this->_data['payment']->originCurrency;
    }

    public function getOriginValue()
    {
        return __($this->_data['payment']->originValue);
    }

    public function getCryptoCurrency()
    {
        return __($this->_data['payment']->currency);
    }

    public function getCryptoValue()
    {
        return __($this->_data['payment']->value * pow(10, - $this->_data['payment']->decimal));
    }

    public function getQrCode() {
        return __($this->_data['payment']->qr);
    }

    public function getExpiration() {
        return __($this->_data['payment']->expiredTime);
    }
}
