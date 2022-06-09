<?php

namespace Invertus\dpdBaltics\Service\API;

use Address;
use Country;
use DPDAddressTemplate;
use DPDProduct;
use Invertus\dpdBaltics\Adapter\AddressAdapter;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Repository\CodPaymentRepository;
use Invertus\dpdBaltics\Service\Parcel\ParcelShopService;
use Invertus\dpdBalticsApi\Api\DTO\Request\ShipmentCreationRequest;
use Invertus\dpdBalticsApi\Factory\APIRequest\ShipmentCreationFactory;
use Invertus\dpdBaltics\Service\Email\Handler\ParcelTrackingEmailHandler;
use Invertus\dpdBaltics\Util\StringUtility;
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
    /**
     * @var ParcelTrackingEmailHandler
     */
    private $emailHandler;
    /**
     * @var ParcelShopService
     */
    private $parcelShopService;
    /**
     * @var AddressAdapter
     */
    private $addressAdapter;

    public function __construct(
        ShipmentCreationFactory $shipmentCreationFactory,
        CodPaymentRepository $codPaymentRepository,
        ParcelTrackingEmailHandler $emailHandler,
        ParcelShopService $parcelShopService,
        AddressAdapter $addressAdapter
    ) {
        $this->shipmentCreationFactory = $shipmentCreationFactory;
        $this->codPaymentRepository = $codPaymentRepository;
        $this->emailHandler = $emailHandler;
        $this->parcelShopService = $parcelShopService;
        $this->addressAdapter = $addressAdapter;
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \Invertus\dpdBaltics\Exception\ParcelEmailException
     * @throws \SmartyException
     */
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
        $country = Country::getIsoById($address->id_country);
        $postCode = $address->postcode;
        $hasAddressFields = (bool) !$postCode || !$firstName || !$address->city || !$country;

        // IF prestashop allows, we take selected parcel terminal address in case information is missing in checkout address in specific cases.
        if (($hasAddressFields) && $shipmentData->isPudo()) {
            $parcel = $this->parcelShopService->getParcelShopByShopId($shipmentData->getSelectedPudoId());
            $selectedParcel = is_array($parcel) ? reset($parcel) : $parcel;
            $firstName = $selectedParcel->getCompany();
            $postCode = $selectedParcel->getPCode();
            $address->address1 = $selectedParcel->getStreet();
            $address->city = $selectedParcel->getCity();
            $country = $selectedParcel->getCountry();
        }

        $postCode = $this->addressAdapter->formatPostCodeByCountry($postCode, $country);

        $shipmentCreationRequest = new ShipmentCreationRequest(
            $firstName,
            $address->address1,
            $address->city,
            $country,
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
            $trimmedRemarkMessage = StringUtility::trimString($cartMessage[0]['message']);
            $shipmentCreationRequest->setRemark(StringUtility::removeSpecialCharacters($trimmedRemarkMessage));
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

        $shipmentResponse = $shipmentCreator->createShipment($shipmentCreationRequest);

        if ($shipmentResponse->getStatus() === "ok" && $this->isTrackingEmailAllowed()) {
            $this->emailHandler->handle($orderId, $shipmentResponse->getPlNumber());
        }

        return $shipmentResponse;
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

    private function isTrackingEmailAllowed()
    {
        return (bool) \Configuration::get(Config::SEND_EMAIL_ON_PARCEL_CREATION);
    }

}
