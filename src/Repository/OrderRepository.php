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

class OrderRepository extends AbstractEntityRepository
{
    public function getDPDOrderIdByOrderId($idOrder)
    {
        $query = new DbQuery();
        $query->select('id_dpd_order');
        $query->from('dpd_order');
        $query->where('`id_order`="'.(int) $idOrder.'"');
        return $this->db->getValue($query);
    }

    public function getPhoneByIdCart($idCart)
    {
        $query = new DbQuery();
        $query->select('`phone`, `phone_area`');
        $query->from('dpd_order_phone');
        $query->where('`id_cart`="'.(int) $idCart.'"');
        $result = $this->db->executeS($query);
        if (is_array($result) && !empty($result)) {
            return $result[0];
        }
        return $result;
    }

    public function getOrderPhoneIdByCartId($idCart)
    {
        $query = new DbQuery();
        $query->select('`id_dpd_order_phone`');
        $query->from('dpd_order_phone');
        $query->where('`id_cart`="'.(int) $idCart.'"');
        return $this->db->getValue($query);
    }

    public function getOrderCarrierId($orderId)
    {
        $query = new DbQuery();
        $query->select('id_order_carrier');
        $query->from('order_carrier');
        $query->where('id_order = ' . (int) $orderId);

        return $this->db->getValue($query);
    }
}
