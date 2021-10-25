<?php

namespace Invertus\dpdBaltics\Validate\Phone;

use DPDBaltics;
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
                $this->module->l('Phone number prefix is empty'),
                400
            );
        }
        if (empty($phone)) {
            throw new DpdCarrierException(
                $this->module->l('Phone number is empty'),
                400
            );
        }
       if (!is_numeric($phone)) {
           throw new DpdCarrierException(
               $this->module->l('Phone number contains invalid characters'),
               400
           );
       }

       if (strlen($phone) < 8 || strlen($phone) > 13) {
           throw new DpdCarrierException(
               $this->module->l('Phone number length is invalid'),
               400
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

