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

class OnBoardParagraph
{
    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $class;

    public function __construct($text, $class = null)
    {
        $this->text = $text;
        $this->class = $class;
    }
}
