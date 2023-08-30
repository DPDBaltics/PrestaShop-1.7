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

use Address;
use Country;
use DbQuery;
use PrestaShopDatabaseException;

class ZoneRepository extends AbstractEntityRepository
{
    /**
     * Find all zones for given carrier
     *
     * @param $idReference
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function findZonesIdsByCarrierReference($idReference)
    {
        $query = new DbQuery();
        $query->select('z.id_dpd_zone AS id, z.name, p.all_zones');
        $query->from('dpd_product', 'p');
        $query->leftJoin('dpd_product_zone', 'pz', 'pz.`id_dpd_product` = p.`id_dpd_product`');
        $query->leftJoin('dpd_zone', 'z', 'z.id_dpd_zone = pz.id_dpd_zone OR p.all_zones = 1');
        $query->where('p.id_reference = ' . (int) $idReference);

        return $this->db->executeS($query);
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function findAllZonesIds()
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

    public function removeZonesByProductId($productId)
    {
        return $this->db->delete(
            'dpd_product_zone',
            'id_dpd_product = ' . (int)$productId
        );
    }

    /**
     * @param $name
     * @return false|string|null
     */
    public function getByName($name)
    {
        $query = new DbQuery();
        $query->select('`id_dpd_zone`');
        $query->from('dpd_zone');

        $query->where('`name` = "'.pSQL($name).'"');

        return $this->db->getValue($query);
    }

    public function addProductZonesFromArray(array $zones)
    {
        foreach ($zones as $zone) {
            $this->db->insert('dpd_product_zone', $zone);
        }
    }

    public function findAddressInZones(Address $address)
    {
        $idCountry = $address->id_country ?: (int)\Configuration::get('PS_COUNTRY_DEFAULT');
        $zipCode = $address->postcode;

        $query = new DbQuery();
        $query->select('dz.id_dpd_zone');
        $query->from('dpd_zone', 'dz');
        $query->leftJoin('dpd_zone_range', 'dzr', 'dzr.id_dpd_zone = dz.id_dpd_zone');
        $query->where('dzr.id_country = ' . (int)$idCountry);
        $query->where('dzr.include_all_zip_codes = 1 OR (dzr.zip_code_from <= \'' . pSQL($zipCode) . '\' AND dzr.zip_code_to >= \'' . pSQL($zipCode) . '\')');

        $result = $this->db->executeS($query);

        return $result ?: [];
    }
}
