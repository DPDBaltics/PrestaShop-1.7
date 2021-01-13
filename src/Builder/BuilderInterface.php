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

namespace Invertus\dpdBaltics\Builder;

/**
 * Interface DPDBuilderInterface - holds the interface required for builders to execute
 */
interface BuilderInterface
{
    /**
     * saves the builder result.
     */
    public function save();

    /**
     * gets errors. Collect them during save process
     * @return array
     */
    public function getErrors();


    /**
     * checks if the builder has errors or no.
     * @return bool
     */
    public function hasErrors();
}
