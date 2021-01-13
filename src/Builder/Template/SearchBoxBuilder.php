<?php

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
