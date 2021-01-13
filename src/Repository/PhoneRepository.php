<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use DPDOrderPhone;

class PhoneRepository extends AbstractEntityRepository
{
    public function getOrderPhoneIdByCartId($idCart)
    {
        $query = new DbQuery();
        $query->select('`id_dpd_order_phone`');
        $query->from('dpd_order_phone');
        $query->where('`id_cart`="'.(int) $idCart.'"');
        return $this->db->getValue($query);
    }

    public function saveCarrierPhone($idCart, $dpdPhone, $dpdPhoneCode)
    {
        $idDpdOrderPhone = $this->phoneRepository->getOrderPhoneIdByCartId($idCart);

        if (!$idDpdOrderPhone) {
            $dpdOrderPhone = new DPDOrderPhone();
        } else {
            $dpdOrderPhone = new DPDOrderPhone($idDpdOrderPhone);
        }

        $dpdOrderPhone->phone = $dpdPhone;
        $dpdOrderPhone->phone_area = $dpdPhoneCode;
        $dpdOrderPhone->id_cart = $idCart;

        if (!$dpdOrderPhone->save()) {
            return false;
        }

        return true;
    }
}
