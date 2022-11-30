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
