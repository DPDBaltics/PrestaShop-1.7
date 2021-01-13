<?php

namespace Invertus\dpdBaltics\Service\API;

use Address;
use Country;
use DPDAddressTemplate;
use DPDProduct;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Repository\CodPaymentRepository;
use Invertus\dpdBalticsApi\Api\DTO\Request\ShipmentCreationRequest;
use Invertus\dpdBalticsApi\Factory\APIRequest\ShipmentCreationFactory;
use Message;

class ShipmentApiService
{

    /**
     * @var ShipmentCreationFactory
     */
    private $shipmentCreationFactory;
    /**
     * @var CodPaymentRepository
     */
    private $codPaymentRepository;

    public function __construct(
        ShipmentCreationFactory $shipmentCreationFactory,
        CodPaymentRepository $codPaymentRepository
    ) {
        $this->shipmentCreationFactory = $shipmentCreationFactory;
        $this->codPaymentRepository = $codPaymentRepository;
    }

    public function createShipment($addressId, ShipmentData $shipmentData, $orderId)
    {
        $address = new Address($addressId);
        $isCompany = $shipmentData->getCompany() ? true : false;
        $firstName = $shipmentData->getName();
        if ($isCompany) {
            $firstName = $shipmentData->getCompany();
        }
        $phoneNumber = $shipmentData->getPhoneArea() . $shipmentData->getPhone();
        $dpdProduct = new DPDProduct($shipmentData->getProduct());
        $parcelType = $dpdProduct->getProductReference();

        $postCode = preg_replace('/[^0-9]/', '', $address->postcode);
        $shipmentCreationRequest = new ShipmentCreationRequest(
            $firstName,
            $address->address1,
            $address->city,
            Country::getIsoById($address->id_country),
            $postCode,
            $shipmentData->getParcelAmount(),
            $parcelType,
            $phoneNumber,
            $shipmentData->getEmail(),
            1
        );

        if (!$isCompany) {
            $shipmentCreationRequest->setName2($address->lastname);
        }

        $shipmentCreationRequest = $this->setNotRequiredData($shipmentCreationRequest, $shipmentData);

        if ($dpdProduct->is_cod) {
            $shipmentCreationRequest->setCodAmount($shipmentData->getGoodsPrice());
        }

        $cartMessage = Message::getMessagesByOrderId($orderId);
        if ($cartMessage)
        {
            $shipmentCreationRequest->setRemark($cartMessage[0]['message']);
        }

        if ($shipmentData->getSelectedPudoId()) {
            $shipmentCreationRequest = $this->setPudoData($shipmentCreationRequest, $shipmentData);
        }

        if (!$dpdProduct->is_pudo && $shipmentData->isDpdDocumentReturn()) {
            $shipmentCreationRequest->setParcelType($parcelType . Config::DOCUMENT_RETURN_CODE);
            $shipmentCreationRequest->setDnoteReference($shipmentData->getDpdDocumentReturnNumber());
        }

        if ($shipmentData->getDeliveryTime()) {
            $timeFrames = explode('-', $shipmentData->getDeliveryTime());
            $shipmentCreationRequest->setTimeFrameFrom($timeFrames[0]);
            $shipmentCreationRequest->setTimeFrameTo($timeFrames[1]);
        }
        $shipmentCreator = $this->shipmentCreationFactory->makeShipmentCreation();

        return $shipmentCreator->createShipment($shipmentCreationRequest);
    }

    public function createReturnServiceShipment($addressTemplateId)
    {
        $dpdAddressTemplate = new DPDAddressTemplate($addressTemplateId);

        $phoneNumber = $dpdAddressTemplate->mobile_phone_code . $dpdAddressTemplate->mobile_phone;
        $parcelType = 'RET-RETURN';

        $postCode = preg_replace('/[^0-9]/', '', $dpdAddressTemplate->zip_code);
        $shipmentCreationRequest = new ShipmentCreationRequest(
            $dpdAddressTemplate->full_name,
            $dpdAddressTemplate->address,
            $dpdAddressTemplate->dpd_city_name,
            Country::getIsoById($dpdAddressTemplate->dpd_country_id),
            $postCode,
            '1',
            $parcelType,
            $phoneNumber,
            $dpdAddressTemplate->email,
            1
        );
        $shipmentCreator = $this->shipmentCreationFactory->makeShipmentCreation();

        return $shipmentCreator->createShipment($shipmentCreationRequest);
    }

    private function setNotRequiredData(ShipmentCreationRequest $shipmentCreationRequest, ShipmentData $shipmentData)
    {
        $shipmentCreationRequest->setOrderNumber($shipmentData->getReference1());
        $shipmentCreationRequest->setOrderNumber1($shipmentData->getReference2());
        $shipmentCreationRequest->setOrderNumber2($shipmentData->getReference3());
        $shipmentCreationRequest->setOrderNumber3($shipmentData->getReference4());
        $shipmentCreationRequest->setWeight($shipmentData->getWeight());
        $shipmentCreationRequest->setIdmSmsNumber($shipmentData->getPhone());
        $shipmentCreationRequest->setOrderNumber($shipmentData->getReference1());

        return $shipmentCreationRequest;
    }

    private function setPudoData(ShipmentCreationRequest $shipmentCreationRequest, ShipmentData $shipmentData)
    {
        $shipmentCreationRequest->setParcelShopId($shipmentData->getSelectedPudoId());

        return $shipmentCreationRequest;
    }
}
