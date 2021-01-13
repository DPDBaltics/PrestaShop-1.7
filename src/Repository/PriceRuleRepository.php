<?php

namespace Invertus\dpdBaltics\Repository;

use Address;
use Db;
use DbQuery;
use DPDPriceRule;
use mysqli_result;
use PaymentModule;
use PDOStatement;
use PrestaShopDatabaseException;
use Validate;

class PriceRuleRepository extends AbstractEntityRepository
{

    /** returns value if all elements are selected
     * @param $tableName - table name without prefix. Should be only dpd_price_rule_carrier or dpd_price_rule_payment
     * @param $idPriceRule
     * @param $idShop
     * @return bool|false|null|string
     */
    public function getAllFlag($tableName, $idPriceRule)
    {
        if (!in_array($tableName, ['dpd_price_rule_carrier', 'dpd_price_rule_payment'])) {
            return false;
        }
        $flag = '';

        if ($tableName == 'dpd_price_rule_carrier') {
            $flag = 'all_carriers';
        }

        if ($tableName == 'dpd_price_rule_payment') {
            $flag = 'all_payments';
        }

        $query = new DbQuery();
        $query->select('c.`' . pSQL($flag) . '`');
        $query->from(pSQL($tableName), 'c');
        $query->innerJoin(
            'dpd_price_rule_shop',
            'cs',
            'cs.`id_dpd_price_rule`=c.`id_dpd_price_rule`'
        );
        $query->where('c.`id_dpd_price_rule`="' . (int)$idPriceRule . '"');
        return $this->db->getValue($query);
    }

    /**
     * Get price rules by carrier reference ID and by delivery address types
     * which checks if customer address is regular or company for current shop
     *
     * @param Address $deliveryAddress
     * @param int $carrierReferenceId
     * @return array price rules IDs
     * @throws PrestaShopDatabaseException
     */
    public function getByCarrierReference(
        Address $deliveryAddress,
        $carrierReference
    ) {
        $query = new DbQuery();
        $query->select('prc.`id_dpd_price_rule`');
        $query->from('dpd_price_rule_carrier', 'prc');
        $query->innerJoin('dpd_price_rule', 'pr', 'pr.id_dpd_price_rule = prc.id_dpd_price_rule');
        $query->innerJoin(
            'dpd_price_rule_shop',
            'prs',
            'prs.`id_dpd_price_rule` = prc.`id_dpd_price_rule`'
        );
        $query->where('prc.`id_reference`="' . (int)$carrierReference . '" OR prc.`all_carriers`="1"');
        $query->where('pr.active = 1');
        $query->orderBy('pr.position ASC');


        if (Validate::isLoadedObject($deliveryAddress)) {
            if ($deliveryAddress->company) {
                $query->where(
                    'pr.`customer_type` IN ("' .
                    pSQL(DPDPriceRule::CUSTOMER_TYPE_ALL) .
                    '","' . pSQL(DPDPriceRule::CUSTOMER_TYPE_COMPANY) . '")'
                );
            } else {
                $query->where(
                    'pr.`customer_type` IN ("' .
                    pSQL(DPDPriceRule::CUSTOMER_TYPE_ALL) .
                    '","' . pSQL(DPDPriceRule::CUSTOMER_TYPE_REGULAR) . '")'
                );
            }
        }

        $resource = $this->db->query($query);
        $result = [];

        while ($row = $this->db->nextRow($resource)) {
            $result[] = $row['id_dpd_price_rule'];
        }

        return $result;
    }

    /**
     * @param $table
     * @param $idPriceRule
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getPriceRuleTableData($table, $idPriceRule)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from($table);

        $query->where('`id_dpd_price_rule`=' . (int)$idPriceRule);

        $resource = $this->db->query($query);

        $result = [];

        while ($row = $this->db->nextRow($resource)) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param $table
     * @param $priceRuleTableData
     * @param $newPriceRuleId
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public function addPriceRuleTableData($table, $priceRuleTableData, $newPriceRuleId)
    {
        foreach ($priceRuleTableData as $priceRuleTableDataRow) {
            $priceRuleTableDataRow['id_dpd_price_rule'] = $newPriceRuleId;

            if (!Db::getInstance()->insert($table, $priceRuleTableDataRow)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return false|string|null
     */
    public function getLowestPosition()
    {
        $query = new DbQuery();
        $query->select('MAX(position)');
        $query->from('dpd_price_rule', 'pr');

        return $this->db->getValue($query);
    }

