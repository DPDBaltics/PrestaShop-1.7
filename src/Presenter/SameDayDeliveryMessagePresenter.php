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
use Invertus\dpdBaltics\Util\TimeZoneUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SameDayDeliveryMessagePresenter
{

    /**
     * @var \DPDBaltics
     */
    private $module;
    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context, DPDBaltics $module)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function getSameDayDeliveryMessageTemplate()
    {
        $currentTime = TimeZoneUtility::getBalticTimeZone();
        switch ($currentTime) {
            case $currentTime < Config::COURIER_SAME_DAY_TIME_LIMITATION:
                $message =
                    $this->module->l('Order will be delivered tomorrow in the first part of the day.', 'SameDayDeliveryMessagePresenter');
                break;
            default:
                $message = $this->module->l('Order will be delivered today in the second half of the day.', 'SameDayDeliveryMessagePresenter');
                break;
        }

        $this->context->smarty->assign(
            [
                'sameDayDeliveryMessage' => $message,
            ]
        );

        return $this->context->smarty->fetch(
            'module:dpdbaltics/views/templates/hook/front/carrier-same-day-delivery-message.tpl'
        );
    }
}