<?php

namespace Invertus\dpdBaltics\Controller;

class AbstractFrontController extends \ModuleFrontController
{
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        parent::ajaxRender($value, $controller, $method);

        exit();
    }
}