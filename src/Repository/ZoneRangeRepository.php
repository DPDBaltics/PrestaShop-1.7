<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */


namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class ZoneRangeRepository extends AbstractEntityRepository
{
    public function findInZones(array $zoneIds, $countryId)
    {
        $zoneIds = $zoneIds ?: [0];

        $query = new DbQuery();
        $query->select(
            'dzr.id_dpd_zone_range, dzr.id_dpd_zone, dzr.id_country, dzr.include_all_zip_codes'
        );
        $query->select('dzr.zip_code_from, dzr.zip_code_to');
        $query->from('dpd_zone_range', 'dzr');
        $query->where('dzr.id_dpd_zone IN (' . implode(',', $zoneIds) . ')');
        $query->where('dzr.id_country = ' . (int)$countryId);

        $result = $this->db->executeS($query);

        return $result ? $result : [];
    }

    public function findAllZoneRangeCountryIds()
    {
        return \Db::getInstance()->executeS('
            SELECT DISTINCT `id_country`
            FROM `' . _DB_PREFIX_ . 'dpd_zone_range`
        ');
    }

    public function findBy(array $criteria, $limit = null, array $notEqualsCriteria = [])
    {
        $query = new DbQuery();
        $query->select(
            'dzr.id_dpd_zone_range, dzr.id_dpd_zone, dzr.id_country, dzr.include_all_zip_codes'
        );
        $query->select('dzr.zip_code_from, dzr.zip_code_to');
        $query->from('dpd_zone_range', 'dzr');

        foreach ($criteria as $field => $value) {
            $query->where('dzr.'.bqSQL($field).' = "'.pSQL($value).'"');
        }

        foreach ($notEqualsCriteria as $field => $value) {
            $query->where('dzr.'.bqSQL($field).' != "'.pSQL($value).'"');
        }

        if ($limit) {
            $query->limit((int) $limit);
        }

        $result = $this->db->executeS($query);

        return $result ? $result : [];
    }
}
