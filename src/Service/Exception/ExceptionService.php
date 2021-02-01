<?php

namespace Invertus\dpdBaltics\Service\Exception;

use DPDBaltics;
use Exception;
use Invertus\dpdBaltics\Validate\ShipmentData\Exception\InvalidShipmentDataField;
use Invertus\dpdBalticsApi\Exception\DPDBalticsAPIException;

class ExceptionService
{
    const SHORT_CLASS_NAME = 'ExceptionService';

    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    public function getAPIErrorMessages()
    {
        return [
            DPDBalticsAPIException::class => [
                DPDBalticsAPIException::PARCEL_SHOP_SEARCH =>
                $this->module->l('Failed to find parcel shops', self::SHORT_CLASS_NAME),
                DPDBalticsAPIException::SHIPMENT_CREATION =>
                $this->module->l('Failed to create shipment', self::SHORT_CLASS_NAME),
                DPDBalticsAPIException::PARCEL_PRINT =>
                $this->module->l('Failed to print parcel label', self::SHORT_CLASS_NAME),
                DPDBalticsAPIException::CLOSING_MANIFEST =>
                    $this->module->l('Failed to close manifest', self::SHORT_CLASS_NAME),
                DPDBalticsAPIException::COLLECTION_REQUEST =>
                $this->module->l('Failed to create collection request', self::SHORT_CLASS_NAME),
                DPDBalticsAPIException::COURIER_REQUEST =>
                $this->module->l('Failed to create courier request', self::SHORT_CLASS_NAME),
            ]
        ];
    }

    public function getShipmentFieldErrorMessages()
    {
        return [
            InvalidShipmentDataField::class => [
                InvalidShipmentDataField::ERROR_MESSAGE_CODE_INVALID_PRODUCT =>
                $this->module->l('Shipment data field \'Product\' is invalid', self::SHORT_CLASS_NAME)
            ]
        ];
    }

    public function getErrorMessageForException(Exception $exception, array $messages)
    {
        $exceptionType = get_class($exception);
        $exceptionCode = $exception->getCode();
        $exceptionMesage = $exception->getMessage();
        if (isset($messages[$exceptionType])) {
            $message = $messages[$exceptionType];

            if (is_string($message)) {
                return $message. ' - '. $exceptionMesage;
            }

            if (is_array($message) && isset($message[$exceptionCode])) {
                return $message[$exceptionCode]. ' - '. $exceptionMesage;
            }
        }

        return $this->module->l('Unknown exception in DPDBaltics', self::SHORT_CLASS_NAME);
    }
}