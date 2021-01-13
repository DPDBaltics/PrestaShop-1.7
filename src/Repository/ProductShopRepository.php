<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class ProductShopRepository extends AbstractEntityRepository
{
    public function deleteProductShops($productId)
    {
        return $this->db->delete(
            'dpd_product_shop',
            '`id_dpd_product`="' . (int)$productId . '"'
        );
    }

    public function insertProductShop($productId, $shopId)
    {
        return $this->db->insert(
            'dpd_product_shop',
            [
                'id_dpd_product' => (int) $productId,
                'id_shop' => (int) $shopId
            ]
        );
    }

    public function getProductShops($idProduct)
    {
        $query = new DbQuery();
        $query->select('s.id_shop AS id');
        $query->select('s.name');
        $query->select('IF(dps.id_dpd_product = ' . (int) $idProduct . ', 1, 0) `selected`');
        $query->from('shop', 's');
        $query->leftJoin('dpd_product_shop', 'dps', 's.id_shop = dps.id_shop AND dps.id_dpd_product = ' . (int) $idProduct);

        return $this->db->getInstance()->executeS($query);
    }

    /**
     * @param $idReference
     * @return array|false|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    public function getProductShopsByReference($carrierReference)
    {
        $query = new DbQuery();
        $query->select('ps.id_shop, p.all_shops');
        $query->from('dpd_product', 'p');
        $query->leftJoin('dpd_product_shop', 'ps', 'ps.`id_dpd_product` = p.`id_dpd_product`');
        $query->where('p.id_reference = ' . (int) $carrierReference);

        return $this->db->executeS($query);
    }
}
