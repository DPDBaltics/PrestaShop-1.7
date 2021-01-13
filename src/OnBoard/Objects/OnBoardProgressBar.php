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

class OnBoardProgressBar
{
    /**
     * @var int
     */
    public $sections;

    /**
     * @var int
     */
    public $currentSection;

    /**
     * @var int
     */
    public $currentStep;

    /**
     * @var int
     */
    public $truckProgressClass;

    public function __construct($sections, $currentSection, $currentStep, $truckProgressClass)
    {
        $this->sections = $sections;
        $this->currentSection = $currentSection;
        $this->currentStep = $currentStep;
        $this->truckProgressClass = $truckProgressClass;
    }
}
