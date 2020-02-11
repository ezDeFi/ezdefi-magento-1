<?php
class Ezdefi_Cryptocurrencypayment_Helper_Calculator extends Mage_Core_Helper_Abstract
{
    public function sum($a, $b){
        return $a + $b;
    }

    public function substraction($a, $b){
        return $a - $b;
    }

    public function multiplication($a, $b){
        return $a * $b;
    }

    public function division($a, $b){
        return $a / $b;
    }
}