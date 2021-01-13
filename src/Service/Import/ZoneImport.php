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

namespace Invertus\dpdBaltics\Service\Import;

use Country;
use DPDBaltics;
use DPDZone;
use DPDZoneRange;
use Invertus\dpdBaltics\Adapter\ZoneAdapter;
use Invertus\dpdBaltics\DTO\ZoneRangeObject;
use Invertus\dpdBaltics\ORM\EntityManager;
use Invertus\dpdBaltics\Repository\DPDZoneRepository;
use Invertus\dpdBaltics\Repository\ZoneRepository;
use Invertus\dpdBaltics\Validate\Zone\ZoneDeleteValidate;
use Invertus\dpdBaltics\Validate\Zone\ZoneRangeValidate;
use Shop;
use Smarty;
use Validate;

class ZoneImport implements ImportableInterface
{
    const FILE_NAME = 'ZoneImport';

    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var bool
     */
    private $errors = [];

    /**
     * @var array
     */
    private $confirmations = [];

    /**
     * @var int
     */
    private $importedRowsCount = 0;

    /**
     * @var ZoneAdapter
     */
    private $zoneAdapter;

    /**
     * @var ZoneRangeValidate
     */
    private $zoneRangeValidate;
    /**
     * @var DPDZoneRepository
     */
    private $zoneRepository;
    /**
     * @var Smarty
     */
    private $smarty;

    public function __construct(
        DPDBaltics $module,
        EntityManager $em,
        ZoneAdapter $zoneAdapter,
        ZoneRangeValidate $zoneRangeValidate,
        DPDZoneRepository $zoneRepository,
        Smarty $smarty
    ) {
        $this->module = $module;
        $this->em = $em;
        $this->zoneAdapter = $zoneAdapter;
        $this->zoneRangeValidate = $zoneRangeValidate;
        $this->zoneRepository = $zoneRepository;
        $this->smarty = $smarty;
    }

    /**
     * @param $zones
     * @return bool | array
     */
    public function validateAndAdaptZones($zones)
    {
        foreach ($zones as &$zoneRanges) {
            $zoneRanges = $this->zoneAdapter->convertSnakeToCamel($zoneRanges);
            $zoneRanges = $this->zoneAdapter->convertZoneRangesToObjects($zoneRanges);

            if (!$this->zoneRangeValidate->validateAndFormatZoneRanges($zoneRanges) ||
                !$this->zoneRangeValidate->validateZoneRangesOverlap($zoneRanges)
            ) {
                $this->errors = array_merge($this->errors, $this->zoneRangeValidate->errors);
            }
        }

        return $zones;
    }

    public function validateAndAdaptZone($zoneRanges)
    {
        $zoneRanges = $this->zoneAdapter->convertSnakeToCamel($zoneRanges);
        $zoneRanges = $this->zoneAdapter->convertToFormat($zoneRanges);
        $zoneRanges = $this->zoneAdapter->convertZoneRangesToObjects($zoneRanges);

        if (!$this->zoneRangeValidate->validateAndFormatZoneRanges($zoneRanges) ||
            !$this->zoneRangeValidate->validateZoneRangesOverlap($zoneRanges)
        ) {
            $this->errors = array_merge($this->errors, $this->zoneRangeValidate->errors);
        }

        return $zoneRanges;
    }

    /**
     * {@inheritdoc}
     */
    public function importRows(array $rows)
    {
        $zones = $this->groupRows($rows);

        $break = $this->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/admin/partials/break.tpl'
        );

        if ($this->hasErrors() || !$zones) {
            return;
        }

        $this->confirmations[] = sprintf(
            $this->module->l('Successfully imported %s zones'),
            count($zones)
        ) . $break;


