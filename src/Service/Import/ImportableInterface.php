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

interface ImportableInterface
{
    /**
     * Import given array of rows
     *
     * @param array $rows
     *
     * @return void
     */
    public function importRows(array $rows);

    /**
     * Check if import had any errors
     *
     * @return bool
     */
    public function hasErrors();

    /**
     * Get array of import errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * Get number of imported rows
     *
     * @return int
     */
    public function getImportedRowsCount();

    /**
     * Get import warnings
     *
     * @return array
     */
    public function getWarnings();


    /**
     * Get import confirmations
     *
     * @return array
     */
    public function getConfirmations();

    /**
     * Set importable to use transaction if it is supported
     *
     * @return void
     */
    public function useTransaction();

    /**
     * Delete previous data if it is relevant
     *
     * @return array
     */
    public function deleteOldData();
}
