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

    const REMARK_LIMIT = 44;

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
            $trimmedRemarkMessage = $this->trimRemarkMessage($cartMessage[0]['message']);
            $toASCIIChars = $this->convertAccentsAndSpecialToNormal($trimmedRemarkMessage);
            var_dump($toASCIIChars);
            die;
            $shipmentCreationRequest->setRemark($this->convertMessageToASCII($trimmedRemarkMessage));
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

    private function trimRemarkMessage($message) {
        return strlen($message) > self::REMARK_LIMIT ? substr($message,0,self::REMARK_LIMIT)."..." : $message;
    }

    private function convertAccentsAndSpecialToNormal($string) {
        $table = array(
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Ă'=>'A', 'Ā'=>'A', 'Ą'=>'A', 'Æ'=>'A', 'Ǽ'=>'A',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'ă'=>'a', 'ā'=>'a', 'ą'=>'a', 'æ'=>'a', 'ǽ'=>'a',

            'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',

            'Ç'=>'C', 'Č'=>'C', 'Ć'=>'C', 'Ĉ'=>'C', 'Ċ'=>'C',
            'ç'=>'c', 'č'=>'c', 'ć'=>'c', 'ĉ'=>'c', 'ċ'=>'c',

            'Đ'=>'Dj', 'Ď'=>'D',
            'đ'=>'dj', 'ď'=>'d',

            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ĕ'=>'E', 'Ē'=>'E', 'Ę'=>'E', 'Ė'=>'E',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'ę'=>'e', 'ė'=>'e',

            'Ĝ'=>'G', 'Ğ'=>'G', 'Ġ'=>'G', 'Ģ'=>'G',
            'ĝ'=>'g', 'ğ'=>'g', 'ġ'=>'g', 'ģ'=>'g',

            'Ĥ'=>'H', 'Ħ'=>'H',
            'ĥ'=>'h', 'ħ'=>'h',

            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'Ĩ'=>'I', 'Ī'=>'I', 'Ĭ'=>'I', 'Į'=>'I',
            'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'į'=>'i', 'ĩ'=>'i', 'ī'=>'i', 'ĭ'=>'i', 'ı'=>'i',

            'Ĵ'=>'J',
            'ĵ'=>'j',

            'Ķ'=>'K',
            'ķ'=>'k', 'ĸ'=>'k',

            'Ĺ'=>'L', 'Ļ'=>'L', 'Ľ'=>'L', 'Ŀ'=>'L', 'Ł'=>'L',
            'ĺ'=>'l', 'ļ'=>'l', 'ľ'=>'l', 'ŀ'=>'l', 'ł'=>'l',

            'Ñ'=>'N', 'Ń'=>'N', 'Ň'=>'N', 'Ņ'=>'N', 'Ŋ'=>'N',
            'ñ'=>'n', 'ń'=>'n', 'ň'=>'n', 'ņ'=>'n', 'ŋ'=>'n', 'ŉ'=>'n',

            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ō'=>'O', 'Ŏ'=>'O', 'Ő'=>'O', 'Œ'=>'O',
            'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ō'=>'o', 'ŏ'=>'o', 'ő'=>'o', 'œ'=>'o', 'ð'=>'o',

            'Ŕ'=>'R', 'Ř'=>'R',
            'ŕ'=>'r', 'ř'=>'r', 'ŗ'=>'r',

            'Š'=>'S', 'Ŝ'=>'S', 'Ś'=>'S', 'Ş'=>'S',
            'š'=>'s', 'ŝ'=>'s', 'ś'=>'s', 'ş'=>'s',

            'Ŧ'=>'T', 'Ţ'=>'T', 'Ť'=>'T',
            'ŧ'=>'t', 'ţ'=>'t', 'ť'=>'t',

            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ũ'=>'U', 'Ū'=>'U', 'Ŭ'=>'U', 'Ů'=>'U', 'Ű'=>'U', 'Ų'=>'U',
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ũ'=>'u', 'ū'=>'u', 'ŭ'=>'u', 'ů'=>'u', 'ű'=>'u', 'ų'=>'u',

            'Ŵ'=>'W', 'Ẁ'=>'W', 'Ẃ'=>'W', 'Ẅ'=>'W',
            'ŵ'=>'w', 'ẁ'=>'w', 'ẃ'=>'w', 'ẅ'=>'w',

            'Ý'=>'Y', 'Ÿ'=>'Y', 'Ŷ'=>'Y',
            'ý'=>'y', 'ÿ'=>'y', 'ŷ'=>'y',

            'Ž'=>'Z', 'Ź'=>'Z', 'Ż'=>'Z',
            'ž'=>'z', 'ź'=>'z', 'ż'=>'z',

            '“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '•'=>'-', '…'=>'...', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>' degrees ',
            '¼'=>' 1/4 ', '½'=>' 1/2 ', '¾'=>' 3/4 ', '⅓'=>' 1/3 ', '⅔'=>' 2/3 ', '⅛'=>' 1/8 ', '⅜'=>' 3/8 ', '⅝'=>' 5/8 ', '⅞'=>' 7/8 ',
            '÷'=>' divided by ', '×'=>' times ', '±'=>' plus-minus ', '√'=>' square root ', '∞'=>' infinity ',
            '≈'=>' almost equal to ', '≠'=>' not equal to ', '≡'=>' identical to ', '≤'=>' less than or equal to ', '≥'=>' greater than or equal to ',
            '←'=>' left ', '→'=>' right ', '↑'=>' up ', '↓'=>' down ', '↔'=>' left and right ', '↕'=>' up and down ',
            '℅'=>' care of ', '℮' => ' estimated ',
            'Ω'=>' ohm ',
            '♀'=>' female ', '♂'=>' male ',
            '©'=>' Copyright ', '®'=>' Registered ', '™' =>' Trademark ',
        );

        $string = strtr($string, $table);
        // Currency symbols: £¤¥€  - we dont bother with them for now
        $string = preg_replace("/[^\x9\xA\xD\x20-\x7F]/u", "", $string);

        return $string;
    }
}
