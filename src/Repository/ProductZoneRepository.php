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

if (!defined('_PS_VERSION_')) {
    exit;
}

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
