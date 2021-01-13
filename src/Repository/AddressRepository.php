<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use DPDAddressTemplate;

class AddressRepository extends AbstractEntityRepository
{
    public function getAddressPhonesAndCodes($idAddressTemplate)
    {
        $query = new DbQuery();
        $query->select('mobile_phone, mobile_phone_code');
        $query->from('dpd_address_template');
        $query->where('id_dpd_address_template=' . (int) $idAddressTemplate);

        $result = $this->db->query($query);

        $data = [];

        while ($row = $this->db->nextRow($result)) {
            $data['mobile_phone'] = pSQL($row['mobile_phone']);
            $data['mobile_phone_code'] = pSQL($row['mobile_phone_code']);
        }

        return $data;
    }

    public function findAllByShop()
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(DPDAddressTemplate::$definition['table'], 'a');
        $query->where('a.type = "' . DPDAddressTemplate::ADDRESS_TYPE_COLLECTION_REQUEST . '"');

        $addresses = $this->db->executeS($query);
        if (!$addresses) {
            $addresses = [];
        }

        return $addresses;
    }
}
