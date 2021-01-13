<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use PrestaShopDatabaseException;

/**
 * Class DPDZoneRepository
 */
class DPDZoneRepository extends AbstractEntityRepository
{
    /**
     * @param $name
     * @param $shopId
     * @param $idWsUser
     * @param array $exceptions
     *
     * @return false|string|null
     */
    public function getByName($name, $exceptions = [])
    {
        $query = new DbQuery();
        $query->select('dz.`id_dpd_zone`');
        $query->from('dpd_zone', 'dz');

        $query->where('`name` = "'.pSQL($name).'"');

        if ($exceptions) {
            $query->where(
                //pSQL for prestashop's validator
                'id_dpd_zone NOT IN ('.pSQL(implode(',', array_map('intval', $exceptions))).')'
            );
        }

        return $this->db->getValue($query);
    }

    public function getId($idShop)
    {
        $query = new DbQuery();
        $query->select('id_dpd_zone');
        $query->from('dpd_zone');
        $query->where('`id_shop`="'.(int) $idShop.'"');
        return $this->db->getValue($query);
    }

    /**
     * Find all zones ids in given shop
     *
     * @param int $idShop
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    public function findAllIds()
    {
        $query = new DbQuery();
        $query->select('dz.id_dpd_zone');
        $query->from('dpd_zone', 'dz');


        $result = $this->db->query($query);

        $zones = [];

        while ($row = $this->db->nextRow($result)) {
            $zones[] = (int) $row['id_dpd_zone'];
        }

        return $zones;
    }

    public function updatePriceRuleZones($idPriceRule, array $zones, $allElementsSelected)
    {
        if (!$idPriceRule) {
            return;
        }

        if ($allElementsSelected) {
            return $this->db->insert(
                'dpd_price_rule_zone',
                [
                    'id_dpd_price_rule' => (int)$idPriceRule,
                    'id_dpd_zone' => 0,
                    'all_zones' => 1
                ]
            );
        }

        if (empty($zones)) {
            return true;
        }

        $counter = 0;
        foreach ($zones as $zoneId) {
            $result = $this->db->insert(
                'dpd_price_rule_zone',
                [
                    'id_dpd_price_rule' => (int) $idPriceRule,
                    'id_dpd_zone' => (int) $zoneId,
                    'all_zones' => 0
                ]
            );
            if (!$result) {
                $counter++;
            }
        }
        return $counter ? false : true;
    }

    public function removePriceRuleZones($idPriceRule)
    {
        $this->db->delete(
            'dpd_price_rule_zone',
            '`id_dpd_price_rule`=' . (int)$idPriceRule
        );
    }

    public function getSelectedPriceRuleZones($idPriceRule)
    {
        $query = new DbQuery();
        $query->select('zl.id_dpd_zone as id, zl.`name`, prz.`all_zones` AS `all_selected_flag`, IF(ISNULL(prz.id_dpd_zone), 0, 1) `selected`');
        $query->from('dpd_zone', 'zl');
        $query->leftJoin(
            'dpd_price_rule_zone',
            'prz',
            'prz.id_dpd_price_rule = ' . (int)$idPriceRule .
            ' AND (zl.id_dpd_zone = prz.id_dpd_zone OR prz.all_zones = 1)'
        );
        $query->groupBy('zl.id_dpd_zone');

        return $this->db->executeS($query);
    }

    public function getZonesIdsByPriceRule($idPriceRule)
    {
        $query = new DbQuery();
        $query->select('zl.id_dpd_zone as id');
        $query->from('dpd_price_rule_zone', 'z');
        $query->innerJoin(
            'dpd_price_rule_shop',
            'zs',
            'zs.`id_dpd_price_rule`=z.`id_dpd_price_rule`'
        );
        $query->leftJoin(
            'dpd_zone',
            'zl',
            '(zl.`id_dpd_zone`=z.`id_dpd_zone` OR z.`all_zones`)'
        );
        $query->where('z.`id_dpd_price_rule`="' . (int)$idPriceRule . '"');
        return $this->db->executeS($query);
    }
}
