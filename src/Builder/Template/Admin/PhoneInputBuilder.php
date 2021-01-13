<?php

namespace Invertus\dpdBaltics\Builder\Template\Admin;

use DPDBaltics;
use Smarty;

class PhoneInputBuilder
{
    /**
     * @var DPDBaltics
     */
    private $module;
    /**
     * @var Smarty
     */
    private $smarty;

    public function __construct(DPDBaltics $module, Smarty $smarty)
    {
        $this->module = $module;
        $this->smarty = $smarty;
    }

    public function renderPhoneInput($inputName, $countryCallingCodes, $code = null, $phone = null)
    {
        $this->smarty->assign(
            [
                'inputName' => $inputName,
                'phoneNumber' => $phone,
                'countryCallingCode' => $code,
                'countryCallingCodes' => $countryCallingCodes
            ]
        );

        return $this->smarty->fetch(
            $this->module->getLocalPath().'views/templates/admin/phone-and-code-inputs.tpl'
        );
    }
}
