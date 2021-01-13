<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class OrderDeliveryTimeRepository extends AbstractEntityRepository
{
    public function getOrderDeliveryIdByCartId($cartId)
    {
        $query = new DbQuery();
        $query->select('`id_dpd_order_delivery_time`');
        $query->from('dpd_order_delivery_time');
        $query->where('`id_cart`="'.(int) $cartId.'"');

        return $this->db->getValue($query);
    }
}