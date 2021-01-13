<?php

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
