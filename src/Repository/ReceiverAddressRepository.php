<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;

class ReceiverAddressRepository extends AbstractEntityRepository
{
    public function getAddressIdByOrderId($orderid)
    {
        $query = new DbQuery();
        $query->select('`id_dpd_receiver_address`, `id_origin_address`');
        $query->from('dpd_receiver_address');
        $query->where('id_order =' . (int) $orderid);

        $resource = $this->db->query($query);
        $result = [];

        while ($row = $this->db->nextRow($resource)) {
            $result[$row['id_dpd_receiver_address']] = $row['id_origin_address'];
        }

        return $result;
    }

    public function deleteOldReceiverAddress($idDpdReceiverAddress)
    {
        $this->db->delete(
            'dpd_receiver_address',
            'id_dpd_receiver_address = ' . (int) $idDpdReceiverAddress
        );
    }
}
