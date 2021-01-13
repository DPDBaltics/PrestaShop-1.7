<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\OnBoard;

use DPDBaltics;
use Invertus\dpdBaltics\OnBoard\Service\OnBoardStepActionService;
use Invertus\dpdBaltics\OnBoard\Service\OnBoardStepDataService;

abstract class AbstractOnBoardStep implements OnBoardStepInterface
{
    /**
     * @var DPDBaltics
     */
    protected $module;

    /**
     * @var OnBoardStepActionService
     */
    protected $stepActionService;

    /**
     * @var OnBoardStepDataService
     */
    protected $stepDataService;

    public function __construct(
        DPDBaltics $module,
        OnBoardStepActionService $stepActionService,
        OnBoardStepDataService $stepDataService
    ) {
        $this->module = $module;
        $this->stepActionService = $stepActionService;
        $this->stepDataService = $stepDataService;
    }
}