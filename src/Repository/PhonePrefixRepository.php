<?php

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
