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

class CourierRequestRepository extends AbstractEntityRepository
{
    public function getPhonesAndCodes($idCourierRequest)
    {
        $query = new DbQuery();
        $query->select('sender_phone_code, sender_phone');
        $query->from('dpd_courier_request');
        $query->where('id_dpd_courier_request = ' . (int) $idCourierRequest);

        $result = $this->db->query($query);

        $data = [];

        while ($row = $this->db->nextRow($result)) {
            $data['sender_phone'] = pSQL($row['sender_phone']);
            $data['sender_phone_code'] = pSQL($row['sender_phone_code']);
        }

        return $data;
    }
}
