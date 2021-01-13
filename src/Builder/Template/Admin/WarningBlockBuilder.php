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

namespace Invertus\dpdBaltics\Builder\Template\Admin;

use DPDBaltics;
use Invertus\dpdBaltics\Builder\Template\TemplateBuilderInterface;
use Smarty;

class WarningBlockBuilder implements TemplateBuilderInterface
{
    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * Info Block Text Variable Declaration.
     *
     * @var string
     */
    private $infoBlockText;

    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * DPDInfoBlockBuilder constructor.
     *
     * @param DPDBaltics $module
     */
    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    /**
     * @param Smarty $smarty
     *
     * @return DPDTemplateBuilderInterface|void
     */
    public function setSmarty(Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    public function setInfoBlockText($infoBlockText)
    {
        $this->infoBlockText = $infoBlockText;
    }

    public function render()
    {
        $this->smarty->assign([
            'warningBlockText' => $this->infoBlockText
        ]);
        $path = $this->module->getLocalPath().'views/templates/admin/warning-block.tpl';

        return $this->smarty->fetch($path);
    }
}
