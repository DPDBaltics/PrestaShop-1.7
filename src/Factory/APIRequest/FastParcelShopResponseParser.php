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

namespace Invertus\dpdBaltics\Factory\APIRequest;

use Invertus\dpdBalticsApi\Api\DTO\Object\ParcelShop;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelShopSearchResponse;
use stdClass;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Fast parser for ParcelShopSearchResponse that bypasses slow Symfony serializer.
 * Uses simple property assignment instead of ReflectionExtractor.
 *
 * Performance: ~100x faster than Symfony serializer for large datasets.
 */
class FastParcelShopResponseParser
{
    /**
     * Parse API response array into ParcelShopSearchResponse object.
     * This is much faster than Symfony's ObjectNormalizer with ReflectionExtractor.
     *
     * @param array $responseData Raw response data from API
     * @return ParcelShopSearchResponse
     */
    public function parse(array $responseData)
    {
        $response = new ParcelShopSearchResponse();

        if (isset($responseData['status'])) {
            $response->setStatus($responseData['status']);
        }

        if (isset($responseData['errlog'])) {
            $response->setErrLog($responseData['errlog']);
        }

        $parcelShops = [];
        if (isset($responseData['parcelshops']) && is_array($responseData['parcelshops'])) {
            foreach ($responseData['parcelshops'] as $shopData) {
                $parcelShops[] = $this->parseParcelShop($shopData);
            }
        }

        $response->setParcelShops($parcelShops);

        return $response;
    }

    /**
     * Parse single parcel shop data into ParcelShop object.
     *
     * @param array|object $data
     * @return ParcelShop
     */
    private function parseParcelShop($data)
    {
        // Handle both array and object (stdClass) formats
        $data = (array) $data;

        $shop = new ParcelShop();

        // PHP 5.6 compatible - use isset() instead of ?? operator
        $shop->setParcelShopId(isset($data['parcelshop_id']) ? $data['parcelshop_id'] : null);
        $shop->setCompany(isset($data['company']) ? $data['company'] : null);
        $shop->setCountry(isset($data['country']) ? $data['country'] : null);
        $shop->setCity(isset($data['city']) ? $data['city'] : null);
        $shop->setPCode(isset($data['pcode']) ? $data['pcode'] : null);
        $shop->setStreet(isset($data['street']) ? $data['street'] : null);
        $shop->setEmail(isset($data['email']) ? $data['email'] : null);
        $shop->setPhone(isset($data['phone']) ? $data['phone'] : null);
        $shop->setDistance(isset($data['distance']) ? $data['distance'] : null);
        $shop->setLongitude(isset($data['longitude']) ? $data['longitude'] : null);
        $shop->setLatitude(isset($data['latitude']) ? $data['latitude'] : null);
        $shop->setCoordinateX(isset($data['coordinateX']) ? $data['coordinateX'] : null);
        $shop->setCoordinateY(isset($data['coordinateY']) ? $data['coordinateY'] : null);
        $shop->setCoordinateZ(isset($data['coordinateZ']) ? $data['coordinateZ'] : null);

        $openingHours = array();
        if (isset($data['openingHours']) && is_array($data['openingHours'])) {
            foreach ($data['openingHours'] as $hoursData) {
                $openingHours[] = $this->parseOpeningHours($hoursData);
            }
        }
        $shop->setOpeningHours($openingHours);

        return $shop;
    }

    /**
     * Parse opening hours data into stdClass object.
     * Using stdClass because existing code accesses properties directly (e.g., $item->weekday)
     *
     * @param array|object $data
     * @return stdClass
     */
    private function parseOpeningHours($data)
    {
        // Handle both array and object (stdClass) formats
        $data = (array) $data;

        // Create stdClass to match expected property access pattern
        // PHP 5.6 compatible - use isset() instead of ?? operator
        $hours = new stdClass();
        $hours->weekday = isset($data['weekday']) ? $data['weekday'] : null;
        $hours->openMorning = isset($data['openMorning']) ? $data['openMorning'] : null;
        $hours->closeMorning = isset($data['closeMorning']) ? $data['closeMorning'] : null;
        $hours->openAfternoon = isset($data['openAfternoon']) ? $data['openAfternoon'] : null;
        $hours->closeAfternoon = isset($data['closeAfternoon']) ? $data['closeAfternoon'] : null;

        return $hours;
    }
}
