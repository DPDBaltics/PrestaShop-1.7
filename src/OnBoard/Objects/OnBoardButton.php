<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\OnBoard\Objects;

class OnBoardButton
{
    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $nextStep;

    /**
     * @var string
     */
    public $validateField;

    public function __construct($text, $class = null, $nextStep = null, $validateField = null)
    {
        $this->text = $text;
        $this->class = $class;
        $this->nextStep = $nextStep;
        $this->validateField = $validateField;
    }
}
