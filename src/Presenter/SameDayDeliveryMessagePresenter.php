<?php

namespace Invertus\dpdBaltics\Presenter;

use Context;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Util\TimeZoneUtility;

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
            $this->module->getLocalPath() . '/views/templates/hook/front/carrier-same-day-delivery-message.tpl'
        );
    }
}