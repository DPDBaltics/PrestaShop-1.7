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

namespace Invertus\dpdBaltics\Plugin\SearchBox;

interface SearchBoxPluginInterface
{
    /**
     * Set text to display on the input when nothing is selected yet
     * @param $text
     */
    public function setPlaceholderText($text);

    /**
     * Set the name of search box plugin - used in $_POST
     * @param string $name
     */
    public function setName($name);

    /**
     * Set disabled of search box plugin - used in $_POST
     * @param string $name
     */
    public function setDisabled($disabled);

    /**
     * Set options that are available to select
     * @param array $availableOptions
     */
    public function setAvailableOptions(array $availableOptions);

    /**
     * Set's the "All selected" option value
     * @param bool $allSelected
     */
    public function setAllSelected($allSelected);

    /**
     * Set col value in bootstrap's measurement
     * @param int $col
     */
    public function setBootstrapCol($col);
}
