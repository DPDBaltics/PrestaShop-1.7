<?php

namespace Invertus\dpdBaltics\Factory;

use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBalticsApi\Api\DTO\Object\OpeningHours;
use Invertus\dpdBalticsApi\Api\DTO\Object\ParcelShop;

class ShopFactory
{
    /**
     * @var ParcelShopRepository
     */
    private $parcelShopRepository;

    public function __construct(ParcelShopRepository $parcelShopRepository)
    {
        $this->parcelShopRepository = $parcelShopRepository;
    }

    /**
     * @param array $shops
     * @return array
     */
    public function createShop(array $shops)
    {
        $shopsArray = [];
        foreach ($shops as $shop) {
            $workHoursArray = [];
            $workHours = $this->parcelShopRepository->getShopWorkHoursByShopId($shop['parcel_shop_id']);
            foreach ($workHours as $workHour) {
                $workHourObj = new OpeningHours(
                    $workHour['week_day'],
                    $workHour['open_morning'],
                    $workHour['close_morning'],
                    $workHour['close_afternoon'],
                    $workHour['open_afternoon']
                );
                $workHoursArray[] = $workHourObj;
            }

            $shopObj = new ParcelShop();
            $shopObj->setParcelShopId($shop['parcel_shop_id']);
            $shopObj->setCompany($shop['company']);
            $shopObj->setCountry($shop['country']);
            $shopObj->setCity($shop['city']);
            $shopObj->setPCode($shop['p_code']);
            $shopObj->setStreet($shop['street']);
            $shopObj->setEmail($shop['email']);
            $shopObj->setLongitude($shop['longitude']);
            $shopObj->setLatitude($shop['latitude']);
            $shopObj->setOpeningHours($workHoursArray);

            $shopsArray[] = $shopObj;
        }

        return $shopsArray;
    }
}