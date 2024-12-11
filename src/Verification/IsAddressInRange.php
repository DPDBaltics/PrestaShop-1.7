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

namespace Invertus\dpdBaltics\Verification;

use Invertus\dpdBaltics\Repository\ZoneRangeRepository;
use Invertus\dpdBaltics\Validate\Zone\ZoneRangeValidate;

class IsAddressInRange
{
    /**
     * Check if address falls into given zones
     *
     * @param \Address $address
     * @param array $zones
     *
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function verify(\Address $address, array $range)
    {
        $idCountry = $address->id_country ?: (int)\Configuration::get('PS_COUNTRY_DEFAULT');

        //NOTE: if countries do not match, we continue.
        if ((int) $idCountry !== (int) $address->id_country) {
            return false;
        }

        if ($range['include_all_zip_codes']) {
            return true;
        }

        if (ZoneRangeValidate::isZipCodeInRange(
            $address->postcode,
            $range['zip_code_from'],
            $range['zip_code_to'],
            $idCountry
        )) {
            return true;
        }

        return false;
    }
}