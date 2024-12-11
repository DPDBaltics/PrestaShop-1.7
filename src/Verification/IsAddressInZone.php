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

class IsAddressInZone
{
    /**
     * @var ZoneRangeRepository
     */
    private $zoneRangeRepository;
    /**
     * @var IsAddressInRange
     */
    private $isAddressInRange;

    public function __construct(
        ZoneRangeRepository $zoneRangeRepository,
        IsAddressInRange $isAddressInRange
    ) {
        $this->zoneRangeRepository = $zoneRangeRepository;
        $this->isAddressInRange = $isAddressInRange;
    }

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
    public function verify(\Address $address, array $zones)
    {
        $idCountry = $address->id_country ?: (int)\Configuration::get('PS_COUNTRY_DEFAULT');

        foreach ($zones as $zone) {
            // Get ranges by zone and country
            $ranges = $this->zoneRangeRepository->findBy([
                'id_dpd_zone' => $zone['id'],
                // Check by country as well, because the zone must match the country of address
                'id_country' => $idCountry,
            ]);

            if (empty($ranges)) {
                continue;
            }

            foreach ($ranges as $range) {
                if ($this->isAddressInRange->verify($address, $range)) {
                    return true;
                }
            }
        }

        return false;
    }
}