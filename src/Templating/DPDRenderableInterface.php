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

namespace  Invertus\dpdBaltics\Templating;

interface DPDRenderableInterface
{
    /**
     * Sets template path for rendering
     *
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath);

    /**
     * Renders the template
     *
     * @return string html
     */
    public function render();
}
