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

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Controller\AbstractAdminController;
use Invertus\dpdBaltics\Service\API\ParcelShopSearchApiService;
use Invertus\dpdBaltics\Service\Import\API\ParcelShopImport;
use Invertus\dpdBaltics\Service\Import\ImportMainZone;
use Invertus\dpdBaltics\Service\Parcel\ParcelUpdateService;
use Invertus\dpdBaltics\Service\PudoService;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelShopSearchResponse;

require_once dirname(__DIR__).'/../vendor/autoload.php';

class AdminDPDBalticsAjaxController extends AbstractAdminController
{
    public function ajaxProcessImportZones()
    {
        /** @var ImportMainZone $importOnLoginService */
        $importOnLoginService = $this->module->getModuleContainer()->get(ImportMainZone::class);

        $selectedCountry = Tools::getValue('country');
        switch ($selectedCountry) {
            case 'latvia' :
                $this->ajaxDie(Tools::jsonEncode($importOnLoginService->importLatviaZones()));
                break;
            case 'lithuania':
                $this->ajaxDie(Tools::jsonEncode($importOnLoginService->importLithuaniaZones()));
                break;
            default:
                $this->ajaxDie();
                break;
        }
    }

    public function ajaxProcessImportParcels()
    {
        /** @var ParcelShopImport $parcelShopImport */
        $parcelShopImport = $this->module->getModuleContainer(ParcelShopImport::class);

        $countryId = Tools::getValue('countryId');
        $countryIso = Country::getIsoById($countryId);
        $this->ajaxDie(json_encode($parcelShopImport->importParcelShops($countryIso)));
    }
}