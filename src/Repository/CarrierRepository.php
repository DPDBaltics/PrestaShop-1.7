<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use PrestaShopDatabaseException;

class CarrierRepository extends AbstractEntityRepository
{
    /**
     * @param $zoneId
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getCarriersByDPDZoneId($zoneId)
    {
        $query = new DbQuery();
        $query->select('c.`id_carrier`, c.`name`');
        $query->from('carrier', 'c');
        $query->innerJoin('dpd_product', 'p', 'p.`id_reference` = c.`id_reference`');
        $query->innerJoin('dpd_product_zone', 'pz', 'pz.`id_dpd_product` = p.`id_dpd_product`');
        $query->where('pz.`id_dpd_zone` = "' . (int)$zoneId . '"');
        $query->where('c.`deleted` = 0');

        $result = $this->db->executeS($query);

        if (empty($result)) {
            return [];
        }

        return (array)$result;
    }

    public function getAllPriceRuleCarriers($idPriceRule, $idShop, $selectAll = false, $langId = null)
    {
        $carriers = (array)$this->getDpdCarriers($idShop, $langId);

        foreach ($carriers as $key => $carrier) {
            $carriers[$key]['selected'] = true;
        }

        if (empty($carriers)) {
            return [];
        }

        if ($selectAll) {
            return $carriers;
        }

        $query = new DbQuery();
        $query->select('c.`id_reference`, c.`all_carriers`');
        $query->from('dpd_price_rule_carrier', 'c');
        $query->where('c.`id_dpd_price_rule`="' . (int)$idPriceRule . '"');
        $resource = $this->db->query($query);
        $resultIds = [];
        while ($row = $this->db->nextRow($resource)) {
            if ($row['all_carriers']) {
                return $carriers;
            }
            $resultIds[] = $row['id_reference'];
        }
        foreach ($carriers as $key => $carrier) {
            if (!in_array($carrier['id_reference'], $resultIds)) {
                $carriers[$key]['selected'] = false;
            }
        }
        return $carriers;
    }

    public function getDpdCarriers($shopId = null, $langId = null)
    {
        $query = new DbQuery();
        $query->select('dc.`id_reference`, c.`name`');
        $query->from('dpd_product', 'dc');
        $query->innerJoin(
            'carrier',
            'c',
            'c.`id_reference` = dc.`id_reference` AND c.`deleted`="0"'
        );

        if ($langId) {
            $query->select('cl.`delay`');
            $query->leftJoin('carrier_lang', 'cl', 'cl.id_carrier = c.id_carrier');
            $query->where('cl.id_lang = ' . (int)$langId);
            $query->where('cl.id_shop = ' . (int)$shopId);
        }

        return $this->db->executeS($query);
    }

    public function findCarrierIdByName($name)
    {
        $q = new DbQuery();
        $q->select('c.id_carrier');
        $q->from('carrier', 'c');
        $q->leftJoin('carrier_shop', 'cs', 'cs.id_carrier = c.id_carrier');
        $q->where('c.name = "'.pSQL($name).'"');
        $q->where('c.`deleted`=0');
        $q->where('c.`external_module_name`="dpdbaltics"');

        $carrierId = $this->db->getValue($q);
        if (!$carrierId) {
            return null;
        }

        return (int) $carrierId;
    }
}
