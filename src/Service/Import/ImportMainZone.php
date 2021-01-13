<?php
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

namespace Invertus\dpdBaltics\Service\Import;

class ImportMainZone
{
    /**
     * @var ImportProvider
     */
    private $importProvider;
    /**
     * @var ZoneImport
     */
    private $importable;

    public function __construct(
        ImportProvider $importProvider,
        ZoneImport $importable
    ) {
        $this->importProvider = $importProvider;
        $this->importable = $importable;
    }

    public function importLatviaZones()
    {
        return $this->importProvider->importFromFile($this->importable, __DIR__ . '/File/latvia_zones.csv');
    }

    public function importLithuaniaZones()
    {
        return $this->importProvider->importFromFile($this->importable, __DIR__ . '/File/lithuania_zones.csv');
    }
}
