<?php

namespace Invertus\dpdBaltics\Infrastructure\Adapter;

use Module;

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