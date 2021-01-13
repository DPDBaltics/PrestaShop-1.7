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

interface OnBoardStepInterface
{
    /**
     * Checks if this step is need to be loaded
     *
     * @param $currentStep
     * @return bool
     */
    public function checkIfRightStep($currentStep);

    /**
     * takes data that is required for step
     */
    public function takeStepData();

    /**
     * takes action that is required for step
     */
    public function takeStepAction();
}
