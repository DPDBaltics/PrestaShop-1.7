<?php

use Invertus\dpdBaltics\Config\Config;
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
                $parcelShopImport = $this->module->getModuleContainer(ParcelShopImport::class);
                $countries = Country::getCountries($this->context->language->id, true);

                foreach ($countries as $country) {
                    $response = $parcelShopImport->importParcelShops($country['iso_code']);
                    if (!$response['success']) {
                        $this->ajaxDie(json_encode($response));
                    }
                }
                $this->ajaxDie(json_encode($response));
                break;
            default:
                return;
        }
    }
}