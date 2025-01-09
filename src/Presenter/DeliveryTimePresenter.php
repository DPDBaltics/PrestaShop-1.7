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


namespace Invertus\dpdBaltics\Presenter;

use Context;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DeliveryTimePresenter
{
    /**
     * @var Context
     */
    private $context;
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(Context $context, DPDBaltics $module)
    {
        $this->context = $context;
        $this->module = $module;
    }

    public function getDeliveryTimeTemplate($countryIso, $city)
    {
        $supportedDeliveryTimeCities = Config::getTimeFrameCountries($countryIso);
        if (!in_array(strtolower($city), $supportedDeliveryTimeCities)) {
            return '';
        }

        $this->context->smarty->assign(
            [
                'deliveryTimes' => Config::getDeliveryTimes($countryIso),
                'selectedDeliveryTime' => '',
            ]
        );

        return $this->context->smarty->fetch(
            $this->module->getLocalPath().'/views/templates/hook/front/carrier-delivery-time.tpl'
        );
    }
}