    /**
     * @param $idZone
     * @return array|false|mysqli_result|PDOStatement|resource|null
     * @throws PrestaShopDatabaseException
     */
    public function getPriceRulesByIdZone($idZone)
    {
        $query = new DbQuery();
        $query->select('prz.`id_dpd_price_rule`, pr.`name`');
        $query->from('dpd_price_rule_zone', 'prz');
        $query->innerJoin('dpd_price_rule', 'pr', 'pr.`id_dpd_price_rule` = prz.`id_dpd_price_rule`');
        $query->where('prz.`id_dpd_zone`="' . (int)$idZone . '"');
        $query->groupBy('prz.`id_dpd_price_rule`');
        return $this->db->executeS($query);
    }

    public function findAllPriceRuleIds()
    {
        $query = new DbQuery();
        $query->select('pr.id_dpd_price_rule');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_shop', 'prs', 'pr.id_dpd_price_rule = prs.id_dpd_price_rule');
        $query->groupBy('pr.id_dpd_price_rule');
        $ids = [];
        $result = $this->db->query($query);

        while ($row = $this->db->nextRow($result)) {
            $ids[] = (int)$row['id_dpd_price_rule'];
        }

        return $ids;
    }

    /**
     * Find whether price rule has all carriers assigned to it or not
     *
     * @param int $priceRuleId
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function findIsAllCarriersAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('pr.id_dpd_price_rule');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_carrier', 'prc', 'pr.id_dpd_price_rule = prc.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId . ' OR prc.all_carriers = 1');

        $result = $this->db->executeS($query);
        if (!is_array($result) || empty($result)) {
            return false;
        }

        return true;
    }

    /**
     * Find all carriers assigned to given price rule
     *
     * @param int $priceRuleId
     *
     * @return array|int[]  Array of carrier references
     * @throws PrestaShopDatabaseException
     */
    public function findAllCarriersAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('prc.id_reference');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_carrier', 'prc', 'pr.id_dpd_price_rule = prc.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId);

        $ids = [];
        $result = $this->db->query($query);

        while ($row = $this->db->nextRow($result)) {
            $idReference = (int)$row['id_reference'];
            if (!$idReference) {
                continue;
            }

            $ids[] = (int)$idReference;
        }

        return $ids;
    }

    /**
     * Find whether price rule has all carriers assigned to it or not
     *
     * @param int $priceRuleId
     *
     * @return bool
     */
    public function findIsAllZonesAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('pr.id_dpd_price_rule');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_zone', 'prz', 'pr.id_dpd_price_rule = prz.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId);
        $query->where('prz.all_zones = 1');

        $result = $this->db->executeS($query);
        if (!is_array($result) || empty($result)) {
            return false;
        }

        return true;
    }

    /**
     * Find all zones assigned to given price rule
     *
     * @param int $priceRuleId
     *
     * @return array|string[]  Array of zone ids
     */
    public function findAllZonesAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('prz.id_dpd_zone');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_zone', 'prz', 'pr.id_dpd_price_rule = prz.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId);

        $ids = [];
        $result = $this->db->query($query);

        while ($row = $this->db->nextRow($result)) {
            $idZone = (int)$row['id_dpd_zone'];
            if (!$idZone) {
                continue;
            }

            $ids[] = (int)$idZone;
        }

        return $ids;
    }

    public function findIsAllShopsAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('pr.id_dpd_price_rule');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_shop', 'prz', 'pr.id_dpd_price_rule = prz.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId);
        $query->where('prz.all_shops = 1');

        $result = $this->db->executeS($query);
        if (!is_array($result) || empty($result)) {
            return false;
        }

        return true;
    }

    public function findAllShopsAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('prz.id_shop');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_shop', 'prz', 'pr.id_dpd_price_rule = prz.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId);

        $ids = [];
        $result = $this->db->query($query);

        while ($row = $this->db->nextRow($result)) {
            $idShop = (int)$row['id_shop'];
            if (!$idShop) {
                continue;
            }

            $ids[] = (int)$idShop;
        }

        return $ids;
    }

    /**
     * Find whether price rule has all payments assigned to it or not
     *
     * @param int $priceRuleId
     *
     * @return bool
     */
    public function findIsAllPaymentsAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('pr.id_dpd_price_rule');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_payment', 'prp', 'pr.id_dpd_price_rule = prp.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId);
        $query->where('prp.all_payments = 1');

        $result = $this->db->executeS($query);
        if (!is_array($result) || empty($result)) {
            return false;
        }

        return true;
    }

    /**
     * Find all payments assigned to given price rule
     *
     * @param int $priceRuleId
     *
     * @return array|string[]  Array of payment ids
     */
    public function findAllPaymentsAssigned($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('prp.id_module');
        $query->from('dpd_price_rule', 'pr');
        $query->leftJoin('dpd_price_rule_payment', 'prp', 'pr.id_dpd_price_rule = prp.id_dpd_price_rule');
        $query->where('pr.id_dpd_price_rule = ' . (int)$priceRuleId);

        $ids = [];
        $result = $this->db->query($query);

        while ($row = $this->db->nextRow($result)) {
            $moduleId = (int)$row['id_module'];
            if (!$moduleId) {
                continue;
            }

            $ids[] = (int)$moduleId;
        }

        return $ids;
    }

    /**
     * @param $zone
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function addPriceRuleZone(array $zone)
    {
        return $this->db->insert('dpd_price_rule_zone', $zone);
    }


    /**
     * @param array $shop
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function addPriceRuleShop(array $shop)
    {
        return $this->db->insert('dpd_price_rule_shop', $shop);
    }


    /**
     * @param array $payment
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function addPriceRulePayment(array $payment)
    {
        return $this->db->insert('dpd_price_rule_payment', $payment);
    }

    /**
     * @param array $carrier
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function addPriceRuleCarrier(array $carrier)
    {
        return $this->db->insert('dpd_price_rule_carrier', $carrier);
    }

    public function deleteOldData()
    {
        $this->db->delete('dpd_price_rule');
        $this->db->delete('dpd_price_rule_carrier');
        $this->db->delete('dpd_price_rule_payment');
        $this->db->delete('dpd_price_rule_zone');
    }

    public function deletePriceRuleShops($priceRuleId)
    {
        $this->db->delete('dpd_price_rule_shop', 'id_dpd_price_rule = ' . (int) $priceRuleId);
    }

    public function isAvailableInShop($priceRuleId, $shopId)
    {
        $query = new DbQuery();
        $query->select('id_dpd_price_rule');
        $query->from('dpd_price_rule_shop');
        $query->where('id_dpd_price_rule = ' . (int)$priceRuleId);
        $query->where('(id_shop = ' . (int)$shopId . ' OR all_shops = 1)');

        return $this->db->executeS($query);
    }

    /**
     * Returns payment modules IDs, that are allowed to use by this price rule
     *
     * @param $priceRuleId
     * @param $shopId
     *
     * @return array|string
     * @throws PrestaShopDatabaseException
     */
    public function getAllowedPaymentMethodsIds($priceRuleId)
    {
        $query = new DbQuery();
        $query->select('c.`id_module`, c.`all_payments`');
        $query->from('dpd_price_rule_payment', 'c');
        $query->where('c.`id_dpd_price_rule`= "'.(int) $priceRuleId.'"');
        $resource = $this->db->query($query);
        $result = array();

        while ($row = $this->db->nextRow($resource)) {
            if ($row['all_payments']) {
                return 'all';
            }

            $result[] = $row['id_module'];
        }

        return $result;
    }

}
