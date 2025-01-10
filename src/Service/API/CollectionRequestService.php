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


namespace Invertus\dpdBaltics\Service\API;

use Country;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\CollectionRequestData;
use Invertus\dpdBalticsApi\Api\DTO\Request\CollectionRequestRequest;
use Invertus\dpdBalticsApi\Api\DTO\Response\CollectionRequestResponse;
use Invertus\dpdBalticsApi\Factory\APIRequest\CollectionRequestFactory;
use Symfony\Component\Config\Definition\Exception\Exception;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CollectionRequestService
{
    const FILE_NAME = 'CollectionRequestService';

    /**
     * @var CollectionRequestFactory
     */
    private $collectionRequestFactory;
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(CollectionRequestFactory $collectionRequestFactory, DPDBaltics $module)
    {
        $this->collectionRequestFactory = $collectionRequestFactory;
        $this->module = $module;
    }

    public function createCollectionRequest(CollectionRequestData $collectionRequestData)
    {
        $senderIsoCode = Country::getIsoById($collectionRequestData->getPickupAddressIdWsCountry());
        $receiverIsoCode = Country::getIsoById($collectionRequestData->getPickupAddressIdWsCountry());
        $pickupPostCode = preg_replace(
            '/[^0-9]/',
            '',
            $collectionRequestData->getPickupAddressZipCode()
        );
        $receiverPostCode = preg_replace(
            '/[^0-9]/',
            '',
            $collectionRequestData->getReceiverAddressZipCode()
        );
        $request = new CollectionRequestRequest(
            $collectionRequestData->getPickupAddressFullName(),
            $collectionRequestData->getPickupAddressAddress(),
            $senderIsoCode,
            $pickupPostCode,
            $collectionRequestData->getPickupAddressCity(),
            $collectionRequestData->getInfo1(),
            $collectionRequestData->getReceiverAddressFullName(),
            $collectionRequestData->getReceiverAddressAddress(),
            $receiverPostCode,
            $receiverIsoCode,
            $collectionRequestData->getReceiverAddressCity()
        );

        $request = $this->setPhoneNumbers(
            $request,
            $collectionRequestData->getPickupAddressMobilePhoneCode(),
            $collectionRequestData->getPickupAddressMobilePhone(),
            $collectionRequestData->getReceiverAddressMobilePhoneCode(),
            $collectionRequestData->getReceiverAddressMobilePhone()
        );

        $request->setInfo2($collectionRequestData->getInfo2());

        $collectionRequest = $this->collectionRequestFactory->makeCollectionRequest();

        /** @var CollectionRequestResponse $response */
        $response = $collectionRequest->collectionRequest($request);

        if (!$this->checkIfCollectionRequestIsSuccess($response)) {
            return [
                'status' => false,
                'message' => $this->getCollectionRequestError($response)
            ];
        }

        return [
            'status' => true,
            'message' => $this->module->l('Collection request was successfully created!', self::FILE_NAME)
        ];
    }

    private function setPhoneNumbers(
        CollectionRequestRequest $collectionRequestRequest,
        $senderPhoneCode,
        $senderPhoneNumber,
        $receiverPhoneCode,
        $receiverPhoneNumber
    ) {
        $senderPhone = $senderPhoneCode . $senderPhoneNumber;
        $receiverPhone = $receiverPhoneCode . $receiverPhoneNumber;
        $collectionRequestRequest->setCphone($senderPhone);
        $collectionRequestRequest->setRphone($receiverPhone);

        return $collectionRequestRequest;
    }

    private function checkIfCollectionRequestIsSuccess($collectionRequestResponse)
    {
        if (strpos($collectionRequestResponse, Config::API_COLLECTION_REQUEST_SUCCESS_STATUS)) {
            return true;
        }

        return false;
    }

    private function getCollectionRequestError($collectionRequestResponse)
    {
        $errorPosition = strpos(
            $collectionRequestResponse,
            Config::API_COLLECTION_REQUEST_ERROR_STATUS
        );

        return substr($collectionRequestResponse, $errorPosition);
    }
}
