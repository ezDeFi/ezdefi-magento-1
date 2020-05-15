<?php

class Ezdefi_Cryptocurrencypayment_Frontend_GatewayController extends Mage_Core_Controller_Front_Action
{
    public function checkApiKeyAction()
    {
        $requests     = Mage::app()->getRequest()->getParams();
        $apiKeyStatus = Mage::helper('cryptocurrencypayment/GatewayApi')->checkApiKey($requests['api_key'], $requests['gateway_api_url']);
        $this->getResponse()->setBody(json_encode($apiKeyStatus));
    }

    public function checkPublicKeyAction()
    {
        $requests        = Mage::app()->getRequest()->getParams();
        $publicKeyStatus = Mage::helper('cryptocurrencypayment/GatewayApi')->checkPublicKey($requests['public_key'], $requests['api_key'], $requests['gateway_api_url']);

        if($publicKeyStatus == 'true') {
            var_dump(1);
            Mage::helper('cryptocurrencypayment/GatewayApi')->updateCallbackUrl('ezdefi_frontend/callback/confirmorder', $requests['public_key'], $requests['api_key'], $requests['gateway_api_url']);
        }
        $this->getResponse()->setBody(json_encode($publicKeyStatus));
    }
}