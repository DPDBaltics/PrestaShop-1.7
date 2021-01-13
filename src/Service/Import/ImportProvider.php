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

use DPDBaltics;
use Exception;
use Invertus\dpdBaltics\Adapter\DPDConfigurationAdapter;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Exception\ImportException;
use Invertus\dpdBaltics\File\UploadedFile;

class ImportProvider
{
    const FILE_NAME = 'ImportProvider';

    /**
     * @var DPDConfigurationAdapter
     */
    private $configuration;

    /**
     * @var DPDBaltics
     */
    private $module;

    private $deleteBeforeImport = false;

    public function __construct(DPDBaltics $module, DPDConfigurationAdapter $configuration)
    {
        $this->configuration = $configuration;
        $this->module = $module;
    }

    /**
     * Import data
     *
     * @param ImportableInterface $importable
     * @param string|null $importFile
     * @param bool $useTransaction
     *
     * @return bool|string[] TRUE if import was successful or array of errors otherwise
     *
     * @throws ImportException
     */
    public function import(ImportableInterface $importable, $importFile = null, $useTransaction = false)
    {
        if (!isset($_FILES['DPD_IMPORT_FILE'])) {
            throw new ImportException($this->module->l('Import file was not found', self::FILE_NAME));
        }

        if (!$importFile) {
            try {
                $uploadedFile = new UploadedFile(
                    'DPD_IMPORT_FILE',
                    'csv',
                    ['text/csv', 'text/plain']
                );
            } catch (Exception $e) {
                return [
                    $e->getMessage(),
                ];
            }

            $importFile = $uploadedFile->getTmpName();
        }

        return $this->importFromFile($importable, $importFile, $useTransaction);
    }

    /**
     * Does all the import logic without validation
     *
     * @param ImportableInterface $importable
     * @param null $importFile
     * @param bool $useTransaction
     * @return array|bool
     */
    public function importFromFile(ImportableInterface $importable, $importFile = null, $useTransaction = false)
    {
        $delimiter = $this->configuration->get(Config::IMPORT_FIELD_SEPARATOR);
        $skipLines = (int) $this->configuration->get(Config::IMPORT_LINES_SKIP);

        $resource = fopen($importFile, 'r');
        $rows = [];

        while (false !== ($row = fgetcsv($resource, 0, $delimiter))) {
            if (0 < $skipLines) {
                $skipLines--;
                continue;
            }

            $rows[] = $row;
        }

        fclose($resource);

        if ($useTransaction) {
            $importable->useTransaction();
        }
        if ($this->deleteBeforeImport) {
            $importable->deleteOldData();
        }

        $importable->importRows($rows);

        if ($importable->hasErrors()) {
            return $importable->getErrors();
        }

        return true;
    }

    public function deleteBeforeImport()
    {
        $this->deleteBeforeImport = true;
    }
}
