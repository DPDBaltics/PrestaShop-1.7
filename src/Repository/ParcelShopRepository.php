<?php

namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use Db;
use Invertus\dpdBalticsApi\Api\DTO\Object\ParcelShop;

class ParcelShopRepository extends AbstractEntityRepository
{
    public function deleteShopsByCountryCode($countryCode)
    {

        $sql = 'DELETE w FROM `' . _DB_PREFIX_ . 'dpd_shop_work_hours` w 
        INNER JOIN `' . _DB_PREFIX_ . 'dpd_shop` s ON s.country = "' . pSQL($countryCode) . '" 
        WHERE s.parcel_shop_id = w.parcel_shop_id';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        return $this->db->delete(
            'dpd_shop',
            '`country`= "' . pSQL($countryCode) . '"'
        );
    }

    public function getShopsByCity($country, $city)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('dpd_shop', 's');
        $query->where('s.`country` = "' . pSQL($country) . '" AND s.`city` = "' . pSQL($city) . '"');

        return $this->db->executeS($query);
    }

    public function getShopsByShopId($shopId)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('dpd_shop', 's');
        $query->innerJoin('dpd_shop_work_hours', 'wh', 's.parcel_shop_id = wh.parcel_shop_id');
        $query->where('s.`parcel_shop_id` = "' . pSQL($shopId) . '"');
        $query->groupBy('s.parcel_shop_id');

        return $this->db->executeS($query);
    }

    public function getShopWorkHoursByShopId($parcelShopId)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('dpd_shop_work_hours', 's');
        $query->where('s.`parcel_shop_id` = "' . pSQL($parcelShopId) . '"');

        return $this->db->executeS($query);
    }

    public function getAllCitiesByCountryCode($countryCode)
    {
        $query = new DbQuery();
        $query->select('city');
        $query->from('dpd_shop');
        $query->where('`country` = "' . pSQL($countryCode) . '"');
        $query->groupBy('city');
        $result = $this->db->query($query);

        $cityList = [];
        while ($row = $this->db->nextRow($result)) {
            $cityList[$row['city']] = $row['city'];
        }

        return $cityList;
    }

    public function getAllAddressesByCountryCodeAndCity($countryCode, $city)
    {
        $query = new DbQuery();
        $query->select('street, company');
        $query->from('dpd_shop');
        $query->where('`country` = "' . pSQL($countryCode) . '" AND `city` = "' . pSQL($city) . '"');
        $query->groupBy('street');
        $query->orderby('company ASC');
        $result = $this->db->query($query);

        $cityList = [];
        while ($row = $this->db->nextRow($result)) {
            $cityList[$row['company']] = $row['street'];
        }

        return $cityList;
    }

    public function hasAnyParcelShops($countryCode)
    {
        $query = new DbQuery();
        $query->select('parcel_shop_id');
        $query->from('dpd_shop');
        $query->where('`country` = "' . pSQL($countryCode) . '"');

        return $this->db->getValue($query);
    }

    public function getIdByCityAndStreet($city, $street)
    {
        $query = new DbQuery();
        $query->select('parcel_shop_id');
        $query->from('dpd_shop');
        $query->where('`city` = "' . pSQL($city) . '" AND `street` LIKE "%' . pSQL($street) . '%"');

        return $this->db->getValue($query);
    }

    public function getClosestPudoShops($long, $lat, $distance, $limit)
    {
        $sql = 'SELECT * FROM  (
        SELECT *, 
            (
                (
                    (
                        acos(
                            sin(( ' . pSQL($lat) . ' * pi() / 180))
                            *
                            sin(( `latitude` * pi() / 180)) + cos(( ' . pSQL($lat) . ' * pi() /180 ))
                            *
                            cos(( `latitude` * pi() / 180)) * cos((( ' . pSQL($long) . ' - `longitude`) * pi()/180)))
                    ) * 180/pi()
                ) * 60 * 1.1515 * 1.609344
            )
        as distance FROM `' . _DB_PREFIX_ . 'dpd_shop`
        ) markers
        WHERE distance <= ' . pSQL($distance) . '
        ORDER BY distance
        LIMIT ' . pSQL($limit) . '
        ';

        return $this->db->executeS($sql);
    }

    public function getFilterByAddress($countryCode, $city, $street)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from('dpd_shop');
        $query->where(
            '`country` = "' . pSQL($countryCode)
            . '" AND `city` = "' . pSQL($city)
            . '" AND (`street` LIKE "%' . pSQL($street) . '%" OR `company` LIKE "%' . pSQL($street) . '%")'
        );

        return $this->db->executeS($query);
    }
}
