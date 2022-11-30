<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */


namespace Invertus\dpdBaltics\Builder\Template;

use DPDBaltics;
use Invertus\dpdBaltics\Plugin\SearchBox\Chosen;
use Invertus\dpdBaltics\Plugin\SearchBox\DPDSearchBoxPluginFactory;
use Smarty;

class SearchBoxBuilder
{
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var Smarty
     */
    private $smarty;

    public function __construct(DPDBaltics $module, Smarty $smarty)
    {
        $this->module = $module;
        $this->smarty = $smarty;
    }

    public function createSearchBox($availableOptions, $isAllZonesSelected, $name, $isDisabled = false)
    {
        /** @var Chosen $searchBoxPlugin */
        $searchBoxPlugin = DPDSearchBoxPluginFactory::create($this->module, $this->smarty);
        $searchBoxPlugin->setAllSelected($isAllZonesSelected);
        $searchBoxPlugin->setAvailableOptions($availableOptions);
        $searchBoxPlugin->setName($name);
        $searchBoxPlugin->setDisabled($isDisabled);
        $searchBoxPlugin->setBootstrapCol(12);
        $searchBoxPlugin->setTemplatePath($this->module->getLocalPath().'views/templates/admin/search-block.tpl');

        return $searchBoxPlugin;
    }
}
