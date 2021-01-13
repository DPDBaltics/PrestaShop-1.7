<?php

namespace Invertus\dpdBaltics\Repository;

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
}
