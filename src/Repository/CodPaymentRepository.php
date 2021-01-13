<?php

namespace Invertus\dpdBaltics\Repository;

use Db;
use DbQuery;
use Invertus\dpdBaltics\Repository\AbstractEntityRepository;

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

class CodPaymentRepository extends AbstractEntityRepository
{
    public function getCodPaymentModules()
    {
        $query = new DbQuery();
        $query->select('`id_payment_module`');
        $query->from('dpd_cod_payment_modules');

        $resource = $this->db->query($query);
        $result = [];

        while ($row = $this->db->nextRow($resource)) {
            $result[] = $row['id_payment_module'];
        }

        return $result;
    }

    public function removeCodPaymentModules()
    {
        $query = new DbQuery();
        $query->type('DELETE');
        $query->from('dpd_cod_payment_modules');

        if (!Db::getInstance()->execute($query)) {
            return false;
        }

        return true;
    }

    public function addCodPaymentModules($codPaymentsId)
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'dpd_cod_payment_modules` (id_payment_module) VALUES ';

        $sql .= implode(', ', $codPaymentsId);

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        return true;
    }
}
