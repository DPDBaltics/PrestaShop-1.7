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
        if (empty($prefix) && empty($phone)) {
            throw new DpdCarrierException(
                'Phone number details are empty',
                Config::ERROR_EMPTY_PHONE_DETAILS
            );
        } else {

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

            if (strlen($phone) < 6 || strlen($phone) > 13) {
                throw new DpdCarrierException(
                    'Phone number length is invalid',
                    Config::ERROR_PHONE_HAS_INVALID_LENGTH
                );
            }
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

