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

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Controller\AbstractFrontController;
use Invertus\dpdBaltics\Provider\ZoneRangeProvider;
use Invertus\dpdBaltics\Service\Import\API\ParcelShopImport;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @deprecated Use CLI command instead for reliable imports without timeout issues:
 *             php bin/console dpdbaltics:update-parcel-shops --all
 *
 * This HTTP-based cron endpoint may timeout for large countries (PL).
 * The CLI command has no timeout limitations.
 */
class DpdbalticsCronJobModuleFrontController extends AbstractFrontController
{
    public function postProcess()
    {
        // Note: We intentionally do NOT use set_time_limit() here because:
        // 1. It doesn't work on many servers (disabled in php.ini)
        // 2. wget/curl have their own timeout limits anyway
        // For reliable imports, use CLI: php bin/console dpdbaltics:update-parcel-shops --all

        $token = (string) Tools::getValue('token');
        $expectedToken = Configuration::get(Config::DPDBALTICS_HASH_TOKEN);

        // Use hash_equals to prevent timing attacks
        // Ensure both values are strings for PHP 8 compatibility
        if (empty($expectedToken) || empty($token) || !hash_equals((string) $expectedToken, $token)) {
            $this->ajaxDie(json_encode([
                'success' => false,
                'message' => 'Invalid token'
            ]));
            return;
        }

        $action = Tools::getValue('action');
        switch ($action) {
            case 'updateParcelShops':
                /** @var ParcelShopImport $parcelShopImport */
                $parcelShopImport = $this->module->getModuleContainer('invertus.dpdbaltics.service.import.api.parcel_shop_import');
                /** @var  ZoneRangeProvider $zoneRangeProvider */
                $zoneRangeProvider = $this->module->getModuleContainer('invertus.dpdbaltics.provider.zone_range_provider');
                $countriesInZoneRange = $zoneRangeProvider->getAllZoneRangesCountryIsoCodes();

                $response = ['success' => true, 'message' => 'No countries to import'];

                if ($countriesInZoneRange) {
                    foreach ($countriesInZoneRange as $country) {
                        $response = $parcelShopImport->importParcelShops($country);
                        if (isset($response['success']) && !$response['success']) {
                            $this->ajaxDie(json_encode($response));
                        }
                    }
                } else {
                    $countries = Country::getCountries($this->context->language->id, true);
                    foreach ($countries as $country) {
                        $response = $parcelShopImport->importParcelShops($country['iso_code']);
                        if (isset($response['success']) && !$response['success']) {
                            $this->ajaxDie(json_encode($response));
                        }
                    }
                }
                $this->ajaxDie(json_encode($response));

                break;
            default:
                $this->ajaxDie(json_encode([
                    'success' => false,
                    'message' => 'Unknown action. For parcel shop import, use CLI: php bin/console dpdbaltics:update-parcel-shops --all'
                ]));
        }
    }
}
