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
