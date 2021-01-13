<?php

namespace Invertus\dpdBaltics\Service\Import\API;

use Configuration;
use DPDBaltics;
use EntityAddException;
use Exception;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\API\ParcelShopSearchApiService;
use Invertus\dpdBaltics\Service\Parcel\ParcelUpdateService;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelShopSearchResponse;
use Tools;

class ParcelShopImport
{
    const FILE_NAME = 'ParcelShopImport';

    /**
     * @var ParcelShopSearchApiService
     */
    private $apiService;
    /**
     * @var ParcelUpdateService
     */
    private $parcelUpdateService;
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(
        ParcelShopSearchApiService $apiService,
        ParcelUpdateService $parcelUpdateService,
        DPDBaltics $module
    ) {
        $this->apiService = $apiService;
        $this->parcelUpdateService = $parcelUpdateService;
        $this->module = $module;
    }

    public function importParcelShops($selectedCountry)
    {
        /** @var ParcelShopSearchResponse $shops */
        $shops = $this->apiService->getAllCountryParcels(
            $selectedCountry,
            Config::FETCH_PUDO_POINT,
            Config::RETRIEVE_OPENING_HOURS
        );
        if ($shops->getStatus() === Config::API_RESPONSE_ERROR_STATUS) {
            return
                [
                    'success' => false,
                    'error' => sprintf($this->module->l('Failed to update parcel shops: %s', self::FILE_NAME), $shops->getErrLog())
                ];
        }
        try {
            $this->parcelUpdateService->updateParcels($shops->getParcelShops(), $selectedCountry);
        } catch (EntityAddException $e) {
            return
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
        } catch (\Error $e) {
            return
                [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
        }

        return
            [
                'success' => true,
                'success_message' => $this->module->l('Successfully updated parcel shops', self::FILE_NAME)
            ];
    }

}