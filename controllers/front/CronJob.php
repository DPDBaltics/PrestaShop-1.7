<?php

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\ZoneRangeProvider;
use Invertus\dpdBaltics\Service\Import\API\ParcelShopImport;

class DpdbalticsCronJobModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $token = Tools::getValue('token');
        if ($token !== Configuration::get(Config::DPDBALTICS_HASH_TOKEN)) {
            $this->ajaxDie([
                'success' => false,
                'message' => 'wrong token'
            ]);
        }

        $action = Tools::getValue('action');
        switch ($action) {
            case 'updateParcelShops':
                /** @var ParcelShopImport $parcelShopImport */
                $parcelShopImport = $this->module->getModuleContainer('invertus.dpdbaltics.service.import.api.parcel_shop_import');
                /** @var  ZoneRangeProvider $zoneRangeProvider */
                $zoneRangeProvider = $this->module->getModuleContainer('invertus.dpdbaltics.provider.zone_range_provider');
                $countriesInZoneRange = $zoneRangeProvider->getAllZoneRangesCountryIsoCodes();

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
                return;
        }
    }
}
