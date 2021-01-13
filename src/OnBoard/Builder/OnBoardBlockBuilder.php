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

namespace Invertus\dpdBaltics\OnBoard\Builder;

use DPDBaltics;
use Smarty;

class OnBoardBlockBuilder
{
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * OnBoardBlockBuilder constructor.
     *
     * @param DPDBaltics $module
     * @param Smarty $smarty
     */
    public function __construct(DPDBaltics $module, Smarty $smarty)
    {
        $this->module = $module;
        $this->smarty = $smarty;
    }

    public function render($onBoardTemplateData, $ajaxNextStep = false)
    {
        $this->smarty->assign(array(
            'dpdPathUri' => $this->module->getPathUri(),
            'onBoardTemplateData' => $onBoardTemplateData,
        ));

        if ($ajaxNextStep) {
            $pathOnBoard = $this->module->getLocalPath() . 'views/templates/admin/on-board/on-board-pop-up.tpl';
            $pathProgressBar = $this->module->getLocalPath() . 'views/templates/admin/on-board/on-board-progress.tpl';

            $template['onBoardTemplate'] = $this->smarty->fetch($pathOnBoard);

            if ($onBoardTemplateData['progressBar']) {
                $template['progressBarTemplate'] = $this->smarty->fetch($pathProgressBar);
            }

            return $template;
        } else {
            return $this->smarty->fetch(
                $this->module->getLocalPath() . 'views/templates/admin/on-board/on-board-section.tpl'
            );
        }
    }
}
