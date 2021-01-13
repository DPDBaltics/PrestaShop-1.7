<?php

namespace Invertus\dpdBaltics\Presenter;

use Context;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

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