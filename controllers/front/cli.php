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
 if (!defined('_PS_VERSION_')) {
    exit;
 }

 use Invertus\dpdBaltics\Config\Config;
 use Invertus\dpdBaltics\Provider\ZoneRangeProvider;
 use Invertus\dpdBaltics\Service\Import\API\ParcelShopImport; 
class DpdbalticsCliModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ajax;

    public function init()
    {
        $this->ajax = 1;

        if (php_sapi_name() !== 'cli') {
            $this->ajaxRender('Forbidden call.');
            return;
        }

        // TODO: move this to a separate service cause same logic is used in cron job front controller

        $startTime = microtime(true);
        set_time_limit(0);

        $token = Tools::getValue('token');
        if ($token !== Configuration::get(Config::DPDBALTICS_HASH_TOKEN)) {
            $this->ajaxRender('Wrong token (Execution time: ' . round(microtime(true) - $startTime, 2) . 's)');
            return;
        }

        $action = Tools::getValue('action');
        $responseMessage = 'No action specified.';

        if ($action === 'updateParcelShops') {
            /** @var ParcelShopImport $parcelShopImport */
            $parcelShopImport = $this->module->getModuleContainer('invertus.dpdbaltics.service.import.api.parcel_shop_import');
            /** @var ZoneRangeProvider $zoneRangeProvider */
            $zoneRangeProvider = $this->module->getModuleContainer('invertus.dpdbaltics.provider.zone_range_provider');

            $countriesInZoneRange = $zoneRangeProvider->getAllZoneRangesCountryIsoCodes();

            if (!$countriesInZoneRange) {
                $countries = Country::getCountries($this->context->language->id, true);
                $countriesInZoneRange = array_column($countries, 'iso_code');
            }

            foreach ($countriesInZoneRange as $countryIso) {
                $response = $parcelShopImport->importParcelShops($countryIso);
            
                $executionTime = round(microtime(true) - $startTime, 2);
            
                if (isset($response['success']) && !$response['success']) {
                    $errorMessage = isset($response['error']) ? $response['error'] : 'Unknown error occurred';
                    
                    PrestaShopLogger::addLog('DPDBaltics: Error importing parcel shop for country: ' . $countryIso . ' - ' . $errorMessage . ' (Execution time: ' . $executionTime . 's)', 1, null, null, null, true);
            
                    $this->ajaxRender($errorMessage . ' (Execution time: ' . $executionTime . 's)');
                    return;
                }
            
                PrestaShopLogger::addLog('DPDBaltics: Successfully imported parcel shop for country: ' . $countryIso . ' (Execution time: ' . $executionTime . 's)', 1, null, null, null, true);
            }
            
            $responseMessage = isset($response['success_message']) ? $response['success_message'] : 'Parcel shops updated successfully.';
        }

        // Ensure $responseMessage is set correctly
        if (empty($responseMessage)) {
            $responseMessage = 'No message returned from action.';  // Fallback message if empty
        }

        $executionTime = round(microtime(true) - $startTime, 2);
        $this->ajaxRender($responseMessage . ' (Execution time: ' . $executionTime . 's)');
    }
}
