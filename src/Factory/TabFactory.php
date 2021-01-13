<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author INVERTUS UAB www.invertus.eu  <support@invertus.eu>
 * @copyright Fruugo.com Limited
 * @license Fruugo
 */

namespace Invertus\dpdBaltics\Factory;

use Invertus\dpdBaltics\Service\TabService;
use Invertus\psModuleTabs\Object\Tab;
use Invertus\psModuleTabs\Object\TabsCollection;

class TabFactory
{
    /**
     * @var TabService
     */
    private $tabsService;

    /**
     * TabFactory constructor.
     *
     * @param TabService $tabsService
     */
    public function __construct(TabService $tabsService)
    {
        $this->tabsService = $tabsService;
    }

    /**
     * @return TabsCollection
     */
    public function getTabsCollection()
    {
        $tabsDataArray = $this->tabsService->getTabs();

        $tabsObjectsArray = [];

        foreach ($tabsDataArray as $tabData) {
            $tabsObjectsArray[] = $this->getTabObject(
                $tabData['name'],
                $tabData['class_name'],
                $tabData['ParentClassName'],
                isset($tabData['visible']) ? $tabData['visible'] : false
            );
        }

        return new TabsCollection($tabsObjectsArray);
    }

    /**
     * @param $tabName
     * @param $tabClassName
     * @param $tabParentClassName
     *
     * @param bool $isActive
     * @return Tab
     */
    private function getTabObject($tabName, $tabClassName, $tabParentClassName, $isActive = false)
    {
        return new Tab($tabName, $tabClassName, $tabParentClassName, $isActive);
    }
}
