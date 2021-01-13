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

namespace Invertus\dpdBaltics\OnBoard\Provider;

use Invertus\dpdBaltics\OnBoard\OnBoardStepInterface;

class OnBoardStepStrategyProvider
{
    /**
     * @var OnBoardStepInterface[]
     */
    private $onBoardStepsArray;

    /**
     * @var OnBoardStepInterface
     */
    private $step;

    public function __construct(OnBoardStepInterface ...$onBoardStepsArray)
    {
        $this->onBoardStepsArray = $onBoardStepsArray;
    }

    public function setOnBoardStrategyByStep($currentStep)
    {
        foreach ($this->onBoardStepsArray as $onBoardStep) {
            if ($onBoardStep->checkIfRightStep($currentStep)) {
                $this->step = $onBoardStep;
                break;
            }
        }
    }

    public function takeStepData()
    {
        if (!isset($this->step)) {
            return false;
        }

        $response = $this->step->takeStepData();

        return $response;
    }

    public function takeStepAction()
    {
        if (!isset($this->step)) {
            return false;
        }

        $this->step->takeStepAction();
    }
}
