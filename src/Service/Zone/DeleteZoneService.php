<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */


namespace Invertus\dpdBaltics\Service\Zone;

use DPDBaltics;
use DPDZone;
use Exception;
use Invertus\dpdBaltics\Factory\ArrayFactory;
use Invertus\dpdBaltics\Repository\CarrierRepository;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use PrestaShopException;
use Validate;

class DeleteZoneService
{
    const FILE_NAME = 'DeleteZoneService';

    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @var ArrayFactory
     */
    private $arrayFactory;

    /**
     * @var PriceRuleRepository
     */
    private $priceRuleRepository;

    /**
     * @var array
     */
    private $successfullyDeletedZones = [];

    /**
     * @var array
     */
    private $error = [];

    public function __construct(
        DPDBaltics $module,
        CarrierRepository $carrierRepository,
        PriceRuleRepository $priceRuleRepository,
        ArrayFactory $arrayFactory
    ) {
        $this->module = $module;
        $this->carrierRepository = $carrierRepository;
        $this->priceRuleRepository = $priceRuleRepository;
        $this->arrayFactory = $arrayFactory;
    }

    public function getErrors()
    {
        return $this->error;
    }

    public function getConfirmation()
    {
        $confirmation = [];
        if (!$this->successfullyDeletedZones) {
            return $confirmation;
        }
        $confirmationMessage = $this->module->l('Successfully deleted zones: ', self::FILE_NAME);
        $confirmationMessage .= implode(', ', $this->successfullyDeletedZones);

        return [$confirmationMessage];
    }

    public function bulkDeleteZones($zonesId)
    {
        foreach ($zonesId as $zoneId) {
            $this->deleteZone($zoneId);
        }
    }

    /**
     * @param $zoneId
     *
     * @return bool
     * @throws PrestaShopException
     */
    public function deleteZone($zoneId)
    {
        try {
            $zone = new DPDZone($zoneId);
        } catch (Exception $e) {
            $this->error[] = $e->getMessage();
            return false;
        }

        if (!Validate::isLoadedObject($zone)) {
            $this->error[] = $this->module->l('Zone cannot be loaded', self::FILE_NAME);
            return false;
        }

        $carriers = $this->carrierRepository->getCarriersByDPDZoneId($zone->id);

        $zoneName = $zone->name;
        if (!empty($carriers)) {
            $carrierNames = $this->arrayFactory->implodeByTagName($carriers, 'name');

            $this->error[] = sprintf(
                $this->module->l('Unable to delete zone. Zone \'%s\' is used by \'%s\' carriers', self::FILE_NAME),
                $zoneName,
                $carrierNames
            );
            return false;
        }

        $priceRules = $this->priceRuleRepository->getPriceRulesByIdZone($zone->id);

        if (!empty($priceRules)) {
            $priceRuleNames = $this->arrayFactory->implodeByTagName($priceRules, 'name');

            $this->error[] = sprintf(
                $this->module->l('Unable to delete zone. Zone \'%s\' is used by \'%s\' price rules', self::FILE_NAME),
                $zoneName,
                $priceRuleNames
            );
            return false;
        }

        if (!$zone->deleteZoneRanges()) {
            $this->error[] = $this->module->l('Failed to delete zone ranges', self::FILE_NAME);
            return false;
        }

        if (!$zone->delete()) {
            $this->error[] = $this->module->l('Failed to delete zone', self::FILE_NAME);
            return false;
        }
        $this->successfullyDeletedZones[] = $zoneName;

        return true;
    }
}
