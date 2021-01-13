<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class ProductZoneRepository extends AbstractEntityRepository
{
    public function deleteProductZones($productId)
    {
        return $this->db->delete(
            'dpd_product_zone',
            '`id_dpd_product`="' . (int)$productId . '"'
        );
    }

    public function insertProductZone($productId, $zoneId)
    {
        return $this->db->insert(
            'dpd_product_zone',
            [
                'id_dpd_product' => (int) $productId,
                'id_dpd_zone' => (int) $zoneId
            ]
        );
    }

    public function getProductSelectedZones($idProduct)
    {
        $query = new DbQuery();
        $query->select('dz.`id_dpd_zone` AS id');
        $query->select('dz.`name`');
        $query->select('IF(dpz.id_dpd_product = ' . (int) $idProduct . ', 1, 0) `selected`');
        $query->from('dpd_zone', 'dz');
        $query->leftJoin('dpd_product_zone', 'dpz', 'dz.id_dpd_zone = dpz.id_dpd_zone AND dpz.id_dpd_product = ' . (int) $idProduct);

        return $this->db->getInstance()->executeS($query);
    }
}
