<?php

namespace Invertus\dpdBaltics\Service\Parcel;

use DPDShop;
use DPDShopWorkHours;
use EntityAddException;
use Exception;
use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBalticsApi\Api\DTO\Object\OpeningHours;
use Invertus\dpdBalticsApi\Api\DTO\Object\ParcelShop;

class ParcelUpdateService
{

    /**
     * @var ParcelShopRepository
     */
    private $parcelShopRepository;

    public function __construct(ParcelShopRepository $parcelShopRepository)
    {
        $this->parcelShopRepository = $parcelShopRepository;
    }

    public function updateParcels(array $parcels, $countryCode)
    {
        $isDeleteSuccess = $this->parcelShopRepository->deleteShopsByCountryCode($countryCode);
        if (!$isDeleteSuccess) {
            return false;
        }

        foreach ($parcels as $parcel) {
            if ($parcel instanceof ParcelShop) {
                $this->addParcelShop($parcel);
            } else {
                $parcelShop = $this->resetParcelObject($parcel);
                $this->addParcelShop($parcelShop);
            }
        }

        return true;
    }

    public function addParcelShop(ParcelShop $parcel)
    {
        $parcelShop = new DPDShop();
        $parcelShop->parcel_shop_id = $parcel->getParcelShopId();
        $parcelShop->company = $parcel->getCompany();
        $parcelShop->country = $parcel->getCountry();
        $parcelShop->city = $parcel->getCity();
        $parcelShop->p_code = $parcel->getPCode();
        $parcelShop->street = $parcel->getStreet();
        $parcelShop->email = $parcel->getEmail();
        $parcelShop->phone = $parcel->getPhone();
        $parcelShop->longitude = $parcel->getLongitude();
        $parcelShop->latitude = $parcel->getLatitude();

        try {
            $parcelShop->add();
        } catch (Exception $e) {
            throw new EntityAddException(
                'Failed to add parcel shop',
                EntityAddException::DPD_PARCEL_SHOP_EXCEPTION,
                $e
            );
        }

        foreach ($parcel->getOpeningHours() as $openingHours) {
            $parcelShopWorkHours = new DPDShopWorkHours();
            $parcelShopWorkHours->parcel_shop_id = $parcel->getParcelShopId();
            $parcelShopWorkHours->week_day = $openingHours->weekday;
            $parcelShopWorkHours->open_morning = $openingHours->openMorning;
            $parcelShopWorkHours->close_morning = $openingHours->closeMorning;
            $parcelShopWorkHours->open_afternoon = $openingHours->openAfternoon;
            $parcelShopWorkHours->close_afternoon = $openingHours->closeAfternoon;

            try {
                $parcelShopWorkHours->add();
            } catch (Exception $e) {
                throw new EntityAddException(
                    'Failed to add parcel shop work hours',
                    EntityAddException::DPD_PARCEL_SHOP_WORK_HOURS_EXCEPTION,
                    $e
                );
            }
        }

        return true;
    }

    /**
     *This function is needed for prestashop versions below 1704 as API response loses object instance
     *
     * @param $parcel
     *
     * @return ParcelShop
     */
    private function resetParcelObject($parcel)
    {
        $parcelShop = new ParcelShop();
        $parcelShop->setParcelShopId($parcel->parcelshop_id);
        $parcelShop->setCompany($parcel->company);
        $parcelShop->setCountry($parcel->country);
        $parcelShop->setCity($parcel->city);
        $parcelShop->setPCode($parcel->pcode);
        $parcelShop->setStreet($parcel->street);
        $parcelShop->setEmail($parcel->email);
        $parcelShop->setPhone($parcel->phone);
        $parcelShop->setDistance($parcel->distance);
        $parcelShop->setLongitude($parcel->longitude);
        $parcelShop->setLatitude($parcel->latitude);
        $parcelShop->setCoordinateX($parcel->coordinateX);q
        $parcelShop->setCoordinateY($parcel->coordinateY);
        $parcelShop->setCoordinateZ($parcel->coordinateZ);
        $parcelShop->setOpeningHours($parcel->openingHours);

        return $parcelShop;
    }
}