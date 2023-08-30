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
use Invertus\dpdBaltics\Repository\ZoneRepository;
use Invertus\dpdBaltics\Validate\Zone\ZoneRangeValidate;

class IsAddressInZone
{
    /**
     * @var ZoneRepository
     */
    private $zoneRepository;

    public function __construct(
        ZoneRepository $zoneRepository
    ) {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * Check if address falls into given zones
     *
     * @param \Address $address
     *
     * @return bool
     */
    public function verify(\Address $address)
    {
        return !empty($this->zoneRepository->findAddressInZones($address));
    }
}