        foreach ($zones as $zoneName => $zoneRanges) {
            $zoneObject = $this->validateAndAdaptZone($zoneRanges);
            $idZone = $this->zoneRepository->getByName($zoneName);
            $zone = new DPDZone($idZone);

            if ($zone->id) {
                $zone->deleteZoneRanges();
            } else {
                $zone->name = $zoneName;
                $zone->is_custom = 1;
                $zone->save();
            }

            /**
             * @var $range ZoneRangeObject
             */
            foreach ($zoneObject as $range) {
                $zoneRange = new DPDZoneRange();
                $zoneRange->id_dpd_zone = $zone->id;
                $zoneRange->include_all_zip_codes = (int)$range->getIncludeAll();
                $zoneRange->id_country = (int)$range->getIdCountry();
                $zoneRange->zip_code_from = $range->getZipCodeFrom();
                $zoneRange->zip_code_to = $range->getZipCodeTo();
                $zoneRange->zip_code_from_numeric = $range->getNumericZipCodeFrom();
                $zoneRange->zip_code_to_numeric = $range->getNumericZipCodeTo();

                $zoneRange->save();
            }
        }
        $this->importedRowsCount += $zoneObject->getCount();
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function getImportedRowsCount()
    {
        return $this->importedRowsCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getWarnings()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmations()
    {
        return $this->confirmations;
    }

    /**
     * {@inheritdoc}
     */
    public function useTransaction()
    {
    }

    /**
     * Group rows by zone name
     *
     * @param array $rows
     *
     * @return array
     */
    private function groupRows(array $rows)
    {
        $groupedRows = [];

        foreach ($rows as $row) {
            $zoneName = isset($row[0]) ? $row[0] : null;
            $countryIso = isset($row[1]) ? $row[1] : null;
            $includeAllRanges = isset($row[2]) ? (bool)$row[2] : null;
            $zipFrom = isset($row[3]) ? $row[3] : null;
            $zipTo = isset($row[4]) ? $row[4] : null;

            if (null === $zoneName ||
                null === $countryIso ||
                null === $includeAllRanges ||
                null === $zipFrom ||
                null === $zipTo
            ) {
                continue;
            }

            if (empty($zoneName) ||
                empty($countryIso) ||
                (!$includeAllRanges && (empty($zipFrom) || empty($zipTo))) ||
                !Validate::isLangIsoCode($countryIso)
            ) {
                $line = sprintf('%s %s %s %s %s', $zoneName, $countryIso, $includeAllRanges, $zipFrom, $zipTo);
                $this->errors[] = sprintf($this->module->l('Invalid row data: %s', self::FILE_NAME), $line);
                continue;
            }

            $idCountry = Country::getByIso($countryIso);
            if (!$idCountry) {
                $this->errors[] =
                    sprintf($this->module->l('Country "%s" does not exist', self::FILE_NAME), $countryIso);
                continue;
            }

            $groupedRows[$zoneName][] = [
                'id_country' => $idCountry,
                'all_ranges' => $includeAllRanges,
                'zip_from' => $zipFrom,
                'zip_to' => $zipTo,
            ];
        }

        if (!$this->hasErrors() && empty($groupedRows)) {
            $this->errors[] = $this->module->l('No rows to import', self::FILE_NAME);
        }

        return $groupedRows;
    }

    /**
     * Makes sure you can delete all zones before starting actual deletion
     * @param array $idZones
     * @param $idShop
     * @return bool
     * @throws Exception
     */
    public function zonesValidateDeletion(array $idZones)
    {
        foreach ($idZones as $idZone) {
            /** @var ZoneDeleteValidate $zoneDeletionValidation */
            $zoneDeletionValidation = $this->module->getModuleContainer(ZoneDeleteValidate::class);
            $this->errors = $zoneDeletionValidation->zoneValidateDeletionReturnError($idZone);
            if (!empty($this->errors)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete previous data if it is relevant
     *
     */
    public function deleteOldData()
    {
        /** @var ZoneRepository $zoneRepository */
        $zoneRepository = $this->module->getModuleContainer(ZoneRepository::class);
        $idZones = $zoneRepository->findAllZonesIds();

        if (!$this->zonesValidateDeletion($idZones)) {
            return false;
        };

        $idZones = $zoneRepository->findAllZonesIds();
        foreach ($idZones as $idZone) {
            $zone = new DPDZone($idZone);
            if ($zone->id) {
                $zone->deleteZoneRanges();
            }

            $zone->delete();
        }
    }
}
