<?php
class Ezdefi_Cryptocurrencypayment_Model_Resource_Amount_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    const MAX_AMOUNT_DECIMAL = 30;
    const MIN_SECOND_REUSE = 10;

    public function _construct()
    {
        $this->_init('ezdefi_cryptocurrencypayment/amount');
    }

    public function createAmountId($currency, $amount, $expiration, $decimal, $variation){
        if($expiration == 0) {
            $expiration = 86400;
        }
        $amount = round($amount, $decimal);

        $table = $this->getMainTable();
        $connection = $this->getConnection();

        $oldAmount = $connection->fetchAll("SELECT `tag_amount`, `id`
                                            FROM ".$table." 
                                            WHERE `currency`='".$currency."' 
                                                AND `amount`='".$amount."' 
                                                AND `expiration` < DATE_SUB(NOW(), INTERVAL ".self::MIN_SECOND_REUSE." SECOND)
                                                AND ( `decimal` = ".(int)$decimal." OR `temp` = 0 )
                                            ORDER BY `temp`
                                            LIMIT 1;");

        if (isset($oldAmount[0])) {
            $connection->query("UPDATE `".$table."` SET `expiration`= DATE_ADD(NOW(), INTERVAL " . $expiration . " SECOND)  WHERE `id`=" . $oldAmount[0]['id']);
            return $oldAmount[0]['tag_amount'];
        } else {
            $connection->query("START TRANSACTION;");

            $sql = "INSERT INTO ".$table." (`temp`, `amount`, `tag_amount`, `expiration`, `currency`, `decimal`)
                                SELECT (case when(MIN(t1.temp + 1) is null) then 0 else MIN(t1.temp + 1) end) as `temp`, " .$amount." as `amount`, 
                                    ".$amount." + (CASE WHEN(MIN(t1.temp + 1) is NULL) THEN 0 WHEN(MIN(t1.temp+1)%2 = 0) then MIN(t1.temp+1)/2 else -(MIN(t1.temp+1)+1)/2 end) * pow(10, -".$decimal.") as `tag_amount`,
                                     DATE_ADD(NOW(), INTERVAL ".$expiration." SECOND) as `expiration`,
                                      '".$currency. "' as `currency`, ".(int)$decimal." as `decimal`
                                FROM ".$table." t1
                                LEFT JOIN ".$table." t2 ON t1.temp + 1 = t2.temp and t1.amount = t2.amount and t1.currency = t2.currency and t1.decimal = t2.decimal
                                WHERE t2.temp IS NULL
                                    AND t1.decimal = ".$decimal."
                                    AND t1.currency = '".$currency."'
                                    AND t1.amount = ROUND(" .$amount.", ".self::MAX_AMOUNT_DECIMAL.")
                                ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), `expiration` = DATE_ADD(NOW(), INTERVAL ".$expiration." SECOND), `decimal` = ".$decimal.";";
            $connection->query($sql);

            $amountId = $connection->fetchOne("select tag_amount from ".$table." where `id` = LAST_INSERT_ID()");

            $connection->query("COMMIT;");

            $variationValue = abs($amountId - $amount);
            if ($variationValue > ($amount * (float)$variation) / 100 ) {
                return false;
            }
            return $amountId;
        }
    }
}