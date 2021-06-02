<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Service;

use Address;
use Cart;
use Context;
use DPDBaltics;
use Invertus\dpdBaltics\ORM\EntityManager;
use Invertus\dpdBaltics\Repository\OrderRepository;
use Invertus\dpdBaltics\Repository\PhonePrefixRepository;
use Invertus\dpdBaltics\Config\Config;
use DPDOrderPhone;

class CarrierPhoneService
{
    const LITHUANIA_FULL_PHONE_PREFIX = '370';
    const LITHUANIA_PHONE_PREFIX = '86';
    const LITHUANIA_NEW_PHONE_PREFIX = '6';
    const LITHUANIA_PHONE_LENGTH = 9;

    /** @var dpdbaltics */
    private $module;

    /** @var Context */
    private $context;

    private $entityManager;
    /**
     * @var PhonePrefixRepository
     */
    private $phonePrefixRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(
        DPDBaltics $module,
        Context $context,
        EntityManager $entityManager,
        PhonePrefixRepository $phonePrefixRepository,
        OrderRepository $orderRepository
    ) {
        $this->module = $module;
        $this->context = $context;
        $this->entityManager = $entityManager;
        $this->phonePrefixRepository = $phonePrefixRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getCarrierPhoneTemplate($cartId)
    {
        $cart = new Cart($cartId);

        $phone = '';

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->module->getModuleContainer()->get(OrderRepository::class);

        $dpdOrderPhone = $orderRepository->getPhoneByIdCart($cart->id);

        if (!empty($dpdOrderPhone)) {
            $phone = $dpdOrderPhone['phone'];
        }

        if (empty($dpdOrderPhone)) {
            $address = new Address($cart->id_address_delivery);

            $phone = $address->phone ? $address->phone : $address->phone_mobile;
        }

        $phoneData['mobile_phone_code_list'] = $this->phonePrefixRepository->getCallPrefixesFrontOffice();

        $phonePrefix = $this->context->country->call_prefix;
        $this->context->controller->addJqueryPlugin('chosen');
        $phone = $this->removePhonePrefix($phone, $phonePrefix);
        $this->context->smarty->assign(
            [
                'dpdPhone' => $phone,
                'dpdPhoneArea' => $phoneData['mobile_phone_code_list'],
                'contextPrefix' => Config::PHONE_CODE_PREFIX . $phonePrefix,
                'isAbove177' => Config::isPrestashopVersionAbove177(),
            ]
        );

        return $this->context->smarty->fetch(
            $this->module->getLocalPath().'/views/templates/hook/front/carrier-phone-number.tpl'
        );
    }

    public function saveCarrierPhone($idCart, $dpdPhone, $dpdPhoneCode)
    {
        $idDpdOrderPhone = $this->orderRepository->getOrderPhoneIdByCartId($idCart);

        if (!$idDpdOrderPhone) {
            $dpdOrderPhone = new DPDOrderPhone();
        } else {
            $dpdOrderPhone = new DPDOrderPhone($idDpdOrderPhone);
        }

        $dpdOrderPhone->phone = $dpdPhone;
        $dpdOrderPhone->phone_area = $dpdPhoneCode;
        $dpdOrderPhone->id_cart = $idCart;

        if (!$dpdOrderPhone->save()) {
            return false;
        }

        return true;
    }

    public function removePhonePrefix($phone, $phonePrefix)
    {
        $prefixLength = strlen($phonePrefix);
        if (strpos($phone, $phonePrefix) === 0) {
            return substr($phone, $prefixLength);
        }
        if (strpos($phone, Config::PHONE_CODE_PREFIX . $phonePrefix) === 0) {
            return substr($phone, $prefixLength + strlen(Config::PHONE_CODE_PREFIX));
        }
        if ($phonePrefix === $this::LITHUANIA_FULL_PHONE_PREFIX && strlen($phone) === $this::LITHUANIA_PHONE_LENGTH) {
            return $this::LITHUANIA_NEW_PHONE_PREFIX . substr($phone, strlen($this::LITHUANIA_PHONE_PREFIX));
        }

        return $phone;
    }
}
