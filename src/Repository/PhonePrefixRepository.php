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
use Invertus\dpdBaltics\Config\Config;

class PhonePrefixRepository extends AbstractEntityRepository
{
    /**
     * @return array
     * @throws PrestaShopDatabaseException
     * return Call prefixes as array for chosen select
     */
    public function getCallPrefixes()
    {
        $query = new DbQuery();
        $query->select('c.`call_prefix`');
        $query->from('country', 'c');
        $resource = Db::getInstance()->query($query);
        $result = [];
        while ($row = Db::getInstance()->nextRow($resource)) {
            $callPrefix = Config::PHONE_CODE_PREFIX . $row['call_prefix'];
            $result[$callPrefix] = $callPrefix;
        }

        return $result;
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     * return Call prefixes as array for chosen select
     */
    public function getCallPrefixesFrontOffice()
    {
        $query = new DbQuery();
        $query->select('c.`call_prefix`');
        $query->from('country', 'c');
        $resource = Db::getInstance()->query($query);
        $result = [];
        while ($row = Db::getInstance()->nextRow($resource)) {
            $callPrefix = Config::PHONE_CODE_PREFIX . $row['call_prefix'];
            $result[$callPrefix] = $row['call_prefix'];
        }

        return $result;
    }

    public function getCountriesCallPrefix($isoCode)
    {
        $query = new DbQuery();
        $query->select('c.`call_prefix`');
        $query->from('country', 'c');
        $query->where('c.iso_code = "' . pSQL($isoCode) . '"');
        $result = Config::PHONE_CODE_PREFIX . $this->db->getValue($query);

        return $result;
    }
}
