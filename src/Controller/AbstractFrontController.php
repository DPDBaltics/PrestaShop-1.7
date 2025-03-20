<?php

namespace Invertus\dpdbaltics\Controller;

class AbstractFrontController extends \ModuleFrontController
{
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        parent::ajaxDie($value, $controller, $method);

        exit();
    }
}