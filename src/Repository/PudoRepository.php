<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class PudoRepository extends AbstractEntityRepository
{
    public function getIdByCart($cartId)
    {
        $query = new DbQuery();
        $query->select('c.`id`');
        $query->from('dpd_pudo_cart', 'c');
        $query->where('c.`id_cart`="' . (int)$cartId . '"');

        return $this->db->getValue($query);
    }

    public function getPudoIdByCarrierId($carrierId, $cartId)
    {
        $query = new DbQuery();
        $query->select('c.`id`');
        $query->from('dpd_pudo_cart', 'c');
        $query->where('c.`id_carrier`="' . pSql($carrierId) . '"');
        $query->where('c.`id_cart`="' . (int)$cartId . '"');

        return $this->db->getValue($query);
    }
}
