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

use Invertus\dpdBaltics\Builder\Template\Admin\InfoBlockBuilder;
use Smarty;

class InfoBlockRender
{

    /**
     * @var InfoBlockBuilder
     */
    private $infoBlockBuilder;

    /**
     * @var Smarty
     */
    private $smarty;

    public function __construct(InfoBlockBuilder $infoBlockBuilder, Smarty $smarty)
    {
        $this->infoBlockBuilder = $infoBlockBuilder;
        $this->smarty = $smarty;
    }
    public function getInfoBlockTemplate($infoBlockText)
    {
        $this->infoBlockBuilder->setSmarty($this->smarty);
        $this->infoBlockBuilder->setInfoBlockText($infoBlockText);

        return $this->infoBlockBuilder->render();
    }
}
