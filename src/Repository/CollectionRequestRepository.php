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

class CollectionRequestRepository extends AbstractEntityRepository
{
    public function getPhonesAndCodes($idCollectionRequest)
    {
        $query = new DbQuery();
        $query->select('pickup_address_mobile_phone, pickup_address_mobile_phone_code');
        $query->select('receiver_address_mobile_phone');
        $query->select('receiver_address_mobile_phone_code');

        $query->from('dpd_collection_request');
        $query->where('id_dpd_collection_request = ' . (int) $idCollectionRequest);

        $result = $this->db->query($query);

        $data = [];

        while ($row = $this->db->nextRow($result)) {
            $data['pickup_address_mobile_phone'] = pSQL($row['pickup_address_mobile_phone']);
            $data['pickup_address_mobile_phone_code'] = pSQL($row['pickup_address_mobile_phone_code']);
            $data['receiver_address_mobile_phone'] = pSQL($row['receiver_address_mobile_phone']);
            $data['receiver_address_mobile_phone_code'] = pSQL($row['receiver_address_mobile_phone_code']);
        }

        return $data;
    }
}
