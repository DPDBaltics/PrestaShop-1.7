<?php

namespace Invertus\dpdBaltics\Uninstaller\ModuleTabs;

use Invertus\psModuleTabs\Object\Tab as ModuleTab;
use Invertus\psModuleTabs\Object\TabsCollection;
use Tab;

class ModuleTabsUninstaller
{
    /**
     * @var Tab
     */
    private $tabs;

    public function __construct($tabs)
    {
        $this->tabs = $tabs;
    }

    public function uninstallTabs()
    {

        foreach ($this->tabs as $tab) {
            $idTab = Tab::getIdFromClassName($tab->getClassName());

            if (!$idTab) {
                continue;
            }

            $tab = new Tab($idTab);
            if (!$tab->delete()) {
                return false;
            }
        }

        return true;
    }
}
