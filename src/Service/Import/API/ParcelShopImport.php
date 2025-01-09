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

if (!defined('_PS_VERSION_')) {
    exit;
}

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