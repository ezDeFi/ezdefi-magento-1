<?php
class Ezdefi_Cryptocurrencypayment_IndexController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {
        $a = Mage::helper('cryptocurrencypayment/Calculator')->sum(2,3);
        echo 'Hello World: '.$a;
    }
}