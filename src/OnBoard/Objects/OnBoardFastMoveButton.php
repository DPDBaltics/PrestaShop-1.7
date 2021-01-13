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

use Invertus\dpdBaltics\Config\Config;

class OnBoardFastMoveButton
{
    /**
     * @var string
     */
    public $step;

    /**
     * @var string
     */
    public $direction;

    public function __construct($step, $direction)
    {
        if (!in_array($direction, array(Config::STEP_FAST_MOVE_FORWARD, Config::STEP_FAST_MOVE_BACKWARD))) {
            throw new Exception('Wrong direction provided for fast step button');
        }

        $this->step = $step;
        $this->direction = $direction;
    }
}
