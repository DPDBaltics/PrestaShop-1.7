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

namespace Invertus\dpdBaltics\Builder\Template;

use Smarty;

interface TemplateBuilderInterface
{
    /**
     * Set smarty as templating engline provider
     *
     * @param Smarty $smarty
     *
     * @return self
     */
    public function setSmarty(Smarty $smarty);

    /**
     * Returns rendered html
     *
     * @return string
     */
    public function render();
}
