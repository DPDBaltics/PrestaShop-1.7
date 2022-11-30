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
use Invertus\dpdBaltics\DTO\courierRequestData;
use Invertus\dpdBalticsApi\Api\DTO\Request\CourierRequestRequest;
use Invertus\dpdBalticsApi\Api\DTO\Response\courierRequestResponse;
use Invertus\dpdBalticsApi\Factory\APIRequest\courierRequestFactory;

class CourierRequestService
{

    /**
     * @var CourierRequestFactory
     */
    private $courierRequestFactory;
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(CourierRequestFactory $courierRequestFactory, DPDBaltics $module)
    {
        $this->courierRequestFactory = $courierRequestFactory;
        $this->module = $module;
    }

    public function createCourierRequest(CourierRequestData $courierRequestData)
    {
        $senderIsoCode = Country::getIsoById($courierRequestData->getSenderIdWsCountry());

        $postalCode = preg_replace(
            '/[^0-9]/',
            '',
            $courierRequestData->getSenderPostalCode()
        );
        
        $request = new CourierRequestRequest(
            $courierRequestData->getOrderNr(),
            $courierRequestData->getSenderAddress(),
            $courierRequestData->getSenderCity(),
            $senderIsoCode,
            $postalCode,
            $courierRequestData->getSenderName(),
            $courierRequestData->getSenderPhoneCode() . $courierRequestData->getSenderPhone(),
            $courierRequestData->getSenderWorkUntil(),
            $courierRequestData->getPickupTime(),
            $courierRequestData->getWeight(),
            $courierRequestData->getParcelsCount()
        );
        
        $courierRequest = $this->courierRequestFactory->makecourierRequest();

        /** @var courierRequestResponse $response */
        $response = $courierRequest->courierRequest($request);

        if (!$this->checkIfCourierRequestIsSuccess($response)) {
            return [
                'status' => false,
                'message' => $this->getCourierRequestError($response)
            ];
        }

        return [
            'status' => true,
            'message' => $this->module->l('courier request was successfully created!')
        ];
    }

    private function checkIfCourierRequestIsSuccess($courierRequestResponse)
    {
        if ($courierRequestResponse === Config::API_COURIER_REQUEST_SUCCESS_STATUS) {
            return true;
        }

        return false;
    }

    private function getCourierRequestError($courierRequestResponse)
    {
        $errorPosition = strpos(
            $courierRequestResponse,
            Config::API_COURIER_REQUEST_ERROR_STATUS
        );

        return substr($courierRequestResponse, $errorPosition);
    }
}
