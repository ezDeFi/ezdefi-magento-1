<?php

class Ezdefi_Cryptocurrencypayment_Frontend_GatewayController extends Mage_Core_Controller_Front_Action
{
    public function getCoinsAction() {
        $requests = Mage::app()->getRequest()->getParams();
        $listCoin = Mage::helper('cryptocurrencypayment/GatewayApi')->getListToken($requests['keyword'],'ezdefi-magento1.lan');
        $this->getResponse()->setBody($listCoin);
    }

    public function checkApiKeyAction() {
        $requests = Mage::app()->getRequest()->getParams();
        $apiKeyStatus = Mage::helper('cryptocurrencypayment/GatewayApi')->checkApiKey($requests['api_key'], $requests['gateway_api_url']);;
        $this->getResponse()->setBody(json_encode($apiKeyStatus));
    }
}