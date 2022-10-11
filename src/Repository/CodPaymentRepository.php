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
