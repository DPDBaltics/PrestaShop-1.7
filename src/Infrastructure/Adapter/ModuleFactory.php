<?php

namespace Invertus\dpdBaltics\Infrastructure\Adapter;

use Module;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModuleFactory
{
    /**
     * @return \DPDBaltics|false|Module|null
     */
    public function getModule()
    {
        return Module::getInstanceByName('dpdbaltics') ?: null;
    }
}