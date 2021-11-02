<?php


namespace Invertus\dpdBaltics\Provider;

use Country;
use Exception;
use Invertus\dpdBaltics\Repository\ZoneRangeRepository;
use Language;
use DPDZone;
use Tools;
use Validate;

class ZoneRangeProvider
{
    /**
     * @var Language
     */
    private $language;
    /**
     * @var ZoneRangeRepository
     */
    private $rangeRepository;

    public function __construct(Language $language, ZoneRangeRepository $rangeRepository)
    {
        $this->language = $language;
        $this->rangeRepository = $rangeRepository;
    }

    /**
     * Get zone ranges for Javascript
     *
     * @return array
     */
    public function getZoneRangesForJS()
    {
        $idZone = Tools::getValue('id_dpd_zone');
        $jsZoneRanges = [];

        try {
            $zone = new DPDZone($idZone);

            if (!Validate::isUnsignedId($idZone) || !Validate::isLoadedObject($zone)) {
                return $jsZoneRanges;
            }

            $countries = Country::getCountries($this->language->id);
            $zoneRanges = $zone->getRanges();
        } catch (Exception $e) {
            return $jsZoneRanges;
        }

        foreach ($zoneRanges as $range) {
            if (!isset($countries[$range->id_country])) {
                continue;
            }

            $jsZoneRanges[] = [
                'id' => $range->id,
                'zipFrom' => $range->zip_code_from,
                'zipTo' => $range->zip_code_to,
                'allRanges' => $range->include_all_zip_codes,
                'countryName' => $countries[$range->id_country]['name'],
                'countryId' => $range->id_country,
            ];
        }

        return $jsZoneRanges;
    }

    public function getAllZoneRangesCountryIsoCodes()
    {
        $isoCodes = [];
        $countries = $this->rangeRepository->findAllZoneRangeCountryIds();

        foreach ($countries as $item) {
            $isoCodes[] = Country::getIsoById((int) $item['id_country']);
        }

        return $isoCodes;
    }
}
