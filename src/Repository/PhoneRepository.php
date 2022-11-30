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

    public function findDpdOrderPhone($id)
    {
        return new DPDOrderPhone($id);
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
