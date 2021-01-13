<?php


namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use PaymentModule;

class PaymentRepository extends AbstractEntityRepository
{
    public function updatePriceRulePayments($idPriceRule, array $paymentMethods, $checkAll = 0)
    {
        if (!$idPriceRule) {
            return;
        }

        if (empty($paymentMethods)) {
            return true;
        }

        if ($checkAll) {
            return $this->db->insert(
                'dpd_price_rule_payment',
                [
                    'id_dpd_price_rule' =>  (int) $idPriceRule,
                    'all_payments' => (int) $checkAll
                ]
            );
        }

        $counter = 0;
        foreach ($paymentMethods as $paymentMethod) {
            $result = $this->db->insert(
                'dpd_price_rule_payment',
                [
                    'id_dpd_price_rule' => (int)$idPriceRule,
                    'id_module' => (int)$paymentMethod
                ]
            );
            if (!$result) {
                $counter++;
            }
        }

        return ($counter) ? false : true;
    }

    public function removePriceRulePayments($idPriceRule)
    {
        $this->db->delete(
            'dpd_price_rule_payment',
            '`id_dpd_price_rule`=' . (int)$idPriceRule
        );
    }

    public function getAllPriceRulePaymentMethods($idPriceRule, $selectAll = false)
    {
        $paymentMethods = (array) PaymentModule::getInstalledPaymentModules();

        foreach ($paymentMethods as $key => $method) {
            $paymentMethods[$key]['selected'] = true;
        }

        if (empty($paymentMethods)) {
            return [];
        }

        if ($selectAll) {
            return $paymentMethods;
        }

        $query = new DbQuery();
        $query->select('c.`id_module`, c.`all_payments`');
        $query->from('dpd_price_rule_payment', 'c');
        $query->where('c.`id_dpd_price_rule`= "' . (int)$idPriceRule . '"');
        $resource = $this->db->query($query);
        $resultIds = [];
        while ($row = $this->db->nextRow($resource)) {
            if ($row['all_payments']) {
                return $paymentMethods;
            }
            $resultIds[] = $row['id_module'];
        }
        foreach ($paymentMethods as $key => $method) {
            if (!in_array($method['id_module'], $resultIds)) {
                $paymentMethods[$key]['selected'] = false;
            }
        }
        return $paymentMethods;
    }
}
