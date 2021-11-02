<?php

namespace Invertus\dpdBaltics\Validate\Phone;

use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Exception\DpdCarrierException;
use Invertus\dpdBaltics\Repository\PhoneRepository;
use Invertus\dpdBaltics\Util\NumberUtility;
use Invertus\dpdBaltics\Util\StringUtility;

class PhoneNumberValidator
{

    private $module;

    private $phoneRepository;

    public function __construct(DPDBaltics $module, PhoneRepository $phoneRepository)
    {
        $this->module = $module;
        $this->phoneRepository = $phoneRepository;
    }

    public function isPhoneValid($prefix, $phone)
    {
        if (empty($prefix)) {
            throw new DpdCarrierException(
                'Phone number prefix is empty',
                Config::ERROR_BAD_PHONE_NUMBER_PREFIX
            );
        }
        if (empty($phone)) {
            throw new DpdCarrierException(
             'Phone number is empty',
                Config::ERROR_PHONE_EMPTY
            );
        }
       if (!is_numeric($phone)) {
           throw new DpdCarrierException(
              'Phone number contains invalid characters',
               Config::ERROR_PHONE_HAS_INVALID_CHARACTERS
           );
       }

       if (strlen($phone) < 8 || strlen($phone) > 13) {
           throw new DpdCarrierException(
              'Phone number length is invalid',
               Config::ERROR_PHONE_HAS_INVALID_LENGTH
           );
       }

        return true;
    }

    public function isPhoneAddedInOrder($idCart)
    {
        $id = $this->phoneRepository->getOrderPhoneIdByCartId($idCart);
        $dpdPhone = $this->phoneRepository->findDpdOrderPhone($id);

        return $this->isPhoneValid($dpdPhone->phone_area, $dpdPhone->phone);
    }
}

