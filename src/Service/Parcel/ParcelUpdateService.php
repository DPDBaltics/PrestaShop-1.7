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

namespace Invertus\dpdBaltics\Service\Parcel;

use Db;
use EntityAddException;
use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBalticsApi\Api\DTO\Object\ParcelShop;
use Psr\Log\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ParcelUpdateService
{
    const BATCH_SIZE = 100;

    /**
     * @var ParcelShopRepository
     */
    private $parcelShopRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ParcelShopRepository $parcelShopRepository, LoggerInterface $logger)
    {
        $this->parcelShopRepository = $parcelShopRepository;
        $this->logger = $logger;
    }

    /**
     * Update parcels using batch insert for better performance
     *
     * @param array $parcels
     * @param string $countryCode
     * @return bool
     * @throws EntityAddException
     */
    public function updateParcels(array $parcels, $countryCode)
    {
        $isDeleteSuccess = $this->parcelShopRepository->deleteShopsByCountryCode($countryCode);

        if (!$isDeleteSuccess) {
            $this->logger->error(sprintf(
                '[ParcelUpdate] FAILED to delete existing shops for %s',
                $countryCode
            ));
            return false;
        }

        $shopsBatch = [];
        $workHoursBatch = [];

        foreach ($parcels as $parcel) {
            if (!($parcel instanceof ParcelShop)) {
                $parcel = $this->resetParcelObject($parcel);
            }

            $shopsBatch[] = $this->prepareShopData($parcel);

            $openingHours = $parcel->getOpeningHours();
            if (is_array($openingHours) && !empty($openingHours)) {
                foreach ($openingHours as $openingHoursItem) {
                    $workHoursBatch[] = $this->prepareWorkHoursData($parcel->getParcelShopId(), $openingHoursItem);
                }
            }

            if (count($shopsBatch) >= self::BATCH_SIZE) {
                $this->insertShopsBatch($shopsBatch);
                $shopsBatch = [];
            }

            if (count($workHoursBatch) >= self::BATCH_SIZE * 7) {
                $this->insertWorkHoursBatch($workHoursBatch);
                $workHoursBatch = [];
            }
        }

        if (!empty($shopsBatch)) {
            $this->insertShopsBatch($shopsBatch);
        }

        if (!empty($workHoursBatch)) {
            $this->insertWorkHoursBatch($workHoursBatch);
        }

        return true;
    }

    /**
     * Prepare shop data for batch insert
     *
     * @param ParcelShop $parcel
     * @return array
     */
    private function prepareShopData(ParcelShop $parcel)
    {
        return [
            'parcel_shop_id' => pSQL($parcel->getParcelShopId()),
            'company' => pSQL($parcel->getCompany()),
            'country' => pSQL($parcel->getCountry()),
            'city' => pSQL($parcel->getCity()),
            'p_code' => pSQL($parcel->getPCode()),
            'street' => pSQL($parcel->getStreet()),
            'email' => pSQL($parcel->getEmail()),
            'phone' => pSQL($parcel->getPhone()),
            'longitude' => pSQL($parcel->getLongitude()),
            'latitude' => pSQL($parcel->getLatitude()),
        ];
    }

    /**
     * Prepare work hours data for batch insert
     *
     * @param string $parcelShopId
     * @param object $openingHours
     * @return array
     */
    private function prepareWorkHoursData($parcelShopId, $openingHours)
    {
        return [
            'parcel_shop_id' => pSQL($parcelShopId),
            'week_day' => pSQL($openingHours->weekday),
            'open_morning' => pSQL($openingHours->openMorning),
            'close_morning' => pSQL($openingHours->closeMorning),
            'open_afternoon' => pSQL($openingHours->openAfternoon),
            'close_afternoon' => pSQL($openingHours->closeAfternoon),
        ];
    }

    /**
     * Insert shops batch
     *
     * @param array $batch
     * @throws EntityAddException
     */
    private function insertShopsBatch(array $batch)
    {
        if (empty($batch)) {
            return;
        }

        $result = Db::getInstance()->insert('dpd_shop', $batch);

        if (!$result) {
            $this->logger->error(sprintf(
                '[ParcelUpdate] FAILED to insert shops batch | Batch size: %d | DB error: %s',
                count($batch),
                Db::getInstance()->getMsgError()
            ));

            throw new EntityAddException(
                'Failed to add parcel shops batch: ' . Db::getInstance()->getMsgError(),
                EntityAddException::DPD_PARCEL_SHOP_EXCEPTION
            );
        }
    }

    /**
     * Insert work hours batch
     *
     * @param array $batch
     * @throws EntityAddException
     */
    private function insertWorkHoursBatch(array $batch)
    {
        if (empty($batch)) {
            return;
        }

        $result = Db::getInstance()->insert('dpd_shop_work_hours', $batch);

        if (!$result) {
            $this->logger->error(sprintf(
                '[ParcelUpdate] FAILED to insert work hours batch | Batch size: %d | DB error: %s',
                count($batch),
                Db::getInstance()->getMsgError()
            ));

            throw new EntityAddException(
                'Failed to add parcel shop work hours batch: ' . Db::getInstance()->getMsgError(),
                EntityAddException::DPD_PARCEL_SHOP_WORK_HOURS_EXCEPTION
            );
        }
    }

    /**
     * This function is needed for prestashop versions below 1704 as API response loses object instance
     *
     * @param $parcel
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
        $parcelShop->setCoordinateX($parcel->coordinateX);
        $parcelShop->setCoordinateY($parcel->coordinateY);
        $parcelShop->setCoordinateZ($parcel->coordinateZ);
        $parcelShop->setOpeningHours($parcel->openingHours);

        return $parcelShop;
    }
}
